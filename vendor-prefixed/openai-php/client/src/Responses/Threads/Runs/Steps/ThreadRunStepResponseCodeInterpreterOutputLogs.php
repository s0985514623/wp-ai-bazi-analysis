<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{type: 'logs', logs: string}>
 */
final class ThreadRunStepResponseCodeInterpreterOutputLogs implements ResponseContract {

	/**
	 * @use ArrayAccessible<array{type: 'logs', logs: string}>
	 */
	use ArrayAccessible;

	use Fakeable;

	/**
	 * @param  'logs' $type
	 */
	private function __construct(
		public string $type,
		public string $logs,
	) {}

	/**
	 * Acts as static factory, and returns a new Response instance.
	 *
	 * @param  array{type: 'logs', logs: string} $attributes
	 */
	public static function from( array $attributes ): self {
		return new self(
			$attributes['type'],
			$attributes['logs'],
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array {
		return [
			'type' => $this->type,
			'logs' => $this->logs,
		];
	}
}
