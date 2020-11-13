<?php

namespace App\HandlerFactory;

use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    /**
     * @param Request $request
     * @param null|object $entity
     * @param null $data
     * @param array $options
     *
     * @return bool
     */
    public function handle(Request $request, ?object $entity = null, $data = null, array $options = []): bool;
}
