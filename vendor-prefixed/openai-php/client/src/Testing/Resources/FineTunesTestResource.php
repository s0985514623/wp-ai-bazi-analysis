<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\FineTunesContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\FineTunes;
use R2WpBaziPlugin\vendor\OpenAI\Responses\FineTunes\ListEventsResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\FineTunes\ListResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\FineTunes\RetrieveResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\StreamResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class FineTunesTestResource implements FineTunesContract
{
    use Testable;

    protected function resource(): string
    {
        return FineTunes::class;
    }

    public function create(array $parameters): RetrieveResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function list(): ListResponse
    {
        return $this->record(__FUNCTION__);
    }

    public function retrieve(string $fineTuneId): RetrieveResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function cancel(string $fineTuneId): RetrieveResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function listEvents(string $fineTuneId): ListEventsResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function listEventsStreamed(string $fineTuneId): StreamResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
