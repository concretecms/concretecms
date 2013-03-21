<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Community Points'), false, 'span8 offset2', false);
?>
<form action="<?=$this->action('save')?>" method='post'>
	<div class='ccm-pane-body'>
		<h4>Point Values</h4>
		<fieldset>
		<?=$form->label('upvote', 'Upvote'); ?>
		<?=$form->text('upvote', '10'); ?>
		<?=$form->label('upvote', 'Downvote'); ?>
		<?=$form->text('upvote', '0'); ?>
		</fieldset>
	</div>
	<div class='ccm-pane-footer'>
	</div>
