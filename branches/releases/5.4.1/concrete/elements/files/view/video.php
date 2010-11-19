<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php 
$path = $fv->getRelativePath();
?>

<OBJECT ID="MediaPlayer" WIDTH="80%" HEIGHT="80%">
<PARAM NAME="FileName" VALUE="<?php echo $path?>">
<EMBED src="<?php echo $path?>" NAME="MediaPlayer" WIDTH="80%" HEIGHT="80%"  ></EMBED>
</OBJECT> 
