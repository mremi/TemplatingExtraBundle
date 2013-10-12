<?php

/*
 * This file is part of the Mremi\TemplatingExtraBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
