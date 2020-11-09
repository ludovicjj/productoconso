<?php

namespace App\HandlerFactory;

/**
 * Interface HandlerFactoryInterface
 * @package App\HandlerFactory
 */
interface HandlerFactoryInterface
{
    public function createHandler(string $handler): HandlerInterface;
}
