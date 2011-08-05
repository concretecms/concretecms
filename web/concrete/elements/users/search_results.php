<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<div id="ccm-list-wrapper">
<?
	if (!$mode) {
		$mode = $_REQUEST['mode'];
	}
	
	$soargs = array();
	$soargs['mode'] = $mode;

	?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
	<td width="100%"><?=$userList->displaySummary();?></td>
		<td style="white-space: nowrap"><?=t('With Selected: ')?>&nbsp;</td>
		<td align="right">
		<select id="ccm-user-list-multiple-operations" disabled>
					<option value="">**</option>
					<option value="properties"><?=t('Edit Properties')?></option>
				<? if ($mode == 'choose_multiple') { ?>
					<option value="choose"><?=t('Choose')?></option>
				<? } ?>
				</select>
		</td>
	</tr>
	</table>
	
	<?
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/users/search_results';
	
	if (count($users) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-user-list" class="ccm-results-list">
		<tr>
			<th><input id="ccm-user-list-cb-all" type="checkbox" /></th>
			<th class="<?=$userList->getSearchResultsClass('uName')?>"><a href="<?=$userList->getSortByURL('uName', 'asc', $bu)?>"><?=t('Username')?></a></th>
			<th class="<?=$userList->getSearchResultsClass('uEmail')?>"><a href="<?=$userList->getSortByURL('uEmail', 'asc', $bu)?>"><?=t('Email Address')?></a></th>
			<th class="<?=$userList->getSearchResultsClass('uDateAdded')?>"><a href="<?=$userList->getSortByURL('uDateAdded', 'asc', $bu)?>"><?=t('Date Added')?></a></th>
			<th class="<?=$userList->getSearchResultsClass('uNumLogins')?>"><a href="<?=$userList->getSortByURL('uNumLogins', 'asc', $bu)?>"><?=t('# Logins')?></a></th>
			<? 
			$slist = UserAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?=$userList->getSearchResultsClass($ak)?>"><a href="<?=$userList->getSortByURL($ak, 'asc', $bu)?>"><?=$ak->getAttributeKeyDisplayHandle()?></a></th>
			<? } ?>			
			<th class="ccm-search-add-column-header"><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns" id="ccm-search-add-column"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
		</tr>
	<?
		foreach($users as $ui) { 
			$action = View::url('/dashboard/users/search?uID=' . $ui->getUserID());
			
			if ($mode == 'choose_one' || $mode == 'choose_multiple') {
				$action = 'javascript:void(0); ccm_triggerSelectUser(' . $ui->getUserID() . ',\'' . $txt->entities($ui->getUserName()) . '\',\'' . $txt->entities($ui->getUserEmail()) . '\'); jQuery.fn.dialog.closeTop();';
			}
			
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		
			<tr class="ccm-list-record <?=$striped?>">
			<td class="ccm-user-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$ui->getUserID()?>" user-email="<?=$ui->getUserEmail()?>" user-name="<?=$ui->getUserName()?>" /></td>
			<td><a href="<?=$action?>"><?=$txt->highlightSearch($ui->getUserName(), $keywords)?></a></td>
			<td><a href="mailto:<?=$ui->getUserEmail()?>"><?=$txt->highlightSearch($ui->getUserEmail(), $keywords)?></a></td>
			<td><?=date(DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS, strtotime($ui->getUserDateAdded('user')))?></td>
			<td><?=$ui->getNumLogins()?></td>
			<? 
			$slist = UserAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<td><?
				$vo = $ui->getAttributeValueObject($ak);
				if (is_object($vo)) {
					print $vo->getValue('display');
				}
				?></td>
			<? } ?>		
			<td>&nbsp;</td>
			</tr>
			<?
		}

	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div id="ccm-list-none"><?=t('No users found.')?></div>
		
	
	<? } 
	$userList->displayPaging($bu, false, $soargs); ?>
	
</div>

<script type="text/javascript">
$(function() { 
	ccm_setupUserSearch(); 
});
</script>