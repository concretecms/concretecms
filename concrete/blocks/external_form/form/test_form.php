<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var Concrete\Core\File\Service\File $file */
/** @var Concrete\Core\Form\Service\Form $form */

/** @var string[] $filenames */
/** @var string|null $filename */
/** @var Concrete\Core\Block\View\BlockView $view */
/** @var string|null $response */
/** @var string $message */

if (isset($response)) {
    ?>
	<div class="alert alert-info"><?=$response?></div>
<?php
} else {
    ?>
    <p><?=$message?></p>
    <?php
} ?>


<form method="post" action="<?=$view->action('test_search')?>">
    <div class="form-floating">

        <?=$form->text('test_text_field', ['placeholder' => t('Test')])?>
        <?=$form->label('test_text_field', t('Test'))?>
    </div>

    <div class="form-group mt-4">
        <input type="submit" name="submit" value="submit" class="btn btn-outline-primary" />
    </div>

</form>
