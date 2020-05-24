<?php
declare(strict_types=1);

namespace Philly\Contracts\Pipeline;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface PipeOutput
 */
interface PipeOutput
{
    public function isSuccessful(): bool;
}