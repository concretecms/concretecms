<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$c = Page::getCurrentPage();
$page_selector = Loader::helper('form/page_selector');
?>
<ul id="ccm-autonav-tabs" class="nav nav-tabs">
    <li class="active"><a id="ccm-autonav-tab-add" href="javascript:void(0);"><?= t('Edit') ?></a></li>
    <li class=""><a id="ccm-autonav-tab-preview" href="javascript:void(0);"><?= t('Preview') ?></a></li>
</ul>

<div style="padding: 10px">


    <div style="clear: both" class="ccm-autonavPane ccm-preview-pane" id="ccm-autonavPane-preview"
         style="display: none">


    </div>
    <div class="ccm-autonavPane" id="ccm-autonavPane-add">


        <input type="hidden" name="autonavCurrentCID" value="<?= $c->getCollectionID() ?>"/>
        <input type="hidden" name="autonavPreviewPane"
               value="<?= Loader::helper('concrete/urls')->getBlockTypeToolsURL($bt) ?>/preview_pane"/>


        <div class="form-group">
            <label for="orderBy"><?= t('Page Order') ?></label>
            <select class="form-control" name="orderBy">
                <?php
                $order = $info['orderBy'];
                ?>
                <option value="display_asc" <?= $order === 'display_asc' ? 'selected' : '' ?>>
                    <?= t('in their sitemap order.') ?>
                </option>
                <option value="chrono_desc" <?= $order === 'chrono_desc' ? 'selected' : '' ?>>
                    <?= t('with the most recent first.') ?>
                </option>
                <option value="chrono_asc" <?= $order === 'chrono_asc' ? 'selected' : '' ?>>
                    <?= t('with the earliest first.') ?>
                </option>
                <option value="alpha_asc" <?= $order === 'alpha_asc' ? 'selected' : '' ?>>
                    <?= t('in alphabetical order.') ?>
                </option>
                <option value="alpha_desc" <?= $order === 'alpha_desc' ? 'selected' : '' ?>>
                    <?= t('in reverse alphabetical order.') ?>
                </option>
                <option value="display_desc" <?= $order === 'display_desc' ? 'selected' : '' ?>>
                    <?= t('in reverse sitemap order.') ?>
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="displayUnavailablePages"><?= t('Check Page Permissions') ?></label>
            <label style="font-weight:normal" class="checkbox">
                <?= $form->checkbox('displayUnavailablePages', 1, $info['displayUnavailablePages']); ?>
                <?= t('Display links that may require login.'); ?>
            </label>
        </div>

        <div class="form-group">
            <label for="displayPages"><?= t('Begin Auto Nav') ?></label>
            <select name="displayPages" onchange="toggleCustomPage(this.value);" class="form-control">
                <option value="top"<? if ($info['displayPages'] == 'top') { ?> selected<? } ?>>
                    <?= t('at the top level'); ?>
                </option>
                <option value="second_level"<? if ($info['displayPages'] == 'second_level') { ?> selected<? } ?>>
                    <?= t('at the second level') ?>
                </option>
                <option value="third_level"<? if ($info['displayPages'] == 'third_level') { ?> selected<? } ?>>
                    <?= t('at the third level') ?>
                </option>
                <option value="above"<? if ($info['displayPages'] == 'above') { ?> selected<? } ?>>
                    <?= t('at the level above') ?>
                </option>
                <option value="current"<? if ($info['displayPages'] == 'current') { ?> selected<? } ?>>
                    <?= t('at the current level') ?>
                </option>
                <option value="below"<? if ($info['displayPages'] == 'below') { ?> selected<? } ?>>
                    <?= t('At the level below') ?>
                </option>
                <option value="custom"<? if ($info['displayPages'] == 'custom') { ?> selected<? } ?>>
                    <?= t('Beneath a particular page') ?>
                </option>
            </select>
        </div>

        <div class="form-group"
             id="ccm-autonav-page-selector"<? if ($info['displayPages'] != 'custom') { ?> style="display: none"<? } ?>>
            <?= $page_selector->selectPage('displayPagesCID', $info['displayPagesCID']); ?>
        </div>

        <div class="form-group">
            <label for="displaySubPages"><?= t('Sibling Pages') ?></label>

            <select class='form-control' name="displaySubPages" onchange="toggleSubPageLevels(this.value);">
                <option value="none"<? if ($info['displaySubPages'] == 'none') { ?> selected<? } ?>>
                    <?= t('None') ?>
                </option>
                <option value="relevant"<? if ($info['displaySubPages'] == 'relevant') { ?> selected<? } ?>>
                    <?= t('Relevant sub pages.') ?>
                </option>
                <option
                    value="relevant_breadcrumb"<? if ($info['displaySubPages'] == 'relevant_breadcrumb') { ?> selected<? } ?>>
                    <?= t('Display breadcrumb trail.') ?>
                </option>
                <option value="all"<? if ($info['displaySubPages'] == 'all') { ?> selected<? } ?>>
                    <?= t('Display all.') ?>
                </option>
            </select>

        </div>

        <div class="form-group">
            <label for="displaySubPages"><?= t('Page Levels') ?></label>

            <select class="form-control" id="displaySubPageLevels"
                    name="displaySubPageLevels" <? if ($info['displaySubPages'] == 'none') { ?> disabled <? } ?>
                    onchange="toggleSubPageLevelsNum(this.value);">
                <option value="enough"<? if ($info['displaySubPageLevels'] == 'enough') { ?> selected<? } ?>>
                    <?= t('Display sub pages to current.') ?></option>
                <option
                    value="enough_plus1"<? if ($info['displaySubPageLevels'] == 'enough_plus1') { ?> selected<? } ?>>
                    <?= t('Display sub pages to current +1.') ?></option>
                <option value="all"<? if ($info['displaySubPageLevels'] == 'all') { ?> selected<? } ?>>
                    <?= t('Display all.') ?></option>
                <option value="custom"<? if ($info['displaySubPageLevels'] == 'custom') { ?> selected<? } ?>>
                    <?= t('Display a custom amount.') ?></option>
            </select>

        </div>

        <div class="form-group"
             id="divSubPageLevelsNum"<? if ($info['displaySubPageLevels'] != 'custom') { ?> style="display: none"<? } ?>>
            <input type="text" name="displaySubPageLevelsNum" value="<?= $info['displaySubPageLevelsNum'] ?>"
                   style="width: 30px; vertical-align: middle">
            &nbsp;<?= t('levels') ?>
        </div>
    </div>
</div>
