<?php

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

$app = Application::getFacadeApplication();
$_c = Page::getCurrentPage();
$valt = $app->make('token');
$resolver = $app->make(ResolverManagerInterface::class);
if ($_c->getCollectionPath() != '/dashboard/news' && $_c->getCollectionPath() != '/dashboard/welcome' && !$_GET['_ccm_dashboard_external']) {
    ?>
    <div class="well" style="margin-bottom: 0px">
        <?php
        if ($_c->isCheckedOut()) {
            ?>
            <a href="#" id="ccm-nav-save-arrange" class="btn ccm-main-nav-arrange-option" style="display: none"><?=t('Save Positioning')?></a>
            <a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$_c->getCollectionID()?>&approve=APPROVE&ctask=check-in&<?=Loader::helper('validation/token')->getParameter()?>" id="ccm-nav-exit-edit-direct" class="btn success ccm-main-nav-edit-option"><?=t('Save Changes')?></a>
            <?php 
        }
        if (!$_c->isCheckedOut()) {
            ?><a href="<?= h($resolver->resolve(["/ccm/system/page/checkout/{$_c->getCollectionID()}/-/" . $valt->generate()])) ?>" id="ccm-nav-check-out" class="btn"><?=t('Edit Page')?></a><?php 
        }
        ?>
    </div>
    <?php 
}

$u = $app->make(Concrete\Core\User\User::class);
$u->saveConfig('NEWSFLOW_LAST_VIEWED', time());
