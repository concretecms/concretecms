<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $nh NavigationHelper */
$nh = Loader::helper('navigation');
/* @var $text TextHelper */
$text = Loader::helper('text');
/* @var $db DataBase */
$db = Loader::db();
?>
<?if($this->controller->getTask() == 'view'):?>
<?=$h->getDashboardPaneHeaderWrapper(t('Form Results'), false, false, false);?>
<div class="ccm-pane-body">
<table class="zebra-striped">
	<thead>
		<tr>
			<th><?php echo t('Form')?></th>
			<th><?php echo t('Submissions')?></th>
			<th><?php echo t('Options')?></th>
		</tr>
	</thead>
	<tbody>
		<?foreach ($surveys as $qsid => $survey):
		$block = Block::getByID((int) $survey['bID']);
		if (!is_object($block)) {
			continue;
		}
		$in_use = (int) $db->getOne(
			'SELECT count(*)
			FROM CollectionVersionBlocks
			INNER JOIN Pages
			ON CollectionVersionBlocks.cID = Pages.cID
			INNER JOIN CollectionVersions
			ON CollectionVersions.cID = Pages.cID
			WHERE CollectionVersions.cvIsApproved = 1
			AND CollectionVersionBlocks.cvID = CollectionVersions.cvID
			AND CollectionVersionBlocks.bID = ?',
			array($block->bID)
		);
		$url = $nh->getLinkToCollection($block->getBlockCollectionObject());
?>
		<tr>
			<td><?=$text->entities($survey['surveyName'])?></td>
			<td><?=$text->entities($survey['answerSetCount'])?></td>
			<td>
				<?=$ih->button(t('View Responses'), $this->action('responses', $qsid), 'left', 'primary')?>
				<?=$ih->button(t('Open Page'), $url, 'left')?>
				<?if(!$in_use):?>
				<?=$ih->button(t('Delete'), $this->action('delete', $qsid), 'right', 'danger')?>
				<?endif?>
			</td>
		</tr>
		<?endforeach?>
	</tbody>
</table>
</div><div class="ccm-pane-footer">

</div>
<?=$h->getDashboardPaneFooterWrapper(false);?>
<?elseif($this->controller->getTask() == 'responses'):?>
<?=$h->getDashboardPaneHeaderWrapper(t('Form Responses'), false, false, false);?>
<div class="ccm-pane-body">
</div><div class="ccm-pane-footer">

</div>
<?=$h->getDashboardPaneFooterWrapper(false);?>
<?endif?>