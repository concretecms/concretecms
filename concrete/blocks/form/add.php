<?php defined('C5_EXECUTE') or die('Access Denied.');
$u = Core::make(Concrete\Core\User\User::class);
$ui = UserInfo::getByID($u->uID);
?>

<script type="text/javascript">
var thisbID=parseInt(<?php echo empty($_REQUEST['bID']) ? 0 : (int) ($_REQUEST['bID'])?>); 
var thisbtID=parseInt(<?php echo $bt->getBlockTypeID()?>); 
</script>

<?php $this->inc('form_setup_html.php'); ?>