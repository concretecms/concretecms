<?php
    defined('C5_EXECUTE') or die("Access Denied.");

    use Concrete\Core\Page\Page;

    $c = Page::getCurrentPage();
    if (!$content && is_object($c) && $c->isEditMode()) {
        ?>
		<div class="ccm-edit-mode-disabled-item"><?=t('Empty Content Block.')?></div> 
	<?php 
    } else {
        echo $content;
    }
