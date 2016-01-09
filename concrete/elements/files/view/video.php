<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
$path = $fv->getURL();
?>

<OBJECT ID="MediaPlayer" WIDTH="80%" HEIGHT="80%">
<PARAM NAME="FileName" VALUE="<?=$path()?>">
<EMBED src="<?=$path?>" NAME="MediaPlayer" WIDTH="80%" HEIGHT="80%"  ></EMBED>
</OBJECT> 
