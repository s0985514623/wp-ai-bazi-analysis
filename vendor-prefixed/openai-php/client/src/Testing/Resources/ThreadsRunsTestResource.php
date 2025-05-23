<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ThreadsRunsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\ThreadsRuns;
use R2WpBaziPlugin\vendor\OpenAI\Responses\StreamResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\ThreadRunListResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class ThreadsRunsTestResource implements ThreadsRunsContract
{
    use Testable;

    public function resource(): string
    {
        return ThreadsRuns::class;
    }

    public function create(string $threadId, array $parameters): ThreadRunResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function createStreamed(string $threadId, array $parameters): StreamResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function retrieve(string $threadId, string $runId): ThreadRunResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function modify(string $threadId, string $runId, array $parameters): ThreadRunResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function submitToolOutputs(string $threadId, string $runId, array $parameters): ThreadRunResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function submitToolOutputsStreamed(string $threadId, string $runId, array $parameters): StreamResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function cancel(string $threadId, string $runId): ThreadRunResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function list(string $threadId, array $parameters = []): ThreadRunListResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function steps(): ThreadsRunsStepsTestResource
    {
        return new ThreadsRunsStepsTestResource($this->fake);
    }
}
