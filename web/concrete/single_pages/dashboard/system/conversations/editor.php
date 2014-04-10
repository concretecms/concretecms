<?php
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Conversation Editor'), false, 'span8 offset2', false);
?>
<form action="<?=$view->action('save')?>" method='post'>
	<div class='ccm-pane-body'>
		<?=$form->select('activeEditor',(array)$editors,$active);?>
	</div>
	<div class='ccm-pane-footer'>
		<button class='btn btn-primary pull-right'>Save</button>
	</div>
</form>