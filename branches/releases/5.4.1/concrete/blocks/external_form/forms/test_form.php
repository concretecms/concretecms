<?php 
$form = Loader::helper('form');
defined('C5_EXECUTE') or die("Access Denied.");
if (isset($response)) { ?>
	<?php echo $response?>
<?php  } ?>
<form method="post" action="<?php echo $this->action('test_search')?>">

<p><?php echo t("This is just an example of how a custom form works.")?></p>

<?php echo $form->text('test_text_field')?>

<input type="submit" name="submit" value="submit" />


</form>