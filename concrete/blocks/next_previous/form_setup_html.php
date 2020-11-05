<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var string $nextLabel */
/** @var string $previousLabel */
/** @var string $parentLabel */
/** @var string $orderBy */
/** @var int $loopSequence */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<fieldset id="ccm_edit_pane_nextPreviousWrap">
    <div class="form-group">
        <?php echo $form->label('nextLabel', t('Next Label')); ?>
        <?php echo $form->text('nextLabel', h($nextLabel), ['placeholder' => t('leave blank to hide')]); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('previousLabel', t('Previous Label')); ?>
        <?php echo $form->text('previousLabel', h($previousLabel), ['placeholder' => t('leave blank to hide')]); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('parentLabel', t('Up Label')); ?>
        <?php echo $form->text('parentLabel', h($parentLabel), ['placeholder' => t('leave blank to hide')]); ?>
    </div>

    <div class="form-group">
        <div class="form-check">
            <?php echo $form->checkbox('loopSequence', 1, intval($loopSequence)); ?>
            <?php echo $form->label('loopSequence', t('Loop Navigation'), ["class" => "form-check-label"]); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('orderBy', t('Order Pages')); ?>
        <?php echo $form->select('orderBy', [
            'display_asc' => t('Sitemap'),
            'chrono_desc' => t('Chronological'),
            'display_desc' => t('Reverse Sitemap'),
            'chrono_asc' => t('Reverse Chronological'),
        ], $orderBy); ?>
    </div>
</fieldset>
