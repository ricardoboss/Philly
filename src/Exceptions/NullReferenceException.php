<?php

declare(strict_types=1);

namespace Philly\Exceptions;

use Throwable;
use TypeError;

/**
 * Class NullReferenceException.
 */
class NullReferenceException extends TypeError
{
    protected ?string $var_name;

    public function __construct($message = null, $code = 0, Throwable $previous = null, ?string $var_name = null)
    {
        if ($message === null && $var_name !== null) {
            $message = "Variable \$$var_name was null.";
        }

        parent::__construct($message, $code, $previous);

        $this->var_name = $var_name;
    }

    /**
     * The variable name being referenced.
     */
    public function getVarName(): ?string
    {
        return $this->var_name;
    }
}
