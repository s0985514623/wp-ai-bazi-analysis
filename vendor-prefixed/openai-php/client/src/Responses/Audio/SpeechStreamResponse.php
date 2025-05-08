<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Audio;

use Generator;
use R2WpBaziPlugin\vendor\Http\Discovery\Psr17Factory;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseHasMetaInformationContract;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseStreamContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Meta\MetaInformation;
use R2WpBaziPlugin\vendor\Psr\Http\Message\ResponseInterface;

/**
 * @implements ResponseStreamContract<string>
 */
final class SpeechStreamResponse implements ResponseHasMetaInformationContract, ResponseStreamContract
{
    public function __construct(
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
            yield $this->response->getBody()->read(1024);
        }
    }

    public function meta(): MetaInformation
    {
        // @phpstan-ignore-next-line
        return MetaInformation::from($this->response->getHeaders());
    }

    public static function fake(?string $content = null, ?MetaInformation $meta = null): static
    {
        $psr17Factory = new Psr17Factory;
        $response = $psr17Factory->createResponse()
            ->withBody($psr17Factory->createStream($content ?? (string) file_get_contents(__DIR__.'/../../Testing/Responses/Fixtures/Audio/speech-streamed.mp3')));

        if ($meta instanceof \R2WpBaziPlugin\vendor\OpenAI\Responses\Meta\MetaInformation) {
            foreach ($meta->toArray() as $key => $value) {
                $response = $response->withHeader($key, (string) $value);
            }
        }

        return new self($response);
    }
}
