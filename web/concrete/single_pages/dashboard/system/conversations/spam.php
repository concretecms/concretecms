<?php
$form = Loader::helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Conversation Spam Settings'), false, 'span8 offset2', false);
?>
<form action="<?=$this->action('save')?>" method='post'>
	<div class='ccm-pane-body'>
		<label for='group_id'>Spam Whitelist Group</label>
		<?=$form->select('group_id', (array)$groups, $whitelistGroup);?>
	</div>
	<div class='ccm-pane-footer'>
		<button class='btn btn-primary ccm-button-right'>Save</button>
	</div>
</form>