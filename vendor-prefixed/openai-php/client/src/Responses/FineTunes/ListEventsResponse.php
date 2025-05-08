<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\FineTunes;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseHasMetaInformationContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\HasMetaInformation;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Meta\MetaInformation;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{object: string, data: array<int, array{object: string, created_at: int, level: string, message: string}>}>
 */
final class ListEventsResponse implements ResponseContract, ResponseHasMetaInformationContract {

	/**
	 * @use ArrayAccessible<array{object: string, data: array<int, array{object: string, created_at: int, level: string, message: string}>}>
	 */
	use ArrayAccessible;

	use Fakeable;
	use HasMetaInformation;

	/**
	 * @param  array<int, RetrieveResponseEvent> $data
	 */
	private function __construct(
		public readonly string $object,
		public readonly array $data,
		private readonly MetaInformation $meta,
	) {}

	/**
	 * Acts as static factory, and returns a new Response instance.
	 *
	 * @param  array{object: string, data: array<int, array{object: string, created_at: int, level: string, message: string}>} $attributes
	 */
	public static function from( array $attributes, MetaInformation $meta ): self {
		$data = array_map(
			fn ( array $result ): RetrieveResponseEvent => RetrieveResponseEvent::from(
			$result
		),
			$attributes['data']
			);

		return new self(
			$attributes['object'],
			$data,
			$meta,
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array {
		return [
			'object' => $this->object,
			'data'   => array_map(
				static fn ( RetrieveResponseEvent $response ): array => $response->toArray(),
				$this->data,
			),
		];
	}
}
