<?php
$form = Loader::helper('form');
defined('C5_EXECUTE') or die("Access Denied.");
if (isset($response)) {
    ?>
	<div class="alert alert-info"><?=$response?></div>
<?php 
} ?>


<form method="post" action="<?=$view->action('test_search')?>">

    <?php if (isset($message)) { ?>
        <p><?=$message?></p>
    <?php } ?>

    <div class="mb-3">
        <label class="form-label"><?=t('Test')?></label>
        <?=$form->text('test_text_field')?>
    </div>

    <div class="mb-3">
        <input type="submit" name="submit" value="submit" class="btn btn-secondary" />
    </div>

</form>
