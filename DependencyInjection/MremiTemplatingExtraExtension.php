<?php

/*
 * This file is part of the Mremi\TemplatingExtraBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\TemplatingExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MremiTemplatingExtraExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->configureProfiler($container, $loader);
    }

    /**
     * Configures the profiler service
     *
     * @param ContainerBuilder $container A container builder instance
     * @param XmlFileLoader    $loader    An XML file loader instance
     */
    private function configureProfiler(ContainerBuilder $container, XmlFileLoader $loader)
    {
        // for unit tests
        if (!$container->hasParameter('kernel.debug')) {
            return;
        }

        if (!$container->getParameter('kernel.debug')) {
            return;
        }

        $loader->load('profiler.xml');
    }
}
