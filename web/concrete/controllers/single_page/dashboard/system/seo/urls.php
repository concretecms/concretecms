<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Core;
use Concrete\Core\Page\Controller\DashboardPageController;
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
        $strRules = '
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase ' . DIR_REL . '/

  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME}/index.html !-f
  RewriteCond %{REQUEST_FILENAME}/index.php !-f
  RewriteRule . ' . DISPATCHER_FILENAME .' [L]

  RewriteRule ^cli(\.bat)?$ ' . DISPATCHER_FILENAME .' [L]
</IfModule>
';

        return trim($strRules);
    }

    /**
     * Returns the .htaccess text to be copied/inserted.
     *
     * @return string
     */
    public function getHtaccessText()
    {
        $strHt = '
# -- concrete5 urls start --
'. $this->getRewriteRules() . '
# -- concrete5 urls end --
';

        return trim($strHt);
    }

    /**
     * Dashboard page view.
     *
     * @param string|bool $strStatus - Result of attempting to update rewrite rules
     * @param bool        $blnHtu    - Flag denoting if the .htaccess file was writable or not
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
            if ($blnHtu !== false) {
                $message .= $blnHtu ? t('We were able to automatically update .htaccess file.') : t('You need to update .htaccess by hand.');
            }
            $this->set('message', $message);
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

        if (!$this->error->has()) {
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
                $blnHtu = 0;
                $htAccessFile = DIR_BASE . '/.htaccess';
                $fh = Core::make('helper/file');
                /* @var $fh \Concrete\Core\File\Service\File */
                if ($intPosted == 1) {
                    if (is_file($htAccessFile)) {
                        if (is_writable($htAccessFile)) {
                            $contents = $fh->getContents($htAccessFile);
                            if ($contents !== false) {
                                $contents = rtrim($contents);
                                if ($contents === '') {
                                    $contents = $this->getHtaccessText() . "\n";
                                } else {
                                    $contents .= "\n\n" . $this->getHtaccessText() . "\n";
                                }
                                if (file_put_contents($htAccessFile, $contents)) {
                                    $blnHtu = 1;
                                }
                            }
                        }
                    } elseif (!file_exists($htAccessFile) && is_writable(DIR_BASE)) {
                        if (file_put_contents($htAccessFile, $this->getHtaccessText() . "\n")) {
                            $blnHtu = 1;
                        }
                    }
                } else {
                    if (is_file($htAccessFile) && is_writable($htAccessFile)) {
                        $contents = $fh->getContents($htAccessFile);
                        if ($contents !== false) {
                            $rx = '';
                            // Contents before
                            $rx .= '(^(?:';
                            $rx .= '\s*'; // before we have nothing (or at most some space/empty line)
                            $rx .= '|.*[\r\n]+'; // or we have something followed by a new line
                            $rx .= ')?)';
                            // Part to remove
                            $rx .= '[ \t]*# -- concrete5 urls start --.*?# -- concrete5 urls end --[ \t]*';
                            // Contents after
                            $rx .= '((?:';
                            $rx .= '\s*'; // after we have nothing (or at most some space/empty line)
                            $rx .= '|[\r\n]+.*'; // or we have something after a new line
                            $rx .= ')$)';
                            $match = null;
                            if (preg_match('/'.$rx.'/s', $contents, $match)) {
                                $pre = rtrim($match[1]);
                                $post = ltrim($match[2]);
                                if ($pre === '' || $post === '') {
                                    $contents = rtrim($pre.$post)."\n";
                                } else {
                                    $contents = $pre."\n\n".$post;
                                }
                                if (file_put_contents($htAccessFile, $contents)) {
                                    $blnHtu = 1;
                                }
                            }
                        }
                    }
                }

                $this->redirect('/dashboard/system/seo/urls', 'saved', $blnHtu);
            }
        }
        $this->view();
    }
}
