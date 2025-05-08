<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Enums\Transporter;

/**
 * @internal
 */
enum ContentType: string
{
    case JSON = 'application/json';
    case MULTIPART = 'multipart/form-data';
    case TEXT_PLAIN = 'text/plain';
}
