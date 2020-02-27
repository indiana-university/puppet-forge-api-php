<?php

/**
 * This class contains the InvalidNameException for the Puppet Forge REST API
 * @license BSD-3-Clause
 */

declare(strict_types=1);

namespace Edu\Iu\Uits\Webtech\ForgeApi\Exception;

use Exception;

/**
 * Class InvalidNameException
 * @package Edu\Iu\Uits\Webtech\ForgeApi\Exception
 */
class InvalidNameException extends Exception
{
    /**
     * InvalidNameException constructor.
     * @param string|null $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = null,
        int $code = 0,
        Exception $previous = null
    ) {
        if (is_null($message)) {
            $message = '';
        }
        parent::__construct($message, $code, $previous);
    }
}
