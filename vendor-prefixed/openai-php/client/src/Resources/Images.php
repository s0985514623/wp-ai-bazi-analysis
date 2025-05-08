<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ImagesContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\EditResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\VariationResponse;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Payload;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Response;

final class Images implements ImagesContract {

	use Concerns\Transportable;

	/**
	 * Creates an image given a prompt.
	 *
	 * @see https://platform.openai.com/docs/api-reference/images/create
	 *
	 * @param  array<string, mixed> $parameters
	 */
	public function create( array $parameters ): CreateResponse {
		$payload = Payload::create('images/generations', $parameters);

		/** @var Response<array{created: int, data: array<int, array{url?: string, b64_json?: string, revised_prompt?: string}>}> $response */
		$response = $this->transporter->requestObject($payload);

		return CreateResponse::from($response->data(), $response->meta());
	}

	/**
	 * Creates an edited or extended image given an original image and a prompt.
	 *
	 * @see https://platform.openai.com/docs/api-reference/images/create-edit
	 *
	 * @param  array<string, mixed> $parameters
	 */
	public function edit( array $parameters ): EditResponse {
		$payload = Payload::upload('images/edits', $parameters);

		/** @var Response<array{created: int, data: array<int, array{url?: string, b64_json?: string}>}> $response */
		$response = $this->transporter->requestObject($payload);

		return EditResponse::from($response->data(), $response->meta());
	}

	/**
	 * Creates a variation of a given image.
	 *
	 * @see https://platform.openai.com/docs/api-reference/images/create-variation
	 *
	 * @param  array<string, mixed> $parameters
	 */
	public function variation( array $parameters ): VariationResponse {
		$payload = Payload::upload('images/variations', $parameters);

		/** @var Response<array{created: int, data: array<int, array{url?: string, b64_json?: string}>}> $response */
		$response = $this->transporter->requestObject($payload);

		return VariationResponse::from($response->data(), $response->meta());
	}
}
