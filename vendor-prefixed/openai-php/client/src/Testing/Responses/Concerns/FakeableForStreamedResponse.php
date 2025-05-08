<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns;

use R2WpBaziPlugin\vendor\Http\Discovery\Psr17FactoryDiscovery;
use R2WpBaziPlugin\vendor\OpenAI\Responses\StreamResponse;

trait FakeableForStreamedResponse
{
    /**
     * @param  resource  $resource
     */
    public static function fake($resource = null): StreamResponse
    {
        if ($resource === null) {
            $filename = str_replace(['R2WpBaziPlugin\vendor\OpenAI\Responses', '\\'], [__DIR__.'/../Fixtures/', '/'], static::class).'Fixture.txt';
            $resource = fopen($filename, 'r');
        }

        $stream = Psr17FactoryDiscovery::findStreamFactory()
            ->createStreamFromResource($resource);

        $response = Psr17FactoryDiscovery::findResponseFactory()
            ->createResponse()
            ->withBody($stream);

        return new StreamResponse(static::class, $response);
    }
}
