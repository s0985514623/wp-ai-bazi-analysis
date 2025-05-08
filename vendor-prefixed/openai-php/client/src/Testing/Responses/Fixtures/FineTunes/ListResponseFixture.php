<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Fixtures\FineTunes;

final class ListResponseFixture
{
    public const ATTRIBUTES = [
        'object' => 'list',
        'data' => [
            RetrieveResponseFixture::ATTRIBUTES,
        ],
    ];
}
