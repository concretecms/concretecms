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

<?php if ($showPagination): ?>
    <?php
    $pagination = $list->getPagination();
    if ($pagination->getTotalPages() > 1) {
        $options = array(
            'prev_message'        => '<i class="fas fa-chevron-left me-2"></i> Prev',
            'next_message'        => 'Next <i class="fas fa-chevron-right ms-2"></i>'
        );
        echo $pagination->renderDefaultView($options);
    }
    ?>
<?php endif; ?>

<?php if ($showPagination) { ?>
    <?php //echo $pagination; ?>
<?php } ?>

