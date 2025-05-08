<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\CompletionsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Completions;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Completions\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\StreamResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class CompletionsTestResource implements CompletionsContract {

	use Testable;

	protected function resource(): string {
		return Completions::class;
	}

	public function create( array $parameters ): CreateResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function createStreamed( array $parameters ): StreamResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}
}
