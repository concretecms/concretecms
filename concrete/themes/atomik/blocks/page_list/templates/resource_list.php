<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Summary\Template\Renderer;

$c = Page::getCurrentPage();
$renderer = app(Renderer::class);

if (isset($pageListTitle) && $pageListTitle) {
    ?>
    <div class="ccm-block-page-list-header">
        <<?php echo $titleFormat; ?>><?php echo h($pageListTitle) ?></<?php echo $titleFormat; ?>>
    </div>
    <?php
} ?>

<div class="container">

    <form method="get" action="<?=$view->action('search_keywords')?>" class="mb-5">
        <div class="row">
            <div class="col-md-6">
                <div class="hstack gap-2">
                    <?=$form->text('keywords', ['placeholder' => t('Keyword Search')])?>
                    <button type="submit" class="btn btn-primary" name="search"><?=t('Search')?></button>
                </div>
            </div>
        </div>
    </form>


    <div class="row">
    <?php foreach ($pages as $page) { ?>

        <div class="col-lg-6 d-flex mb-4">
            <?php
            $renderer->renderSummaryForObject($page, 'resource_page');
            ?>
        </div>

    <?php } ?>
    </div>

</div>

<?php if ($showPagination) { ?>
    <?php echo $pagination; ?>
<?php } ?>

