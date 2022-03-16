<?php
defined('C5_EXECUTE') or die('Access Denied.');
/** @var string $label */
/** @var string $description */
/** @var Concrete\Block\Content\Controller $controller */
/** @var Concrete\Core\Page\Type\Composer\Control\BlockControl $view */
?>

<div class="form-group">
	<label class="form-label" for=""><?=$label?></label>
	<?php if ($description) { ?>
	<i class="fas fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php } ?>
	<?php
    $content = $controller->getContentEditMode();
    if ($controller->getRequest()->getMethod() === 'POST') {
        $data = $view->getRequestValue();
        $content = $data['content'];
    }
    echo app('editor')->outputPageComposerEditor($view->field('content'), $content);
    ?>
</div>