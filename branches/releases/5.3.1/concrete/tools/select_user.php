<?php 
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
<?php 

$s = new UserSearch($_GET);
if ($s->getTotal() > 0) {
	$res = $s->getResult($_GET['sort'], $_GET['start'], $_GET['order'], 10);
	$pOptions = $s->paging($_GET['start'], $_GET['order'], 10);
}
?>
<form id="ccm-user-search" method="get" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_user/">
<div id="ccm-user-search-fields">
<label><?php echo t('Username')?></label>
<input type="text" id="ccm-user-search-uname" name="uName" value="<?php echo $_REQUEST['uName']?>" class="ccm-text" style="width: 80px" />
<label><?php echo t('Email Address')?></label>
<input type="text" id="ccm-user-search-email" name="uEmail" value="<?php echo $_REQUEST['uEmail']?>" class="ccm-text" style="width: 80px" />
<input type="submit" value="<?php echo t('Search')?>" />
</div>

<div id="ccm-user-search-results">

<?php  if ($s->getTotal() > 0) { ?>

		<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php');?>
		<table class="ccm-grid-list" cellspacing="0" cellpadding="0">
		<tr>
			<th><?php echo t('Username')?></th>
			<th class="full"><?php echo t('Email Address')?></th>
		</tr>
		<?php  while ($row = $res->fetchRow()) { ?>
		<tr>
			<?php echo $s->printRow($row['uName'], 'uName', '#sel' . $row['uID'] . '-' . $row['uName'], true)?>
			<?php echo $s->printRow($row['uEmail'], 'uEmail', 'mailto:' . $row['uEmail'])?>
		</tr>
		<?php  } ?>
		</table>

		<?php  if ($pOptions['needPaging']) { 
			$pOptions['script'] = REL_DIR_FILES_TOOLS_REQUIRED . '/select_user';
			?>

	<div id="ccm-user-paging">
	<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>
	</div>
		<?php  } ?>

	<?php  } else { ?>

		<?php echo t('No users found.')?>

	<?php  } ?>

	
	</div>
	
</form>

</div>

<script type="text/javascript">
$(function() {
	ccm_setupUserSearch();
});
</script>