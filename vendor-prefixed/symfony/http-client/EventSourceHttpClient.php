<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace R2WpBaziPlugin\vendor\Symfony\Component\HttpClient;

use R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Chunk\DataChunk;
use R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Chunk\ServerSentEvent;
use R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Exception\EventSourceException;
use R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Response\AsyncContext;
use R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Response\AsyncResponse;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\ChunkInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\Service\ResetInterface;

/**
 * @author Antoine Bluchet <soyuka@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class EventSourceHttpClient implements HttpClientInterface, ResetInterface
{
    use AsyncDecoratorTrait, HttpClientTrait {
        AsyncDecoratorTrait::withOptions insteadof HttpClientTrait;
    }

    private float $reconnectionTime;

    public function __construct(?HttpClientInterface $client = null, float $reconnectionTime = 10.0)
    {
        $this->client = $client ?? HttpClient::create();
        $this->reconnectionTime = $reconnectionTime;
    }

    public function connect(string $url, array $options = [], string $method = 'GET'): ResponseInterface
    {
        return $this->request($method, $url, self::mergeDefaultOptions($options, [
            'buffer' => false,
            'headers' => [
                'Accept' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
            ],
        ], true));
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $state = new class() {
            public ?string $buffer = null;
            public ?string $lastEventId = null;
            public float $reconnectionTime;
            public ?float $lastError = null;
        };
        $state->reconnectionTime = $this->reconnectionTime;

        if ($accept = self::normalizeHeaders($options['headers'] ?? [])['accept'] ?? []) {
            $state->buffer = \in_array($accept, [['Accept: text/event-stream'], ['accept: text/event-stream']], true) ? '' : null;

            if (null !== $state->buffer) {
                $options['extra']['trace_content'] = false;
            }
        }

        return new AsyncResponse($this->client, $method, $url, $options, static function (ChunkInterface $chunk, AsyncContext $context) use ($state, $method, $url, $options) {
            if (null !== $state->buffer) {
                $context->setInfo('reconnection_time', $state->reconnectionTime);
                $isTimeout = false;
            }
            $lastError = $state->lastError;
            $state->lastError = null;

            try {
                $isTimeout = $chunk->isTimeout();

                if (null !== $chunk->getInformationalStatus() || $context->getInfo('canceled')) {
                    yield $chunk;

                    return;
                }
            } catch (TransportExceptionInterface) {
                $state->lastError = $lastError ?? hrtime(true) / 1E9;

                if (null === $state->buffer || ($isTimeout && hrtime(true) / 1E9 - $state->lastError < $state->reconnectionTime)) {
                    yield $chunk;
                } else {
                    $options['headers']['Last-Event-ID'] = $state->lastEventId;
                    $state->buffer = '';
                    $state->lastError = hrtime(true) / 1E9;
                    $context->getResponse()->cancel();
                    $context->replaceRequest($method, $url, $options);
                    if ($isTimeout) {
                        yield $chunk;
                    } else {
                        $context->pause($state->reconnectionTime);
                    }
                }

                return;
            }

            if ($chunk->isFirst()) {
                if (preg_match('/^text\/event-stream(;|$)/i', $context->getHeaders()['content-type'][0] ?? '')) {
                    $state->buffer = '';
                } elseif (null !== $lastError || (null !== $state->buffer && 200 === $context->getStatusCode())) {
                    throw new EventSourceException(sprintf('Response content-type is "%s" while "text/event-stream" was expected for "%s".', $context->getHeaders()['content-type'][0] ?? '', $context->getInfo('url')));
                } else {
                    $context->passthru();
                }

                if (null === $lastError) {
                    yield $chunk;
                }

                return;
            }

            if ($chunk->isLast()) {
                if ('' !== $content = $state->buffer) {
                    $state->buffer = '';
                    yield new DataChunk(-1, $content);
                }

                yield $chunk;

                return;
            }

            $content = $state->buffer.$chunk->getContent();
            $events = preg_split('/((?:\r\n){2,}|\r{2,}|\n{2,})/', $content, -1, \PREG_SPLIT_DELIM_CAPTURE);
            $state->buffer = array_pop($events);

            for ($i = 0; isset($events[$i]); $i += 2) {
                $content = $events[$i].$events[1 + $i];
                if (!preg_match('/(?:^|\r\n|[\r\n])[^:\r\n]/', $content)) {
                    yield new DataChunk(-1, $content);

                    continue;
                }

                $event = new ServerSentEvent($content);

                if ('' !== $event->getId()) {
                    $context->setInfo('last_event_id', $state->lastEventId = $event->getId());
                }

                if ($event->getRetry()) {
                    $context->setInfo('reconnection_time', $state->reconnectionTime = $event->getRetry());
                }

                yield $event;
            }
        });
    }
}
