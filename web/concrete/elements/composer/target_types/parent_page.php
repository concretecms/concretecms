<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>
	<div class="control-group">
		<?=$form->label('ctID', t('Publish Beneath Page'))?>
		<div class="controls">
			<? 
			$pf = Loader::helper('form/page_selector');
			print $pf->selectPage('cParentID');
			?>
		</div>
	</div>

