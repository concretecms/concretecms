<?
$form = Loader::helper('form');
defined('C5_EXECUTE') or die("Access Denied.");
if (isset($response)) { ?>
	<?=$response?>
<? } ?>
<form method="post" action="<?=$this->action('test_search')?>">

<p><?=t("This is just an example of how a custom form works.")?></p>

<?=$form->text('test_text_field')?>

<input type="submit" name="submit" value="submit" />


</form>