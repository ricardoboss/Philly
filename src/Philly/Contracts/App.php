<?php
declare(strict_types=1);

namespace Philly\Contracts;

use Philly\Contracts\Container\BindingContainer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface App
 */
interface App extends BindingContainer
{
    /**
     * Handles a request in the app and returns a response.
     */
    public function handle(Request $request): JsonResponse;
}
