<?php

declare(strict_types=1);

namespace Dvc\ContaoContainerWrapperBundle\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class BackendAssetsListener
{
    protected $scopeMatcher;

    public function __construct(ScopeMatcher $scopeMatcher)
    {
        $this->scopeMatcher = $scopeMatcher;
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->scopeMatcher->isBackendRequest($request)) {
            return;
        }

        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/dvccontaocontainerwrapper/dependency.js|static|1';
    }
}
