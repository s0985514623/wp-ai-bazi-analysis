<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Responses;

use Generator;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseHasMetaInformationContract;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseStreamContract;
use R2WpBaziPlugin\vendor\OpenAI\Exceptions\ErrorException;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Meta\MetaInformation;
use R2WpBaziPlugin\vendor\Psr\Http\Message\ResponseInterface;
use R2WpBaziPlugin\vendor\Psr\Http\Message\StreamInterface;

/**
 * @template TResponse
 *
 * @implements ResponseStreamContract<TResponse>
 */
final class StreamResponse implements ResponseHasMetaInformationContract, ResponseStreamContract
{
    /**
     * Creates a new Stream Response instance.
     *
     * @param  class-string<TResponse>  $responseClass
     */
    public function __construct(
        private readonly string $responseClass,
        private readonly ResponseInterface $response,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Generator
    {
        while (! $this->response->getBody()->eof()) {
            $line = $this->readLine($this->response->getBody());

            $event = null;
            if (str_starts_with($line, 'event:')) {
                $event = trim(substr($line, strlen('event:')));
                $line = $this->readLine($this->response->getBody());
            }

            if (! str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, strlen('data:')));

            if ($data === '[DONE]') {
                break;
            }

            /** @var array{error?: array{message: string|array<int, string>, type: string, code: string}} $response */
            $response = json_decode($data, true, flags: JSON_THROW_ON_ERROR);

            if (isset($response['error'])) {
                throw new ErrorException($response['error'], $this->response->getStatusCode());
            }

            if ($event !== null) {
                $response['__event'] = $event;
                $response['__meta'] = $this->meta();
            }

            yield $this->responseClass::from($response);
        }
    }

    /**
     * Read a line from the stream.
     */
    private function readLine(StreamInterface $stream): string
    {
        $buffer = '';

        while (! $stream->eof()) {
            if ('' === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }

    public function meta(): MetaInformation
    {
        // @phpstan-ignore-next-line
        return MetaInformation::from($this->response->getHeaders());
    }
}
