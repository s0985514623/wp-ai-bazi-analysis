<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\EmbeddingsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Embeddings;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Embeddings\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class EmbeddingsTestResource implements EmbeddingsContract
{
    use Testable;

    protected function resource(): string
    {
        return Embeddings::class;
    }

    public function create(array $parameters): CreateResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
