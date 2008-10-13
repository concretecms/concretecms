<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$uc = Page::getByPath("/dashboard/users");
$ucp = new Permissions($uc);
if (!$ucp->canRead()) {
	die(_("You have no access to users."));
}

Loader::library('search');
Loader::model('search/user');

?>

<div id="ccm-user-search-wrapper">
<?

$s = new UserSearch($_GET);
if ($s->getTotal() > 0) {
	$res = $s->getResult($_GET['sort'], $_GET['start'], $_GET['order'], 10);
	$pOptions = $s->paging($_GET['start'], $_GET['order'], 10);
}
?>
<form id="ccm-user-search" method="get" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_user/">
<div id="ccm-user-search-fields">
<label>Username</label>
<input type="text" id="ccm-user-search-uname" name="uName" value="<?=$_REQUEST['uName']?>" class="ccm-text" style="width: 80px" />
<label>Email Address</label>
<input type="text" id="ccm-user-search-email" name="uEmail" value="<?=$_REQUEST['uEmail']?>" class="ccm-text" style="width: 80px" />
<input type="submit" value="Search" />
</div>

<div id="ccm-user-search-results">

<? if ($s->getTotal() > 0) { ?>

		<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php');?>
		<table class="ccm-grid-list" cellspacing="0" cellpadding="0">
		<tr>
			<th>Username</th>
			<th class="full">Email Address</th>
		</tr>
		<? while ($row = $res->fetchRow()) { ?>
		<tr>
			<?=$s->printRow($row['uName'], 'uName', '#sel' . $row['uID'] . '-' . $row['uName'], true)?>
			<?=$s->printRow($row['uEmail'], 'uEmail', 'mailto:' . $row['uEmail'])?>
		</tr>
		<? } ?>
		</table>

		<? if ($pOptions['needPaging']) { 
			$pOptions['script'] = REL_DIR_FILES_TOOLS_REQUIRED . '/select_user';
			?>

	<div id="ccm-user-paging">
	<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>
	</div>
		<? } ?>

	<? } else { ?>

		No users found.

	<? } ?>

	
	</div>
	
</form>

</div>

<script type="text/javascript">
$(function() {
	ccm_setupUserSearch();
});
</script>