<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $label
 * @var string $description
 */
?>

<div class="form-group">
    <?= $form->label('', $label) ?>
    <?php if ($control->isPageTypeComposerControlRequiredByDefault() || $control->isPageTypeComposerFormControlRequiredOnThisRequest()) { ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php } ?>

	<?php if ($description) { ?>
        <i class="fas fa-question-circle launch-tooltip" data-bs-toggle="tooltip" title="<?= h($description); ?>"></i>
	<?php } ?>

	<?= app('helper/form/user_selector')->selectUser($this->field('user'), $control->getPageTypeComposerControlDraftValue()) ?>
</div>
