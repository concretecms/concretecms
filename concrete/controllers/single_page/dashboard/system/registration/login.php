<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Entity\Page\PagePath;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Validation\Strings;
use Doctrine\ORM\EntityManagerInterface;

class Login extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');

        $this->set('loginUrl', $config->get('concrete.paths.login'));
        $this->set('saveAction', $this->action('save'));
    }

    public function save()
    {
        if (!$this->token->validate('set-login-url')) {
            $this->error->add($this->token->getErrorMessage());

            return $this->view();
        }
        $newLoginUrl = $this->normalizeLoginUrl($this->request->request->get('loginUrl'));
        if ($newLoginUrl === '') {
            $this->error->add(t('Invalid URL of the login page'));

            return $this->view();
        }
        $config = $this->app->make('config');
        $oldLoginUrl = $config->get('concrete.paths.login');
        $loginPage = Page::getByPath($oldLoginUrl);
        if ($loginPage === null || $loginPage->isError()) {
            $this->error->addHtml(t('Failed to get the page at the URL %s', '<code>' . h($oldLoginUrl) . '</code>'));

            return $this->view();
        }
        $this->app->make(EntityManagerInterface::class)->transactional(function (EntityManagerInterface $em) use ($oldLoginUrl, $newLoginUrl, $loginPage, $config) {
            $loginPage->clearPagePaths();
            $newPagePath = new PagePath();
            $newPagePath->setPagePath($newLoginUrl);
            $newPagePath->setPageObject($loginPage);
            $newPagePath->setPagePathIsCanonical(true);
            $em->persist($newPagePath);
            $em->flush();
            $pageThemes = $config->get('app.theme_paths');
            $loginPageTheme = array_get($pageThemes, $oldLoginUrl);
            unset($pageThemes[$oldLoginUrl]);
            if ($loginPageTheme !== null) {
                $pageThemes[$newLoginUrl] = $loginPageTheme;
            }
            $config->save('app.theme_paths', $pageThemes);
            $config->save('concrete.paths.login', $newLoginUrl);
        });

        $this->flash('success', t('Options successfully saved.'));

        return $this->app->make(ResponseFactoryInterface::class)->redirect(
            $this->action(''),
            302
        );
    }

    /**
     * @param mixed $url
     *
     * @return string
     */
    protected function normalizeLoginUrl($url)
    {
        if (!is_string($url)) {
            return '';
        }
        $vals = $this->app->make(Strings::class);
        $chunks = [];
        foreach (preg_split('%[/\s]+%', $url, -1, PREG_SPLIT_NO_EMPTY) as $chunk) {
            if (!$vals->handle($chunk)) {
                return '';
            }
            $chunks[] = $chunk;
        }
        if ($chunks === []) {
            return '';
        }

        return '/' . implode('/', $chunks);
    }
}
