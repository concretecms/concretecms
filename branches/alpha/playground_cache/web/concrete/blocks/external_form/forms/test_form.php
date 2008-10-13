<?
defined('C5_EXECUTE') or die(_("Access Denied."));
if (isset($response)) { ?>
	<?=$response?>
<? } ?>
<form method="post" action="<?=$this->action('test_search')?>">

<p>This is just a test example of how a custom form works. You submit the url as in the action above (in the PHP source) and it's automatically handled by the controller.</p>

<input type="text" name="test_text_field" value="<?=$_GET['test_text_field']?>" />

<input type="submit" name="submit" value="submit" />


</form>