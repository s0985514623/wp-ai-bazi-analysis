<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{input?: string, outputs?: array<int, array{type: 'image', image: array{file_id: string}}|array{type: 'logs', logs: string}>}>
 */
final class ThreadRunStepResponseCodeInterpreter implements ResponseContract {

	/**
	 * @use ArrayAccessible<array{input?: string, outputs?: array<int, array{type: 'image', image: array{file_id: string}}|array{type: 'logs', logs: string}>}>
	 */
	use ArrayAccessible;

	use Fakeable;

	/**
	 * @param  \R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps\ThreadRunStepResponseCodeInterpreterOutputLogs[]|\R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps\ThreadRunStepResponseCodeInterpreterOutputImage[]|null $outputs
	 */
	private function __construct(
		public ?string $input,
		public ?array $outputs,
	) {}

	/**
	 * Acts as static factory, and returns a new Response instance.
	 *
	 * @param  array{input?: string, outputs?: array<int, array{type: 'image', image: array{file_id: string}}|array{type: 'logs', logs: string}>} $attributes
	 */
	public static function from( array $attributes ): self {
		$outputs = array_map(
			fn ( array $output ): \R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps\ThreadRunStepResponseCodeInterpreterOutputImage|\R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps\ThreadRunStepResponseCodeInterpreterOutputLogs => match ($output['type']) {
				'image' => ThreadRunStepResponseCodeInterpreterOutputImage::from($output),
				'logs' => ThreadRunStepResponseCodeInterpreterOutputLogs::from($output),
			},
			$attributes['outputs'] ?? [],
		);

		return new self(
			$attributes['input'] ?? null,
			$outputs,
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array {
		$response = [];

		if (! is_null($this->input)) {
			$response['input'] = $this->input;
		}

		if ($this->outputs) {
			$response['outputs'] = array_map(
				fn ( ThreadRunStepResponseCodeInterpreterOutputImage|ThreadRunStepResponseCodeInterpreterOutputLogs $output ): array => $output->toArray(),
				$this->outputs,
			);
		}

		return $response;
	}
}
