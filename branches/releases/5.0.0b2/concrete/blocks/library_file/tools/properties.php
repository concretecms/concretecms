<?

$b = Block::getByID($_REQUEST['bID']);
$bc = $b->getInstance();

?>
<h2>Title</h2>
<div><?=$bc->getFileName()?></div>

<br/>
<h2>Size</h2>
<div><?=round($bc->getFileSize(), 2)?> KB</div>

<br/>
<h2>Added By</h2>
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

<h2>Dimensions</h2>
<div><?=$d[0]?>x<?=$d[1]?></div>
<br/>
<? } ?>

<h2>Date Added:</h2> 
<div><?=date('F d, Y', strtotime($b->getBlockDateAdded()))?></div>
<br/>

<h2>Date Modified:</h2> 
<div><?=date('F d, Y', strtotime($b->getBlockDateLastModified()))?></div>