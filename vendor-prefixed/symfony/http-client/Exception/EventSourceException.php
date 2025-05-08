<?php

/*
* This file is part of the Symfony package.
*
* (c) Fabien Potencier <fabien@symfony.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Exception;

use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class EventSourceException extends \RuntimeException implements DecodingExceptionInterface {

}
