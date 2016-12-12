<?php
namespace Concrete\Core\Page\Type\PublishTarget\Type;

use Loader;
use Concrete\Core\Page\Type\Type as PageType;
use \Concrete\Core\Page\Type\PublishTarget\Configuration\PageTypeConfiguration;
use Symfony\Component\HttpFoundation\Request;

class PageTypeType extends Type
{


    public function configurePageTypePublishTarget(PageType $pt, $post)
    {
        $configuration = new PageTypeConfiguration($this);
        $configuration->setPageTypeID($post['ptID']);
        $configuration->setSelectorFormFactor($post['selectorFormFactorPageType']);
        $configuration->setStartingPointPageID(isset($post['startingPointPageIDPageType']) ? $post['startingPointPageIDPageType'] : null);
        return $configuration;
    }

    public function configurePageTypePublishTargetFromImport($txml)
    {
        $configuration = new PageTypeConfiguration($this);
        $ct = PageType::getByHandle((string)$txml['pagetype']);
        $formFactor = (string) $txml['form-factor'];
        $configuration->setPageTypeID($ct->getPageTypeID());
        if ($formFactor) {
            $configuration->setSelectorFormFactor($formFactor);
        }
        $path = (string) $txml['path'];
        if ($path) {
            $c = \Page::getByPath($path);
            $configuration->setStartingPointPageID($c->getCollectionID());
        }
        return $configuration;
    }

    public function validatePageTypeRequest(Request $request)
    {
        $e = parent::validatePageTypeRequest($request);
        $type = PageType::getByID($request->request->get('ptID'));
        if (!($type instanceof PageType)) {
            $e->add(t('You must choose the type of page these page types are published beneath.'));
        }
        return $e;
    }


}