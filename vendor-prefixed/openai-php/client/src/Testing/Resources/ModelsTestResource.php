<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ModelsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Models;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Models\DeleteResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Models\ListResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Models\RetrieveResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class ModelsTestResource implements ModelsContract
{
    use Testable;

    protected function resource(): string
    {
        return Models::class;
    }

    public function list(): ListResponse
    {
        return $this->record(__FUNCTION__);
    }

    public function retrieve(string $model): RetrieveResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function delete(string $model): DeleteResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
