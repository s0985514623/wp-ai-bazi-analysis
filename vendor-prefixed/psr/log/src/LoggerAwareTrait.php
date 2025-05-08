<?php

namespace R2WpBaziPlugin\vendor\Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait {

	/**
	 * The logger instance.
	 */
	protected ?LoggerInterface $logger = null;

	/**
	 * Sets a logger.
	 */
	final public function setLogger( LoggerInterface $logger ): void {
		$this->logger = $logger;
	}
}
