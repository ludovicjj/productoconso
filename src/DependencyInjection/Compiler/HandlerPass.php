<?php

namespace App\DependencyInjection\Compiler;

use App\HandlerFactory\HandlerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HandlerPass implements CompilerPassInterface
{
    /**
     * https://symfonycasts.com/screencast/symfony-bundle/tags-compiler-pass
     * https://symfony.com/doc/current/service_container/tags.html
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(HandlerFactory::class)) {
            return;
        }

        $definition = $container->getDefinition(HandlerFactory::class);
        $serviceMap = [];

        // find all service IDs with the app.handler tag
        $taggedServices = $container->findTaggedServiceIds("app.handler");

        foreach (array_keys($taggedServices) as $serviceId) {
            $serviceMap[$container->getDefinition($serviceId)->getClass()] = new Reference($serviceId);
        }

        $definition->setArgument(0, ServiceLocatorTagPass::register($container, $serviceMap));
    }
}
