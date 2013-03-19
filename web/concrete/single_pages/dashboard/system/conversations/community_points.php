<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Community Points'), false, 'span8 offset2', false);
?>
<form action="<?=$this->action('save')?>" method='post'>
	<div class='ccm-pane-body'>
		<span>Upvotes</span><br />
		<span>Downvotes</span>
	</div>
	<div class='ccm-pane-footer'>
	</div>
