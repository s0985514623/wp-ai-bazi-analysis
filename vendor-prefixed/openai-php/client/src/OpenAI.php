<?php

declare(strict_types=1);

use R2WpBaziPlugin\vendor\OpenAI\Client;
use R2WpBaziPlugin\vendor\OpenAI\Factory;

final class R2WpBaziPlugin_vendor_OpenAI
{
    /**
     * Creates a new Open AI Client with the given API token.
     */
    public static function client(string $apiKey, ?string $organization = null, ?string $project = null): Client
    {
        return self::factory()
            ->withApiKey($apiKey)
            ->withOrganization($organization)
            ->withProject($project)
            ->withHttpHeader('R2WpBaziPlugin_vendor_OpenAI-Beta', 'assistants=v2')
            ->make();
    }

    /**
     * Creates a new factory instance to configure a custom Open AI Client
     */
    public static function factory(): Factory
    {
        return new Factory;
    }
}
