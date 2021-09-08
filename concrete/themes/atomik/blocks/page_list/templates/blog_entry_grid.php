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
    <div class="row gx-7">
    <?php foreach ($pages as $page) { ?>

        <div class="col-lg-4 col-md-6 mb-5">
            <?php
            $renderer->renderSummaryForObject($page, 'blog_entry_card');
            ?>
        </div>

    <?php } ?>
    </div>
</div>

<?php if ($showPagination) { ?>
    <?php echo $pagination; ?>
<?php } ?>

