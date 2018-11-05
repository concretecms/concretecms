<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Utility\Service\Validation\Numbers;

class Icons extends DashboardSitePageController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::$helpers
     */
    public $helpers = ['form', 'concrete/asset_library', 'validation/token', 'form/color'];

    public function view()
    {
        $this->requireAsset('core/colorpicker');
        $config = $this->getSite()->getConfigRepository();

        $fid = (int) $config->get('misc.favicon_fid');
        $this->set('favicon', $fid === 0 ? null : $this->entityManager->find(FileEntity::class, $fid));

        $fid = (int) $config->get('misc.iphone_home_screen_thumbnail_fid');
        $this->set('iosHome', $fid === 0 ? null : $this->entityManager->find(FileEntity::class, $fid));

        $fid = (int) $config->get('misc.modern_tile_thumbnail_fid');
        $this->set('modernThumb', $fid === 0 ? null : $this->entityManager->find(FileEntity::class, $fid));

        $this->set('modernThumbBG', (string) $config->get('misc.modern_tile_thumbnail_bgcolor'));

        $this->set('browserToolbarColor', (string) $config->get('misc.browser_toolbar_color'));
    }

    public function update_icons()
    {
        $config = $this->getSite()->getConfigRepository();
        if ($this->token->validate('update_icons')) {
            $post = $this->request->request;

            $valn = $this->app->make(Numbers::class);
            $security = $this->app->make('helper/security');

            $fid = $post->get('faviconFID');
            $config->save('misc.favicon_fid', $valn->integer($fid, 1) ? (int) $fid : null);

            $fid = $post->get('iosHomeFID');
            $config->save('misc.iphone_home_screen_thumbnail_fid', $valn->integer($fid, 1) ? (int) $fid : null);

            $fid = $post->get('modernThumbFID');
            $config->save('misc.modern_tile_thumbnail_fid', $valn->integer($fid, 1) ? (int) $fid : null);

            $s = $security->sanitizeString($post->get('modernThumbBG'));
            $config->save('misc.modern_tile_thumbnail_bgcolor', $s === '' ? null : $s);

            $s = $security->sanitizeString($post->get('browserToolbarColor'));
            $config->save('misc.browser_toolbar_color', $s === '' ? null : $s);
            
            $this->flash('success', t('Icons updated successfully.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(), 302);
        } else {
            $this->set('error', [$this->token->getErrorMessage()]);
        }
    }
}
