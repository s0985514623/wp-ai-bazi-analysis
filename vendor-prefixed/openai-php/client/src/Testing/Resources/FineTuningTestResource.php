<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\FineTuningContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\FineTuning;
use R2WpBaziPlugin\vendor\OpenAI\Responses\FineTuning\ListJobEventsResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\FineTuning\ListJobsResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\FineTuning\RetrieveJobResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class FineTuningTestResource implements FineTuningContract
{
    use Testable;

    protected function resource(): string
    {
        return FineTuning::class;
    }

    public function createJob(array $parameters): RetrieveJobResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function listJobs(array $parameters = []): ListJobsResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function retrieveJob(string $jobId): RetrieveJobResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function cancelJob(string $jobId): RetrieveJobResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function listJobEvents(string $jobId, array $parameters = []): ListJobEventsResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
