<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\BatchesContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Batches;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Batches\BatchListResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Batches\BatchResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class BatchesTestResource implements BatchesContract {

	use Testable;

	public function resource(): string {
		return Batches::class;
	}

	public function create( array $parameters ): BatchResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function retrieve( string $id ): BatchResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function cancel( string $id ): BatchResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}

	public function list( array $parameters = [] ): BatchListResponse {
		return $this->record(__FUNCTION__, func_get_args());
	}
}
