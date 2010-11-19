<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 

<div id="ccm-list-wrapper">
<?php 
	if (!$mode) {
		$mode = $_REQUEST['mode'];
	}
	
	$soargs = array();
	$soargs['mode'] = $mode;

	?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
	<td width="100%"><?php echo $userList->displaySummary();?></td>
		<td style="white-space: nowrap"><?php echo t('With Selected: ')?>&nbsp;</td>
		<td align="right">
		<select id="ccm-user-list-multiple-operations" disabled>
					<option value="">**</option>
					<option value="properties"><?php echo t('Edit Properties')?></option>
				<?php  if ($mode == 'choose_multiple') { ?>
					<option value="choose"><?php echo t('Choose')?></option>
				<?php  } ?>
				</select>
		</td>
	</tr>
	</table>
	
	<?php 
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/users/search_results';
	
	if (count($users) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-user-list" class="ccm-results-list">
		<tr>
			<th><input id="ccm-user-list-cb-all" type="checkbox" /></td>
			<th class="<?php echo $userList->getSearchResultsClass('uName')?>"><a href="<?php echo $userList->getSortByURL('uName', 'asc', $bu)?>"><?php echo t('Username')?></a></th>
			<th class="<?php echo $userList->getSearchResultsClass('uEmail')?>"><a href="<?php echo $userList->getSortByURL('uEmail', 'asc', $bu)?>"><?php echo t('Email Address')?></a></th>
			<th class="<?php echo $userList->getSearchResultsClass('uDateAdded')?>"><a href="<?php echo $userList->getSortByURL('uDateAdded', 'asc', $bu)?>"><?php echo t('Date Added')?></a></th>
			<th class="<?php echo $userList->getSearchResultsClass('uNumLogins')?>"><a href="<?php echo $userList->getSortByURL('uNumLogins', 'asc', $bu)?>"><?php echo t('# Logins')?></a></th>
			<?php  
			$slist = UserAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?php echo $userList->getSearchResultsClass($ak)?>"><a href="<?php echo $userList->getSortByURL($ak, 'asc', $bu)?>"><?php echo $ak->getAttributeKeyDisplayHandle()?></a></th>
			<?php  } ?>			
			<th class="ccm-search-add-column-header"><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns" id="ccm-search-add-column"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
		</tr>
	<?php 
		foreach($users as $ui) { 
			$action = View::url('/dashboard/users/search?uID=' . $ui->getUserID());
			
			if ($mode == 'choose_one' || $mode == 'choose_multiple') {
				$action = 'javascript:void(0); ccm_triggerSelectUser(' . $ui->getUserID() . ',\'' . $ui->getUserName() . '\'); jQuery.fn.dialog.closeTop();';
			}
			
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		
			<tr class="ccm-list-record <?php echo $striped?>">
			<td class="ccm-user-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?php echo $ui->getUserID()?>" user-email="<?php echo $ui->getUserEmail()?>" user-name="<?php echo $ui->getUserName()?>" /></td>
			<td><a href="<?php echo $action?>"><?php echo $txt->highlightSearch($ui->getUserName(), $keywords)?></a></td>
			<td><a href="mailto:<?php echo $ui->getUserEmail()?>"><?php echo $txt->highlightSearch($ui->getUserEmail(), $keywords)?></a></td>
			<td><?php echo date(DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS, strtotime($ui->getUserDateAdded('user')))?></td>
			<td><?php echo $ui->getNumLogins()?></td>
			<?php  
			$slist = UserAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<td><?php 
				$vo = $ui->getAttributeValueObject($ak);
				if (is_object($vo)) {
					print $vo->getValue('display');
				}
				?></td>
			<?php  } ?>		
			<td>&nbsp;</td>
			</tr>
			<?php 
		}

	?>
	
	</table>
	
	

	<?php  } else { ?>
		
		<div id="ccm-list-none"><?php echo t('No users found.')?></div>
		
	
	<?php  } 
	$userList->displayPaging($bu, false, $soargs); ?>
	
</div>

<script type="text/javascript">
$(function() { 
	ccm_setupUserSearch(); 
});
</script>