<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u=new User();
$ui=UserInfo::getByID($u->uID);
$miniSurveyInfo['recipientEmail']=$ui->uEmail;
?>

<script>
var thisbID=parseInt(<?=intval($_REQUEST['bID'])?>); 
var thisbtID=parseInt(<?=$bt->getBlockTypeID()?>); 
</script>

<? $bt->inc('styles_include.php'); ?>
<? $bt->inc('form_setup_html.php'); ?>