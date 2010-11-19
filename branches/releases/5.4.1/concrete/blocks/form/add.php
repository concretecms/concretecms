<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$u=new User();
$ui=UserInfo::getByID($u->uID);
$miniSurveyInfo['recipientEmail']=$ui->uEmail;
?>

<script>
var thisbID=parseInt(<?php  echo intval($_REQUEST['bID'])?>); 
var thisbtID=parseInt(<?php  echo $bt->getBlockTypeID()?>); 
</script>

<?php   $bt->inc('styles_include.php'); ?>
<?php   $bt->inc('form_setup_html.php'); ?>