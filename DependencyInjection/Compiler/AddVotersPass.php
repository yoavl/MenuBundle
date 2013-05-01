<?php

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * A compiler pass to find current menu item voters and add them to the content
 * aware factory.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class AddVotersPass implements CompilerPassInterface
{
    /**
     * Adds any tagged current item voters to the content aware factory
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('symfony_cmf_menu.factory')) {
            return;
        }

        $factory = $container->getDefinition('symfony_cmf_menu.factory');
        $listener = $container->getDefinition('symfony_cmf_menu.listener.voters_request');

        $voterServices = $container->findTaggedServiceIds('symfony_cmf_menu.voter');
        foreach ($voterServices as $id => $attributes) {
            $factory->addMethodCall('addCurrentItemVoter', array(new Reference($id)));

            foreach ($attributes as $attribute) {
                if (isset($attribute['request']) && $attribute['request']) {
                    $listener->addMethodCall('addVoter', array(new Reference($id)));
                }
            }
        }
    }
}