<?
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
<form id="ccm-user-search" method="get" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_user.php">
<div id="ccm-user-search-fields">
<label>Username</label>
<input type="text" id="ccm-user-search-uname" name="uName" value="<?=$_REQUEST['uName']?>" class="ccm-text" style="width: 80px" />
<label>Email Address</label>
<input type="text" id="ccm-user-search-email" name="uEmail" value="<?=$_REQUEST['uEmail']?>" class="ccm-text" style="width: 80px" />
<label>In Group</label>
<select name="gID">
	<option value="">** All</option>
<?
	$gl = new GroupList(null, true);
	$gArray = $gl->getGroupList();
	foreach ($gArray as $g) { ?>
		<option value="<?=$g->getGroupID()?>" <? if ($_REQUEST['gID'] == $g->getGroupID()) { ?> selected <? } ?>><?=$g->getGroupName()?></option>
	<? } ?>
</select>
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

		<? if ($pOptions['needPaging']) { ?>
		<br><br>
			<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>
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