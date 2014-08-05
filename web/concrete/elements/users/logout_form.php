<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>

<form method='post'
      action='<?php echo View::url('/login', 'logout') ?>'>
    <div class="form-group">
        <span><?php echo t("We're sorry to see you go.") ?> </span>
        <hr>
    </div>

	<button class="btn btn-block btn-success"><?php echo t('Log out') ?></button>

    <?php Loader::helper('validation/token')->output('logout'); ?>
</form>
