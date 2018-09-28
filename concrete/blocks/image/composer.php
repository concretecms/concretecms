<?php defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$al = $app->make('helper/concrete/asset_library');
$bf = null;

if ($controller->getFileID() > 0) {
    $bf = $controller->getFileObject();
}

$setcontrol = $control->getPageTypeComposerFormLayoutSetControlObject();
$fieldName = 'ccm-b-image-'.$setcontrol->getPageTypeComposerFormLayoutSetControlID();
?>

<div class="form-group">
    <?php
    echo $form->label($fieldName, $label);

    if ($description) { ?>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?php echo $description ?>"></i>
    <?php } ?>

    <div class="controls">
        <?php echo $al->image($fieldName, $view->field('fID'), t('Choose Image'), $bf); ?>
    </div>
</div>
