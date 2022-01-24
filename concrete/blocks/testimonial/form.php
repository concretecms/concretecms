<?php

use Concrete\Core\File\File;

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var string|null $image */
/** @var string|null $name */
/** @var string|null $position */
/** @var string|null $company */
/** @var string|null $companyURL */
/** @var string|null $paragraph */
/** @var string|null $awardImage */

defined('C5_EXECUTE') or die('Access Denied.');

$name = $name ?? '';
$position = $position ?? '';
$company = $company ?? '';
$companyURL = $companyURL ?? '';
$paragraph = $paragraph ?? '';

$fo = null;
if (isset($fID) && $fID > 0) {
    $fo = File::getByID($fID);
}

$awardImage = null;
if (isset($awardImageID) && $awardImageID > 0) {
    $awardImage = File::getByID($awardImageID);
}
?>

<div class="form-group">
    <?php echo $form->label('fID', t('Picture')); ?>
    <?php
    $al = app('helper/concrete/asset_library');
    echo $al->file('ccm-b-file', 'fID', t('Choose File'), $fo);
    ?>
</div>

<div class="form-group">
    <?php echo $form->label('name', t('Name')); ?>
    <?php echo $form->text('name', $name)?>
</div>

<div class="form-group">
    <?php echo $form->label('position', t('Position')); ?>
    <?php echo $form->text('position', $position)?>
</div>

<div class="form-group">
    <?php echo $form->label('company', t('Company')); ?>
    <?php echo $form->text('company', $company)?>
</div>

<div class="form-group">
    <?php echo $form->label('companyURL', t('Company URL')); ?>
    <?php echo $form->text('companyURL', $companyURL)?>
</div>

<div class="form-group">
    <?php echo $form->label('paragraph', t('Bio/Quote')) ?>
    <?php echo $form->textarea('paragraph', $paragraph, ['rows' => 5])?>
</div>

<div class="form-group">
    <?php echo $form->label('awardImageID', t('Award Image')) ?>
    <?= $al->image('awardImageID', 'awardImageID', t('Choose File'), $awardImage); ?>

</div>
