<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
if (isset($response)) { ?>
	<?php echo $response?>
<?php  } ?>
<form method="post" action="<?php echo $this->action('test_search')?>">

<p><?php echo t("This is just an example of how a custom form works.")?></p>

<input type="text" name="test_text_field" value="<?php echo $_GET['test_text_field']?>" />

<input type="submit" name="submit" value="submit" />


</form>