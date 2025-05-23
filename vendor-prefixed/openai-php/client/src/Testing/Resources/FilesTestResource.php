<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\FilesContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Files;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Files\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Files\DeleteResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Files\ListResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Files\RetrieveResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class FilesTestResource implements FilesContract
{
    use Testable;

    protected function resource(): string
    {
        return Files::class;
    }

    public function list(): ListResponse
    {
        return $this->record(__FUNCTION__);
    }

    public function retrieve(string $file): RetrieveResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function download(string $file): string
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function upload(array $parameters): CreateResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function delete(string $file): DeleteResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
