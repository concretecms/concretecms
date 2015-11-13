<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Config;

class Urls extends DashboardPageController
{
    /**
     * Returns the mod_rewrite rules.
     *
     * @return string
     */
    public function getRewriteRules()
    {
        return Core::make('helper/url/pretty')->getRewriteRules();
    }

    /**
     * Returns the .htaccess text to be copied/inserted.
     *
     * @return string
     */
    public function getHtaccessText()
    {
        return Core::make('helper/url/pretty')->getHtaccessText();
    }

    /**
     * Dashboard page view.
     *
     * @param string|bool $strStatus - Result of attempting to update rewrite rules
     * @param bool $blnHtu - Flag denoting if the .htaccess file was writable or not
     */
    public function view($strStatus = false, $blnHtu = false)
    {
        $strStatus = (string) $strStatus;
        $blnHtu = (bool) $blnHtu;
        $intRewriting = Config::get('concrete.seo.url_rewriting') == 1 ? 1 : 0;

        $this->set('fh', \Core::make('helper/form'));
        $this->set('strRules', $this->getRewriteRules());
        $this->set('intRewriting', $intRewriting);
        $this->set('canonical_url', Config::get('concrete.seo.canonical_url'));
        $this->set('canonical_ssl_url', Config::get('concrete.seo.canonical_ssl_url'));
        $this->set('redirect_to_canonical_url', Config::get('concrete.seo.redirect_to_canonical_url'));

        if ($strStatus == 'saved') {
            $message = t('Settings Saved.');
            if (Config::get('concrete.seo.url_rewriting') && !$blnHtu) {
                $this->set('message', $message . ' ' . $urlmsg . ' ' . t('You need to update .htaccess by hand.'));
            } elseif (Config::get('concrete.seo.url_rewriting') && $blnHtu) {
                $this->set('message', $message . ' ' . $urlmsg . ' ' .t('We were able to automatically update .htaccess file.'));
            } else {
                $this->set('message', $message);
            }
        }
    }

    /**
     * Updates the .htaccess file (if writable).
     */
    public function save_urls()
    {
        if (!$this->token->validate('save_urls')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($this->post('canonical_url') && strpos(strtolower($this->post('canonical_url')), 'http://') !== 0) {
            $this->error->add(t('The canonical URL provided must start with "http://".'));
        }

        if ($this->post('canonical_ssl_url') && strpos(strtolower($this->post('canonical_ssl_url')), 'https://') !== 0) {
            $this->error->add(t('The SSL canonical URL provided must start with "https://".'));
        }

        if (!$this->error->has()) {
            $strHtText = (string) $this->getHtaccessText();

            if ($this->isPost()) {
                Config::save('concrete.seo.canonical_url', $this->post('canonical_url'));
                Config::save('concrete.seo.canonical_ssl_url', $this->post('canonical_ssl_url'));
                Config::save('concrete.seo.redirect_to_canonical_url', $this->post('redirect_to_canonical_url') ? 1 : 0);

                $urlRewriting = (bool) $this->post('URL_REWRITING');
                Config::save('concrete.seo.url_rewriting', $urlRewriting);
                $blnHtu = Core::make('helper/url/pretty')->updateHtaccessContents($urlRewriting);

                $this->redirect('/dashboard/system/seo/urls', 'saved', $blnHtu ? 1 : 0);
            }
        }
        $this->view();
    }
}
