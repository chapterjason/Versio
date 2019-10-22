<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Exception;

use RuntimeException;

class InvalidVersionException extends RuntimeException
{
    /**
     * InvalidVersionException constructor.
     * @param string $version
     */
    public function __construct(string $version)
    {
        parent::__construct('Invalid version "' . $version . '".');
    }
}