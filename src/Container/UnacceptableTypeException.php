<?php

declare(strict_types=1);

namespace Philly\Container;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

/**
 * Class UnacceptableTypeException.
 */
class UnacceptableTypeException extends InvalidArgumentException implements ContainerExceptionInterface
{
    /**
     * UnacceptableTypeException constructor.
     */
    #[Pure] public function __construct(?string $message = null, int $code = 0, Throwable $previous = null, ?string $type = null)
    {
        if ($message === null) {
            if ($type === null) {
                $message = "Cannot accept objects of type '$type'";
            } else {
                $message = "Cannot accept object type.";
            }
        }

        parent::__construct($message, $code, $previous);
    }
}
