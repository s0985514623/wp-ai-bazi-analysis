<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\AssistantsContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Assistants;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Assistants\AssistantDeleteResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Assistants\AssistantListResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Assistants\AssistantResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class AssistantsTestResource implements AssistantsContract {

	use Testable;

	public function resource(): string {
		return Assistants::class;
	}

	public function create( array $parameters ): AssistantResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function retrieve( string $id ): AssistantResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function modify( string $id, array $parameters ): AssistantResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function delete( string $id ): AssistantDeleteResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function list( array $parameters = [] ): AssistantListResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}
}
