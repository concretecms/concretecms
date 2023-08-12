<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Multilingual\Service\Detector;
use Concrete\Core\Page\Page;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class SwitchLanguage extends AbstractController
{
    /**
     * Redirect to the translated page.
     *
     * @param $currentPageID
     * @param $targetSectionID
     * @return Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function switchLanguage($currentPageID, $targetSectionID)
    {
        /** @var ResponseFactoryInterface $factory */
        $factory = $this->app->make(ResponseFactoryInterface::class);
        $currentPage = Page::getByID($currentPageID);
        $targetSection = Section::getByID($targetSectionID);
        if ($currentPage && $targetSection) {
            if ($currentPage->isGeneratedCollection()) {
                // If the current page is a single page, set the session language,
                // then redirect back to the original page.
                /** @var Session $session */
                $session = $this->app->make('session');
                $session->set('multilingual_default_locale', $targetSection->getLocale());
                $translatedPage = $currentPage;
            } else {
                /** @var Detector $detector */
                $detector = $this->app->make('multilingual/detector');
                $translatedPage = $detector->getRelatedPage($currentPage, $targetSection);
            }
            return $factory->redirect($translatedPage->getCollectionLink(), Response::HTTP_FOUND);
        }

        return $factory->error(t('Invalid Request.'), Response::HTTP_BAD_REQUEST);
    }
}