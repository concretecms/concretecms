<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Block\Search\Controller;
use Concrete\Controller\Dialog\Block\Edit;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Application;

/** @var BlockType $bt */
/** @var Block $b */
/** @var Page $c */
/** @var Controller $controller */
/** @var Edit $dialogController */
/** @var Form $form */
/** @var BlockView $this */
/** @var BlockView $view */
/** @var PageSelector $pageSelector */
/** @var bool $allowUserOptions */
/** @var bool $searchAll */
/** @var bool $allowUserOptions */

$app = Application::getFacadeApplication();
/** @var Service $siteService */
$siteService = $app->make(Service::class);
$sites = $siteService->getList();

if ((string)$controller->resultsURL !== '') {
    $resultsPageKind = 'URL';
} elseif ($controller->postTo_cID) {
    $resultsPageKind = 'CID';
} else {
    $resultsPageKind = 'THIS';
}

$baseSearchPage = null;
$baseSearchPath = 'EVERYWHERE';

if ((string)$controller->baseSearchPath !== '') {
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

<?php if (!$controller->indexExists()) { ?>
    <div class="ccm-error">
        <?php echo t('The search index does not appear to exist. This block will not function until the reindex job has been run at least once in the dashboard.') ?>
    </div>
<?php } ?>

<fieldset>
    <div class="form-group">
        <?php echo $form->label('title', t('Title')) ?>
        <?php echo $form->text('title', $controller->title, ['maxlength' => 255]) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('buttonText', t('Button Text')) ?>
        <?php echo $form->text('buttonText', $controller->buttonText, ['maxlength' => 255]) ?>
    </div>

    <div class="form-group">
        <?php if (count($sites) > 1) { ?>
            <?php echo $form->label('allowUserOptions', t('User Options')) ?>

            <div class="form-check">
                <label for="allowUserOptions" class="form-check-label">
                    <?php echo $form->checkbox('allowUserOptions', 'ALLOW', (int)$allowUserOptions === 1) ?>
                    <?php echo t('Allow users to choose search options') ?>
                </label>
            </div>
        <?php } ?>

        <label>
            <?php echo t('Search for Pages'); ?>
        </label>

        <div class="form-check">
            <label for="baseSearchPathEverywhere" class="form-check-label">
                <?php echo $form->radio('baseSearchPathEverywhere', 'EVERYWHERE', $baseSearchPath === 'EVERYWHERE', ["name" => "baseSearchPath", "id" => "baseSearchPathEverywhere"]) ?>
                <?php echo t('In the Current Site'); ?>
            </label>
        </div>

        <?php if (count($sites) > 1) { ?>
            <div class="form-check">
                <label for="baseSearchPathInAllSites" class="form-check-label">
                    <?php echo $form->radio('baseSearchPathInAllSites', 'ALL', (int)$searchAll === 1, ["name" => "baseSearchPath", "id" => "baseSearchPathInAllSites"]) ?>
                    <?php echo t('In all Sites'); ?>
                </label>
            </div>
        <?php } ?>

        <div class="form-check">
            <label for="baseSearchPathBeneathTheCurrentPage" class="form-check-label">
                <?php echo $form->radio('baseSearchPathBeneathTheCurrentPage', 'THIS', $baseSearchPath === 'THIS', ["name" => "baseSearchPath", "id" => "baseSearchPathBeneathTheCurrentPage"]) ?>
                <?php echo t('Beneath the Current Page'); ?>
            </label>
        </div>

        <div class="form-check">
            <label for="baseSearchPathBeneathAnotherPage" class="form-check-label">
                <?php echo $form->radio('baseSearchPathBeneathAnotherPage', 'OTHER', $baseSearchPath === 'OTHER', ["name" => "baseSearchPath", "id" => "baseSearchPathBeneathAnotherPage"]) ?>
                <?php echo t('Beneath Another Page'); ?>
            </label>
        </div>

        <div class="ccm-searchBlock-baseSearchPath" data-for="OTHER"
             style="<?php echo $baseSearchPath === 'OTHER' ? '' : 'display:none;' ?>">
            <?php echo $pageSelector->selectPage('searchUnderCID', $baseSearchPath === 'OTHER' ? $baseSearchPage->getCollectionID() : null) ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('resultsPageKind', t('Results Page')) ?>

        <div class="form-check">
            <label for="resultsPageKindThis" class="form-check-label">
                <?php echo $form->radio('resultsPageKind', 'THIS', $resultsPageKind === 'THIS', ["name" => "resultsPageKind", "id" => "resultsPageKindThis"]) ?>
                <?php echo t('Post results to this page') ?>
            </label>
        </div>


        <div class="form-check">
            <label for="resultsPageKindCID" class="form-check-label">
                <?php echo $form->radio('resultsPageKind', 'CID', $resultsPageKind === 'CID', ["name" => "resultsPageKind", "id" => "resultsPageKindCID"]) ?>
                <?php echo t('Post results to another page') ?>
            </label>
        </div>

        <div class="form-check">
            <label for="resultsPageKindUrl" class="form-check-label">
                <?php echo $form->radio('resultsPageKind', 'URL', $resultsPageKind === 'URL', ["name" => "resultsPageKind", "id" => "resultsPageKindUrl"]) ?>
                <?php echo t('Post results to another URL') ?>
            </label>
        </div>

        <div class="ccm-searchBlock-resultsPageKind" data-for="CID"
             style="margin-top: 10px;<?php echo $resultsPageKind === 'CID' ? '' : 'display:none;' ?>">
            <?php echo $pageSelector->selectPage('postTo_cID', $controller->postTo_cID) ?>
        </div>

        <div class="ccm-searchBlock-resultsPageKind" data-for="URL"
             style="margin-top: 10px;<?php echo $resultsPageKind === 'URL' ? '' : 'display:none;' ?>">
            <?php echo $form->text('resultsURL', $controller->resultsURL, ['maxlength' => 255]) ?>
        </div>
    </div>
</fieldset>

<script>
    $(function () {
        $('input[name="baseSearchPath"]').on('change', function () {
            $('div.ccm-searchBlock-baseSearchPath').hide().filter('[data-for="' + $('input[name="baseSearchPath"]:checked').val() + '"]').show();
        }).trigger('change');

        $('input[name="resultsPageKind"]').on('change', function () {
            $('div.ccm-searchBlock-resultsPageKind').hide().filter('[data-for="' + $('input[name="resultsPageKind"]:checked').val() + '"]').show();
        }).trigger('change');
    });
</script>
