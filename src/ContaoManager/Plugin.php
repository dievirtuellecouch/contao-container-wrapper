<?php

namespace DVC\ContainerWrapper\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use DVC\ContainerWrapper\ContainerWrapperBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContainerWrapperBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                ])
        ];
    }
}
