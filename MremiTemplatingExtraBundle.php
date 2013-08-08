<?php

namespace Mremi\TemplatingExtraBundle;

use Mremi\TemplatingExtraBundle\DependencyInjection\Compiler\TweakTemplatingCompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * MremiTemplatingExtraBundle class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class MremiTemplatingExtraBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TweakTemplatingCompilerPass);
    }
}
