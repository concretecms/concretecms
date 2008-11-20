<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$b = Block::getByID($_REQUEST['bID']);
$bc = $b->getInstance();

?>
<h2><?=t('Title')?></h2>
<div><?=$bc->getFileName()?></div>

<br/>
<h2><?=t('Size')?></h2>
<div><?=round($bc->getFileSize(), 2)?> KB</div>

<br/>
<h2><?=t('Added By')?></h2>
<div>
<?
$uID = $b->getBlockUserID();
$ui = UserInfo::getByID($uID);
print $ui->getUserName();
?>
</div>
<br/>

<? 
$d = $bc->getDimensions();
if ($d) { ?>

<h2><?=t('Dimensions')?></h2>
<div><?=$d[0]?>x<?=$d[1]?></div>
<br/>
<? } ?>

<h2><?=t('Date Added:')?></h2> 
<div><?=date('F d, Y', strtotime($b->getBlockDateAdded()))?></div>
<br/>

<h2><?=t('Date Modified:')?></h2> 
<div><?=date('F d, Y', strtotime($b->getBlockDateLastModified()))?></div>