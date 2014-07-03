<?php defined('C5_EXECUTE') or die("Access Denied.");

$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('css', 'font-awesome');

?>
<!DOCTYPE html>
<html lang="<?=Localization::activeLanguage()?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="<?=$this->getThemePath()?>/css/bootstrap-modified.css">
    <?=$html->css($view->getStylesheet('main.less'))?>
    <?php Loader::element('header_required', array('pageTitle' => $pageTitle));?>
</head>
<body>
<?
    $as = new GlobalArea('Header Search');
    $blocks = $as->getTotalBlocksInArea();
    $displayThirdColumn = $blocks > 0 || $c->isEditMode();
?>
<div class="ccm-page">
    <header>
        <div class="container">
            <div class="col-md-4"><span id="header-brand">Elemental</span></div>
            <div class="<? if ($displayThirdColumn) { ?>col-md-5<? } else { ?>col-md-8<? } ?>">
                <?
                $a = new GlobalArea('Header Navigation');
                $a->display();
                ?>
            </div>
            <? if ($displayThirdColumn) { ?>
                <div class="col-md-3"><? $as->display(); ?></div>
            <? } ?>
        </div>
    </header>