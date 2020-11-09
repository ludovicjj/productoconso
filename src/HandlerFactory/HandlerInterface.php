<?php

namespace App\HandlerFactory;

use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    /**
     * @param Request $request
     * @param object $entity
     * @param null $data
     * @param array $options
     *
     * @return bool
     */
    public function handle(Request $request, object $entity, $data = null, array $options = []): bool;
}
