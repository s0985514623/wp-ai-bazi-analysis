<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\EditsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Edits;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Edits\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class EditsTestResource implements EditsContract {

	use Testable;

	protected function resource(): string {
		return Edits::class;
	}

	public function create( array $parameters ): CreateResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}
}
