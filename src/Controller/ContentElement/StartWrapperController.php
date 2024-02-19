<?php

namespace DVC\ContainerWrapper\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(
    type: self::TYPE_CONTAINER,
    category: 'wrapper',
)]
#[AsContentElement(
    type: self::TYPE_GROUP,
    category: 'wrapper',
)]
class StartWrapperController extends AbstractContentElementController
{
    const TYPE_CONTAINER = 'container_wrapper_start';
    const TYPE_GROUP = 'group_wrapper_start';

    protected $scopeMatcher;

    public function __construct(ScopeMatcher $scopeMatcher)
    {
        $this->scopeMatcher = $scopeMatcher;
    }

    protected function getResponse(
        Template $template,
        ContentModel $model,
        Request $request,
    ): Response {
        $data = $model->dvcWrapperData;
        $data = \json_decode($data, true);

        if ($this->scopeMatcher->isBackendRequest($request)) {
            return $this->render('@Contao_ContainerWrapperBundle/be_wrapper_start.html.twig', $data ?: []);
        }

        return $this->render('@Contao_ContainerWrapperBundle/wrapper_start.html.twig', $data ?: []);
    }
}
