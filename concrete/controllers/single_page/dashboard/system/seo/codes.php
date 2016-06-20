<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Page\Controller\DashboardPageController;
use Config;

class Codes extends DashboardPageController
{
    public function view()
    {
        $this->set('tracking_code_header', Config::get('concrete.seo.tracking.code.header'));
        $this->set('tracking_code_footer', Config::get('concrete.seo.tracking.code.footer'));

        if ($this->isPost()) {
            if ($this->token->validate('update_tracking_code')) {
                Config::save('concrete.seo.tracking.code.header', $this->post('tracking_code_header'));
                Config::save('concrete.seo.tracking.code.footer', $this->post('tracking_code_footer'));

                $pageCache = PageCache::getLibrary();
                if (is_object($pageCache)) {
                    $pageCache->flush();
                }
                $this->redirect('/dashboard/system/seo/codes', 'saved');
            } else {
                $this->error->add($this->token->getErrorMessage());
            }
        }
    }

    public function saved()
    {
        $this->set('message', implode(PHP_EOL, array(
            t('Tracking code settings updated successfully.'),
            t('Cached files removed.')
        )));
        $this->view();
    }
}
