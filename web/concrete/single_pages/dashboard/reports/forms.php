<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $nh NavigationHelper */
$nh = Loader::helper('navigation');
/* @var $text TextHelper */
$text = Loader::helper('text');
/* @var $dh DateHelper*/
$dh = Loader::helper('date');
/* @var $urlhelper UrlHelper */
$urlhelper = Loader::helper('url');
/* @var $db DataBase */
$db = Loader::db();
?>
<?if(!isset($questionSet)):?>
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
				<?=$ih->button(t('View Responses'), $this->action('').'?qsid='.$qsid, 'left', 'primary')?>
				<?=$ih->button(t('Open Page'), $url, 'left')?>
				<?if(!$in_use):?>
				<?=$ih->button(t('Delete'), $this->action('').'?bID='.$survey['bID'].'&qsID='.$qsid, 'right', 'danger')?>
				<?endif?>
			</td>
		</tr>
		<?endforeach?>
	</tbody>
</table>
</div><div class="ccm-pane-footer">

</div>
<?=$h->getDashboardPaneFooterWrapper(false);?>
<?else:?>
<?=$h->getDashboardPaneHeaderWrapper(t('Responses to %s', $surveys[$questionSet]['surveyName']), false, false, false);?>
<div class="ccm-pane-body">
<ul class="breadcrumb">
	<li><a href="<?=$this->action('')?>"><?=t('Back to Form List')?></a></li>
</ul>
<?if(count($answerSets) == 0):?>
<div><?=t('No one has yet submitted this form.')?></div>
<?else:?>

<table class="zebra-striped">
	<thead>
		<tr>
			<? if($_REQUEST['sortBy']=='chrono') { ?>
			<th class="header headerSortUp">
				<a href="<?=$text->entities($urlhelper->unsetVariable('sortBy'))?>">
			<? } else { ?>
			<th class="header headerSortDown">
				<a href="<?=$text->entities($urlhelper->setVariable('sortBy', 'chrono'))?>">
			<? } ?>		
				<?=t('Submitted Date')?>
				</a>
			</th>
			<th><?=t('Submitted By User')?></th>
<?foreach($questions as $question):?>
			<th><?=$question['question']?></th>
<?endforeach?>
		</tr>	
	</thead>
	<tbody>
<?foreach($answerSets as $answerSetId => $answerSet):?>
		<tr>
			<td>
<?=$dh->getSystemDateTime($answerSet['created'])?></td>
			<td><?
			if ($answerSet['uID'] > 0) { 
				$ui = UserInfo::getByID($answerSet['uID']);
				if (is_object($ui)) {
					print $ui->getUserName().' ';
				}
				print t('(User ID: %s)', $answerSet['uID']);
			}
			?></td>
<?foreach($questions as $questionId => $question):
			if ($question['inputType'] == 'fileupload') {
				$fID = (int) $answerSet['answers'][$questionId]['answer'];
				$file = File::getByID($fID);
				if ($fID && $file) {
					$fileVersion = $file->getApprovedVersion();
					echo '<td><a href="' . $fileVersion->getRelativePath() .'">'.
						$text->entities($fileVersion->getFileName()).'</a></td>';
				} else {
					echo '<td>'.t('File not found').'</td>';
				}
			} else if($question['inputType'] == 'text') {
				echo '<td title="'.$text->entities($answerSet['answers'][$questionId]['answerLong']).'">';
				echo $text->entities($text->shortenTextWord($answerSet['answers'][$questionId]['answerLong'], 75));
				echo '</td>';
			} else {
				echo '<td>'.$text->entities($answerSet['answers'][$questionId]['answer']).'</td>';
			}
			
endforeach?>
		</tr>
<?endforeach?>
	</tbody>
</table>
<? if($paginator && strlen($paginator->getPages())>0){ ?>	 
	 <div class="pagination">
		 <div class="pageLeft"><?=$paginator->getPrevious()?></div>
		 <div class="pageRight"><?=$paginator->getNext()?></div>
		 <?=$paginator->getPages()?>
	 </div>		
<? } ?>		
<?endif?>
</div><div class="ccm-pane-footer">
<?=$ih->button(t('Export to Excel'), $this->action('excel', '?qsid=' . $questionSet))?>
<?if(!isset($_REQUEST['all']) || $_REQUEST['all'] !=1 ):?>
<?=$ih->button(t('Show All'), $this->action('').'?all=1&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>
<?else:?>
<?=$ih->button(t('Show Paging'), $this->action('').'?all=0&sortBy='.$_REQUEST['sortBy'].'&qsid='.$questionSet)?>
<?endif?>
</div>
<?=$h->getDashboardPaneFooterWrapper(false);?>
<?endif?>