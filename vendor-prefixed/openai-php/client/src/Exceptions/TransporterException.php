<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Exceptions;

use Exception;
use R2WpBaziPlugin\vendor\Psr\Http\Client\ClientExceptionInterface;

final class TransporterException extends Exception {

	/**
	 * Creates a new Exception instance.
	 */
	public function __construct( ClientExceptionInterface $exception ) {
		parent::__construct($exception->getMessage(), 0, $exception);
	}
}
