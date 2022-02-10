<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var string $label
 * @var string $description
 * @var Concrete\Core\Entity\File\File $bf
 * @var \Concrete\Core\Attribute\View $view
 * @var \Concrete\Core\Application\Service\FileManager $al
 */

$setcontrol = $control->getPageTypeComposerFormLayoutSetControlObject();
?>

<div class="form-group">
    <label class="control-label form-label"><?=$label?></label>
    <?php if ($description) { ?>
        <i class="fas fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
    <?php } ?>
    <div class="controls">
        <?php echo $al->file('ccm-b-file-' . $setcontrol->getPageTypeComposerFormLayoutSetControlID(), $view->field('fID'), t('Choose File'), $bf); ?>
    </div>
</div>
