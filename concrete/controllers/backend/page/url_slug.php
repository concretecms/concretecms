<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Multilingual\Service\Detector;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class UrlSlug extends AbstractController
{
    public function view(): Response
    {
        $this->checkCSRF();
        $urlSlug = $this->urlify($this->getRequestedName(), $this->getUrlSlugMaxLength(), $this->getLanguageCode());

        return $this->buildResponse($urlSlug);
    }

    protected function checkCSRF(): void
    {
        $valt = $this->app->make(Token::class);
        if (!$valt->validate('get_url_slug', $this->request->request->get('token', $this->request->query->get('token')))) {
            throw new UserMessageException($valt->getErrorMessage());
        }
    }

    protected function getRequestedName(): string
    {
        $name = $this->request->request->get('name', $this->request->query->get('name'));

        return is_string($name) ? $name : '';
    }

    protected function getLanguageCode(): string
    {
        $languageCode = $this->getSectionLanguageCode();

        return $languageCode !== '' ? $languageCode : $this->getDefaultLanguageCode();
    }

    protected function getSectionLanguageCode(): string
    {
        $parentID = $this->request->request->get('parentID', $this->request->query->get('parentID'));
        if (!$this->app->make(Numbers::class)->integer($parentID, 1)) {
            return '';
        }
        $parentID = (int) $parentID;
        if (!$this->app->make(Detector::class)->isEnabled()) {
            return '';
        }
        $page = Page::getByID($parentID);
        if (!$page || $page->isError()) {
            return '';
        }
        $section = Section::getBySectionOfSite($page);
        if (!$section || $section->isError()) {
            return '';
        }

        return (string) $section->getLanguage();
    }

    protected function getDefaultLanguageCode(): string
    {
        return Localization::activeLanguage();
    }

    protected function getUrlSlugMaxLength(): int
    {
        return (int) $this->app->make(Repository::class)->get('concrete.seo.segment_max_length');
    }

    protected function urlify(string $name, int $urlSlugMaxLength, string $languageCode): string
    {
        $textService = $this->app->make(Text::class);

        return $textService->urlify($name, $urlSlugMaxLength, $languageCode);
    }

    protected function buildResponse(string $urlSlug): Response
    {
        return $this->app->make(ResponseFactoryInterface::class)->create(
            $urlSlug,
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/plain; charset=' . APP_CHARSET,
            ]
        );
    }
}
