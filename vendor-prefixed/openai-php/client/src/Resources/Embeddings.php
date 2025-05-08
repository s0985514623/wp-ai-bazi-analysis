<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\EmbeddingsContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Embeddings\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Payload;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Response;

final class Embeddings implements EmbeddingsContract {

	use Concerns\Transportable;

	/**
	 * Creates an embedding vector representing the input text.
	 *
	 * @see https://platform.openai.com/docs/api-reference/embeddings/create
	 *
	 * @param  array<string, mixed> $parameters
	 */
	public function create( array $parameters ): CreateResponse {
		$payload = Payload::create('embeddings', $parameters);

		/** @var Response<array{object: string, data: array<int, array{object: string, embedding: array<int, float>, index: int}>, usage: array{prompt_tokens: int, total_tokens: int}}> $response */
		$response = $this->transporter->requestObject($payload);

		return CreateResponse::from($response->data(), $response->meta());
	}
}
