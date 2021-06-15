<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $renderer \Concrete\Core\Summary\Template\Renderer
 */
$renderer = Core::make('Concrete\Core\Summary\Template\Renderer');
?>

<div class="ccm-block-page-list-wrapper">

    <?php if (isset($pageListTitle) && $pageListTitle): ?>
        <div class="ccm-block-page-list-header">
            <h5><?=h($pageListTitle)?></h5>
        </div>
    <?php endif; ?>

    
    <?php foreach ($pages as $page) {
        
        $renderer->renderSummaryForObject($page, 'thumbnail_title_description');
        
    } ?>
    
</div>
