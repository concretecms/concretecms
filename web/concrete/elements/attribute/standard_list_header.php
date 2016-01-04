<?php
defined('C5_EXECUTE') or die("Access Denied.");
if ($category->allowAttributeSets()) { ?>

    <div class="ccm-dashboard-header-buttons btn-group">

    <? if (count($sets) > 0) { ?>

        <div class="btn-group">

            <button type="button" class="btn btn-default" data-toggle="dropdown">
                <?=t('View')?> <span class="caret"></span>
            </button>

            <ul class="dropdown-menu" role="menu">
                <li><a href=""><?=t('Grouped by Set')?></a></li>
                <li><a href=""><?=t('In One List')?></a></li>
            </ul>

        </div>
    <? } ?>

        <a href="<?=URL::to('/dashboard/system/attributes/sets', 'category', $category->getAttributeKeyCategoryID())?>" class="btn btn-default"><?=t('Manage Sets')?></a>
    </div>

<? } ?>