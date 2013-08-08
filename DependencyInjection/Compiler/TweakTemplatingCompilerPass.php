<?php

namespace Mremi\TemplatingExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Tweak templating definition
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class TweakTemplatingCompilerPass implements CompilerPassInterface
{
    const TEMPLATING_ID = 'templating';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mremi_templating_extra.templating_proxy')) {
            return;
        }

        if (!$container->hasAlias(self::TEMPLATING_ID)) {
            return;
        }

        $definition = $container->getDefinition('mremi_templating_extra.templating_proxy');
        $definition->replaceArgument(0, new Reference($container->getAlias(self::TEMPLATING_ID)));

        $container->setAlias(self::TEMPLATING_ID, 'mremi_templating_extra.templating_proxy');
    }
}
