<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Entity\Block\BlockType\BlockType $bt */
/* @var Concrete\Core\Block\Block $b */
/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Block\Search\Controller $controller */
/* @var Concrete\Controller\Dialog\Block\Edit $dialogController */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Block\View\BlockView $this */
/* @var Concrete\Core\Block\View\BlockView $view */
/* @var Concrete\Core\Form\Service\Widget\PageSelector $pageSelector */

if (!$controller->indexExists()) {
    ?>
    <div class="ccm-error"><?= t('The search index does not appear to exist. This block will not function until the reindex job has been run at least once in the dashboard.') ?></div>
    <?php
}
?>
<fieldset>
    <div class="form-group">
        <?= $form->label('title', t('Title')) ?>
        <?= $form->text('title', $controller->title, ['maxlength' => 255]) ?>
    </div>
    <div class="form-group">
        <?= $form->label('buttonText', t('Button Text')) ?>
        <?=$form->text('buttonText', $controller->buttonText, ['maxlength' => 255]) ?>
    </div>
    <div class="form-group">
        <?php
        $baseSearchPage = null;
        $baseSearchPath = 'EVERYWHERE';
        if ((string) $controller->baseSearchPath !== '') {
            $baseSearchPage = Page::getByPath($controller->baseSearchPath);
            if (is_object($baseSearchPage) && !$baseSearchPage->isError()) {
                if (is_object($c) && $c->getCollectionID() == $baseSearchPage->getCollectionID()) {
                    $baseSearchPath = 'THIS';
                    $baseSearchPage = null;
                } else {
                    $baseSearchPath = 'OTHER';
                }
            } else {
                $baseSearchPage = null;
            }
        }
        ?>
        <?= $form->label('', t('Search for Pages')) ?>
        <div class="radio">
            <label>
                <?= $form->radio('baseSearchPath', 'EVERYWHERE', $baseSearchPath === 'EVERYWHERE') ?>
                <?= t('Everywhere') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?= $form->radio('baseSearchPath', 'THIS', $baseSearchPath === 'THIS') ?>
                <?= t('Beneath the Current Page') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?= $form->radio('baseSearchPath', 'OTHER', $baseSearchPath === 'OTHER') ?>
                <?= t('Beneath Another Page') ?>
            </label>
        </div>
        <div class="ccm-searchBlock-baseSearchPath" data-for="OTHER" style="<?= $baseSearchPath === 'OTHER' ? '' : 'display:none;' ?>">
            <?= $pageSelector->selectPage('searchUnderCID', $baseSearchPath === 'OTHER' ? $baseSearchPage->getCollectionID() : null) ?>
        </div>
    </div>
    <div class="form-group">
        <?php
        if ((string) $controller->resultsURL !== '') {
            $resultsPageKind = 'URL';
        } elseif ($controller->postTo_cID) {
            $resultsPageKind = 'CID';
        } else {
            $resultsPageKind = 'THIS';
        }
        ?>
        <?= $form->label('resultsPageKind', t('Results Page')) ?>
        <div class="radio">
            <label>
                <?= $form->radio('resultsPageKind', 'THIS', $resultsPageKind === 'THIS') ?>
                <?= t('Post results to this page') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?= $form->radio('resultsPageKind', 'CID', $resultsPageKind === 'CID') ?>
                <?= t('Post results to another page') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?= $form->radio('resultsPageKind', 'URL', $resultsPageKind === 'URL') ?>
                <?= t('Post results to another URL') ?>
            </label>
        </div>
        <div class="ccm-searchBlock-resultsPageKind" data-for="CID" style="margin-top: 10px;<?= $resultsPageKind === 'CID' ? '' : 'display:none;' ?>">
            <?= $pageSelector->selectPage('postTo_cID', $controller->postTo_cID) ?>
        </div>
        <div class="ccm-searchBlock-resultsPageKind" data-for="URL" style="margin-top: 10px;<?= $resultsPageKind === 'URL' ? '' : 'display:none;' ?>">
            <?= $form->text('resultsURL', $controller->resultsURL, ['maxlength' => 255]) ?>
        </div>
    </div>
</fieldset>
<script>
$(function() {

    $('input[name="baseSearchPath"]').on('change', function() {
        var value = $('input[name="baseSearchPath"]:checked').val();
        $('div.ccm-searchBlock-baseSearchPath')
            .hide()
            .filter('[data-for="' + value + '"]').show()
        ;
    }).trigger('change');

    $('input[name="resultsPageKind"]').on('change', function() {
        var value = $('input[name="resultsPageKind"]:checked').val();
        $('div.ccm-searchBlock-resultsPageKind')
            .hide()
            .filter('[data-for="' + value + '"]').show()
        ;
    }).trigger('change');

});
</script>
