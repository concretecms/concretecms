<?php defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header_top.php');
$as = new GlobalArea('Header Search');
$blocks = $as->getTotalBlocksInArea();
$displayThirdColumn = $blocks > 0 || $c->isEditMode();

?>

<header>
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-xs-6">
                <?
                $a = new GlobalArea('Header Site Title');
                $a->display();
                ?>
            </div>
            <div class="<? if ($displayThirdColumn) { ?>col-sm-5 col-xs-6<? } else { ?>col-sm-8 col-xs-6<? } ?>">
                <?
                $a = new GlobalArea('Header Navigation');
                $a->display();
                ?>
            </div>
            <? if ($displayThirdColumn) { ?>
                <div class="col-sm-3 col-xs-12"><? $as->display(); ?></div>
            <? } ?>
        </div>
    </div>
</header>