<?php

declare(strict_types=1);

namespace Dvc\ContaoContainerWrapperBundle;

use Dvc\ContaoContainerWrapperBundle\DependencyInjection\DvcContaoContainerWrapperExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DvcContaoContainerWrapperBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new DvcContaoContainerWrapperExtension();
    }
}
