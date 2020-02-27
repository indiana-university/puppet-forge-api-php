<?php

/**
 * This file contains the name validation trait
 * @license BSD-3-Clause
 */

namespace Edu\Iu\Uits\Webtech\ForgeApi\Traits;

use Edu\Iu\Uits\Webtech\ForgeApi\Exception\InvalidNameException;

/**
 * Trait NameValidationTrait
 * @package Edu\Iu\Uits\Webtech\ForgeApi\Traits
 */
trait NameValidationTrait
{
    /**
     * Check to see if the current name of this instance in a valid name
     * @return void
     * @throws InvalidNameException
     */
    private function throwExceptionIfNameInvalid(): void
    {
        $name = $this->name;
        if (is_null($name)) {
            $name = '*null*';
        }
        if (
            !preg_match(
                '/' . self::VALID_NAME . '/',
                $name
            )
        ) {
            throw new InvalidNameException(
                'The given name (' . $name . ') is not valid for the specified action'
            );
        }
    }
}
