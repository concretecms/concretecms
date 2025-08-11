<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php
$c = Page::getCurrentPage();
?>

<?php if (isset($success) && $success) { ?>
    <div class="alert alert-success"><?=$success?></div>
<?php } ?>

<?php if ($tableName) { ?>
    <h2><?=$tableName?></h2>
<?php } ?>

<?php if ($tableDescription) {  ?>
    <p><?=$tableDescription?></p>
<?php } ?>


<?php if ($enableSearch) { ?>
    <form method="get" action="<?=$c->getCollectionLink()?>" class="mb-5">
        <div class="row">
            <div class="col-md-6">
                <div class="hstack gap-2">
                    <?=$form->text('keywords', ['placeholder' => t('Keyword Search')])?>
                    <button type="submit" class="btn btn-primary" name="search" value="search"><?=t('Search')?></button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <?php if (count($tableSearchProperties)) { ?>
                    <a href="#" data-document-library-advanced-search="<?=$bID?>"
                       class="btn btn-outline-primary ccm-block-document-library-advanced-search"><?=t('Advanced Search')?></a>
                <?php } ?>
                <?php if ($canAddFiles) { ?>
                    <a href="#" data-document-library-add-files="<?=$bID?>"
                       class="btn btn-secondary ccm-block-document-library-add-files"><?=t('Add Files')?></a>
                <?php } ?>
            </div>
        </div>

        <?php if (count($tableSearchProperties)) { ?>
            <div data-document-library-advanced-search-fields="<?=$bID?>"
                 class="ccm-block-document-library-advanced-search-fields mt-5">
                <input type="hidden" name="advancedSearchDisplayed" value="">
                <?php foreach($tableSearchProperties as $column) { ?>
                    <div class="mb-3"><h5><?=$controller->getColumnTitle($column)?></h5>
                    <div><?=$controller->getSearchValue($column)?></div>
                        </div>
                <?php } ?>
            </div>
        <?php } ?>


    </form>
<?php } else if ($canAddFiles) { ?>
    <div class="mb-5 text-right">
        <a href="#" data-document-library-add-files="<?=$bID?>"
           class="btn btn-secondary ccm-block-document-library-add-files"><?=t('Add Files')?></a>
    </div>
<?php } ?>

<?php if ($canAddFiles) { ?>
    <div data-document-library-upload-action="<?=$view->action('upload')?>" data-document-library-add-files="<?=$bID?>" class="ccm-block-document-library-add-files-uploader">
        <div class="ccm-block-document-library-add-files-pending"><?=t('Upload Files')?></div>
        <div class="ccm-block-document-library-add-files-uploading"><?=t('Uploading')?> <i class="fas fa-spin fa-spinner"></i></div>
        <input type="file" name="file" />
        <?=Core::make('token')->output()?>
    </div>
<?php } ?>


<?php
if (isset($breadcrumbs) && $breadcrumbs) { ?>
    <nav>
        <ol class="breadcrumb">
            <?php
            $breadcrumbsTotal = count($breadcrumbs);
            for ($i = 0; $i < $breadcrumbsTotal; $i++) {
                $breadcrumb = $breadcrumbs[$i];
                $url = $breadcrumb['url'];
                $name = $breadcrumb['name'];
                $lastItem = ($i + 1) == $breadcrumbsTotal;
                ?>
                <li class="breadcrumb-item <?php if ($lastItem) { ?> active<?php } ?>">
                    <?php if ($lastItem) { ?>
                        <?=$name?>
                    <?php } else { ?>
                        <a href="<?= $url ?>"><?= $name ?></a>
                    <?php } ?>
                </li>
                <?php
            }
            ?>
        </ol>
    </nav>
    <?php
}
?>

<script type="text/javascript">
    $(function() {
        $.concreteDocumentLibrary({
            'bID': '<?=$bID?>',
            'allowFileUploading': <?php if ($allowFileUploading) { ?>true<?php } else { ?>false<?php } ?>,
            'allowInPageFileManagement': <?php if ($allowInPageFileManagement) { ?>true<?php } else { ?>false<?php } ?>,
            'advancedSearchDisplayed': <?php if ($advancedSearchDisplayed) { ?>true<?php } else { ?>false<?php } ?>
        });
    });
</script>
