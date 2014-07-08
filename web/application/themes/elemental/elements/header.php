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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
            var msViewportStyle = document.createElement('style')
            msViewportStyle.appendChild(
                document.createTextNode(
                    '@-ms-viewport{width:auto!important}'
                )
            )
            document.querySelector('head').appendChild(msViewportStyle)
        }
    </script>
</head>
<body>
<?
    $as = new GlobalArea('Header Search');
    $blocks = $as->getTotalBlocksInArea();
    $displayThirdColumn = $blocks > 0 || $c->isEditMode();
?>
<div class="ccm-page ccm-elemental">
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