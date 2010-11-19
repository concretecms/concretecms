<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$b = Block::getByID($_REQUEST['bID']);
$bc = $b->getInstance();

?>
<h2><?php echo t('Title')?></h2>
<div><?php echo $bc->getFileName()?></div>

<br/>
<h2><?php echo t('Size')?></h2>
<div><?php echo round($bc->getFileSize(), 2)?> KB</div>

<br/>
<h2><?php echo t('Added By')?></h2>
<div>
<?php 
$uID = $b->getBlockUserID();
$ui = UserInfo::getByID($uID);
print $ui->getUserName();
?>
</div>
<br/>

<?php  
$d = $bc->getDimensions();
if ($d) { ?>

<h2><?php echo t('Dimensions')?></h2>
<div><?php echo $d[0]?>x<?php echo $d[1]?></div>
<br/>
<?php  } ?>

<h2><?php echo t('Date Added:')?></h2> 
<div><?php echo date('F d, Y', strtotime($b->getBlockDateAdded()))?></div>
<br/>

<h2><?php echo t('Date Modified:')?></h2> 
<div><?php echo date('F d, Y', strtotime($b->getBlockDateLastModified()))?></div>