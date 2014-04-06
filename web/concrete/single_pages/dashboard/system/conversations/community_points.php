<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$ih = Loader::helper('concrete/ui');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Community Points'), false, 'span8 offset2', false);
?>

	<div class='ccm-pane-body'>
		<h4>Installed Rating Types</h4>
		<? if(count($ratingTypes) > 0) { ?>
		<table class="table">
			<tr>
				<th class="span1">Name</th>
				<th class="span1">Point Value</th>
				<th class="span1"></th>
			</tr>
			<? foreach($ratingTypes as $ratingType) { ?>
				<form action="<?=$this->action('save')?>" method='post'>
					<?=$form->hidden('rtID', $ratingType->getConversationRatingTypeID());?>
					<tr>
						<td class="span1"><?php echo $ratingType->cnvRatingTypeName;?></td>
						<td class="span1"><?=$form->text('rtPoints', $ratingType->cnvRatingTypeCommunityPoints, array('style' => 'class: span1'))?></td>
						<td class="span1"><button class='btn btn-primary pull-right'><?=t('Update')?></button></td>
					</tr>
				</form>
			<? } ?>
		</table>
		<? }else{ ?>
			<p><?=t('There are no Community Points Rating Types installed.')?></p>
		<? } ?>
	</div>
	
	<div class='ccm-pane-footer'></div>
