<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ModerationsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Moderations;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Moderations\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class ModerationsTestResource implements ModerationsContract {

	use Testable;

	protected function resource(): string {
		return Moderations::class;
	}

	public function create( array $parameters ): CreateResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}
}
