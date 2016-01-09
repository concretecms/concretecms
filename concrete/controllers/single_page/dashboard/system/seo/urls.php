<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Config;
class Urls extends DashboardPageController
{
    /**
    * Returns the mod_rewrite rules
    *
    * @return string
    */
    public function getRewriteRules()
    {
        $strRules = '
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase ' . DIR_REL . '/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}/index.html !-f
RewriteCond %{REQUEST_FILENAME}/index.php !-f
RewriteRule . ' . DISPATCHER_FILENAME .' [L]
</IfModule>';

        return $strRules;
    }

    /**
    * Returns the .htaccess text to be copied/inserted
    *
    * @return string
    */
    public function getHtaccessText()
    {
        $strHt = '
        # -- concrete5 urls start --'
        . $this->getRewriteRules() . '
        # -- concrete5 urls end --
        ';

        return preg_replace('/\t/', '', $strHt);
    }


    /**
    * Dashboard page view
    *
    * @param string|bool $strStatus - Result of attempting to update rewrite rules
    * @param boolean $blnHtu - Flag denoting if the .htaccess file was writable or not
    * @return void
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
    * Updates the .htaccess file (if writable)
    *
    * @return void
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
            $blnHtu = 0;

            if ($this->isPost()) {


                Config::save('concrete.seo.canonical_url', $this->post('canonical_url'));
                Config::save('concrete.seo.canonical_ssl_url', $this->post('canonical_ssl_url'));
                Config::save('concrete.seo.redirect_to_canonical_url', $this->post('redirect_to_canonical_url') ? 1 : 0);

                $intCurrent = Config::get('concrete.seo.url_rewriting') == 1 ? 1 : 0;
                $intPosted = $this->post('URL_REWRITING') == 1 ? 1 : 0;

                // If there was no change we don't attempt to edit/create the .htaccess file
                if ($intCurrent == $intPosted) {
                    $this->redirect('/dashboard/system/seo/urls', 'saved');
                }

                Config::save('concrete.seo.url_rewriting', $intPosted);

                if ($this->post('URL_REWRITING') == 1) {
                    if (file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE . '/.htaccess')) {
                        if (file_put_contents(DIR_BASE . '/.htaccess', $strHtText, FILE_APPEND)) {
                            $blnHtu = 1;
                        }
                    } elseif (!file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE)) {
                        if (file_put_contents(DIR_BASE . '/.htaccess', $strHtText)) {
                            $blnHtu = 1;
                        }
                    }
                } else {
                    if (file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE . '/.htaccess')) {
                        $fh = Loader::helper('file');
                        $contents = $fh->getContents(DIR_BASE . '/.htaccess');

                        if (file_put_contents(DIR_BASE . '/.htaccess', str_replace($strHtText, '', $contents))) {
                            $blnHtu = 1;
                        }
                    }
                }

                $this->redirect('/dashboard/system/seo/urls', 'saved', $blnHtu);
            }
        }
        $this->view();
    }
}
