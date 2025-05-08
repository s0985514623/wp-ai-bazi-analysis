<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ChatContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Chat;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Chat\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\StreamResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class ChatTestResource implements ChatContract
{
    use Testable;

    protected function resource(): string
    {
        return Chat::class;
    }

    public function create(array $parameters): CreateResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function createStreamed(array $parameters): StreamResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
