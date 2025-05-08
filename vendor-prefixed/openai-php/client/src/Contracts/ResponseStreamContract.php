<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Contracts;

use IteratorAggregate;

/**
 * @template T
 *
 * @extends IteratorAggregate<int, T>
 *
 * @internal
 */
interface ResponseStreamContract extends IteratorAggregate {}
