<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?=$this->getThemePath()?>/main.css" />
    <?
$view->requireAsset('css', 'bootstrap');
$view->requireAsset('css', 'font-awesome');
$view->requireAsset('javascript', 'jquery');
$view->requireAsset('javascript', 'bootstrap/alert');
$view->requireAsset('javascript', 'bootstrap/transition');
$view->addHeaderItem('<meta name="viewport" content="width=device-width, initial-scale=1">');

$showLogo = true;
if (is_object($c)) {
    $cp = new Permissions($c);
    if ($cp->canViewToolbar()) {
        $showLogo = false;
    }

     Loader::element('header_required');
} else { 
    $this->markHeaderAssetPosition();
}

$showAccount = false;
if (Config::get('concrete.user.profiles_enabled') && Core::isInstalled()) {
    $account = Page::getByPath('/account');
    if (is_object($account) && !$account->isError()) {
        $cp = new Permissions($account);
        if ($cp->canRead()) {
            $request = Request::getInstance();
            if ($request->matches('/account*')) {
                $showAccount = true;
            }
        }
    }
}
?>
</head>
<body>
<div class="ccm-ui">

<?php if ($showLogo) { ?>
<div id="ccm-toolbar">
    <ul>
        <li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC()?></span></li>
        <?php if ($showAccount) { ?>
        <li class="pull-right">
            <a href="<?=URL::to('/login', 'logout', Loader::helper('validation/token')->generate('logout'))?>" title="<?=t('Sign Out')?>"><i class="fa fa-sign-out"></i>
            <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                <?= tc('toolbar', 'Sign Out') ?>
            </span>
            </a>
        </li>
        <li class="pull-right">
            <a href="<?=URL::to('/')?>">
                <i class="fa fa-home"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                    <?=tc('toolbar', 'Return to Website') ?>
                </span>
            </a>
        </li>
        <li class="pull-right">
            <a href="<?=URL::to('/account')?>">
                <i class="fa fa-user"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                    <?=t('My Account') ?>
                </span>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>
