<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ThreadsRunsStepsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\ThreadsRunsSteps;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps\ThreadRunStepListResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps\ThreadRunStepResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

class ThreadsRunsStepsTestResource implements ThreadsRunsStepsContract {

	use Testable;

	public function resource(): string {
		return ThreadsRunsSteps::class;
	}

	public function retrieve( string $threadId, string $runId, string $stepId ): ThreadRunStepResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function list( string $threadId, string $runId, array $parameters = [] ): ThreadRunStepListResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}
}
