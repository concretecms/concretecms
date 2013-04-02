<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$form = Loader::helper('form');
$ih = Loader::helper('concrete/interface');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Community Points'), false, 'span8 offset2', false);
?>
<form action="<?=$this->action('save')?>" method='post'>
	<div class='ccm-pane-body'>
	
		<?php if ($this->controller->getTask() == 'manage') { ?>
			
			<? if($ratingType instanceof ConversationRatingType) { ?>
				
				<? print_r($ratingType) ?>
				
				<fieldset>
				
					<legend><?=$ratingType->cnvRatingTypeName?></legend>
					
					ID: <?=$ratingType->cnvRatingTypeID?>
					
					<?=$form->hidden('rtID', $ratingType->cnvRatingTypeID)?>
					<?=$form->label('rtName', 'Name')?>
					<?=$form->text('rtName', $ratingType->cnvRatingTypeName, array('style' => 'help-inline'))?>
		
					<?=$form->label('rtHandle', 'Handle')?>
					<?=$form->text('rtHandle', $ratingType->cnvRatingTypeHandle, array('style' => 'help-inline'))?>
					
					<?=$form->label('icon', 'Icon')?>
					<?=$ratingType->outputRatingTypeHTML()?>
			
					<? $pkgID = $ratingType->pkgID;
						if($pkgID > 0) {
							echo $form->label('package', 'Package');
							echo Package::getById($ratingType->pkgID)->pkgName;
						}?>
						

					<?=$form->label('rtPoints', 'Points')?>
					<?=$form->text('rtPoints', $ratingType->cnvRatingTypeCommunityPoints, array('style' => 'help-inline'))?>
					
				</fieldset>
				
				<?}else{?>
			
				<p><?=t('Invalid rating type specified.')?></p>
			
			<?}?>
			
		<? }elseif(count($ratingTypes) > 0) { ?>
			<h4>Installed Rating Types</h4>
			<ul class="item-select-list">
			<? foreach($ratingTypes as $ratingType) { ?>
					<li class="item-select-page"><a href="<?=$this->url('/dashboard/system/conversations/community_points/manage', $ratingType->getConversationRatingTypeID())?>"><?php echo $ratingType->cnvRatingTypeName;?></a></li>
			<? } ?>
			</ul>
		<? }else{ ?>
			<p><?=t('There are no Community Points Rating Types installed.')?></p>
			
		<? } ?>
	</div>
	<div class='ccm-pane-footer'>
		<? if ($this->controller->getTask() == 'manage') { ?>
		
			<? print $ih->button(t('Back to List'), $this->url('/dashboard/system/conversations/community_points'), 'left'); ?>
			<button class='btn btn-primary ccm-button-right'><?=t('Save')?></button>
		
		<? }else {?>
			<? print $ih->button(t('Add'), $this->url('/dashboard/system/conversations/community_points/add'), 'right'); ?>
		<? } ?>
	</div>
