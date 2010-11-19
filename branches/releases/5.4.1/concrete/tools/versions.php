<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$valt = Loader::helper('validation/token');
	$fh = Loader::helper('file');
	
	$token = '&' . $valt->getParameter();
	
	$c = Page::getByID($_REQUEST['cID']);
	$cID = $c->getCollectionID();
	$cp = new Permissions($c);
	$isCheckedOut = $c->isCheckedOut() && !$c->isEditMode();
	
	if (!$cp->canReadVersions() && !$cp->canApproveCollection()) {
		die(_("Access Denied."));
	}
	
	if ($_GET['vtask'] == 'view_version') { ?>
		<?php  /*
		we use the always-updated ID below so that Safari doesn't cache the iframe's contents. We probably shouldn't be
		making a new iframe on every request to this anyway, but it doesn't happen very often and it represents a significant
		hurdle to making it a bit of a better citizen, so we'll do it this way for now.
		
		*/
		?>
		
		<iframe border="0" id="v<?php echo time()?>" frameborder="0" height="100%" width="100%" src="<?php echo BASE_URL . DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cvID=<?php echo $_REQUEST['cvID']?>&cID=<?php echo $_REQUEST['cID']?>" />
	
	<?php  
		exit;
	}
	
	if (isset($_GET['cvID1']) && isset($_GET['cvID2']) && (isset($_GET['vtask']))) {
		
		if ($_GET['vtask'] == 'compare') {
			session_write_close();
			
			// compare
			$src1 = time() . '_' . $_GET['cID'] . '_' . $_GET['cvID1'] . '.html';
			$src2 = time() . '_' . $_GET['cID'] . '_' . $_GET['cvID2'] . '.html';
			
			ob_start();
			$c = Page::getByID($_GET['cID'], $_GET['cvID1']);
			$v = View::getInstance();
			$v->disableEditing();
			$v->disableLinks();

			$v->render($c);
			$ret = ob_get_contents();
			ob_end_clean();

			file_put_contents($fh->getTemporaryDirectory() . '/' . $src1, $ret);
			
			ob_start();
			$c = Page::getByID($_GET['cID'], $_GET['cvID2']);
			$v = View::getInstance();
			$v->disableEditing();
			$v->disableLinks();
			$v->render($c);
			$ret = ob_get_contents();
			ob_end_clean();

			file_put_contents($fh->getTemporaryDirectory() . '/' . $src2, $ret);
			
			if (is_executable(DIR_FILES_BIN_HTMLDIFF)) {
				$val = system(DIR_FILES_BIN_HTMLDIFF . ' ' . $fh->getTemporaryDirectory() . '/' . $src1 . ' ' . $fh->getTemporaryDirectory() . '/' . $src2);
				$val = str_replace($val, '</head>', '<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.compare.css";</style></head>');
				print $val;
			} else {
				print t('You must make %s executable in order to compare versions of pages.',DIR_FILES_BIN_HTMLDIFF);
			}
			exit;
		
		} else if ($_GET['vtask'] == 'compare_iframe') { ?>
		
			<iframe id="v<?php echo time()?>" border="0" frameborder="0" height="100%" width="100%" src="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cvID1=<?php echo $_REQUEST['cvID1']?>&cvID2=<?php echo $_REQUEST['cvID2']?>&vtask=compare&cID=<?php echo $_REQUEST['cID']?>" />
			
		
		<?php  }
		
		exit;
		
	}
	
	if (!$isCheckedOut) {
		
		if ($valt->validate()) {
			switch($_REQUEST['vtask']) {
				case 'remove_group':
					if ($cp->canApproveCollection() && !$isCheckedOut) {
						$cvIDs = explode('_', $_REQUEST['cvIDs']);
						if (is_array($cvIDs)) {
							foreach($cvIDs as $cvID) {
								$v = CollectionVersion::get($c, $cvID);
								if (!$v->isApproved()) {
									$v->delete();							
								}
							}
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&cID=" . $cID);
							exit;
						}
					}
					break;
				case 'approve':
					if ($cp->canApproveCollection() && !$isCheckedOut) {
						$v = CollectionVersion::get($c, $_GET['cvID']);
						$v->approve();
						header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&cID=" . $cID . "&cvID=" . $_GET['cvID']);
						exit;
					}
					break;
				case 'deny':
					if ($cp->canApproveCollection() && !$isCheckedOut) {
						$v = CollectionVersion::get($c, $_GET['cvID']);
						if ($v->isApproved()) {
							$v->deny();
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&cID=" . $cID . "&cvID=" . $_GET['cvID']);
							exit;
						}
					}
					break;
			}
			
			switch($_GET['ctask']) {
				case 'approve_pending_action':
					if ($cp->canApproveCollection() && $cp->canWrite() && !$isCheckedOut) {
						$approve = false;
						if ($c->isPendingDelete()) {
							$children = $c->getNumChildren();
							if ($children == 0 || $cp->canCP()) {
								$approve = true;
								$cParentID = $c->getCollectionParentID();
							}
						} else {
							$approve = true;
						}
						if ($approve) {
							$c->approvePendingAction();
						}
						if ($c->isPendingDelete() && $approve) {
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?cIsDeleted=1&cParentID={$cParentID}");
						} else {
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?cID=" . $cID);
						}
						exit;
					}
					break;
				case 'clear_pending_action':
					if ($cp->canApproveCollection() && $cp->canWrite() && !$isCheckedOut) {
						$c->clearPendingAction();
						header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?cID=" . $cID);
						exit;
					}
					break;
			}
		}
		
		$page = $_REQUEST['ccm_paging_p'];
		if (!$page) {
			$page = 1;
		}
		$vl = new VersionList($c, 20, $page);
		$total = $vl->getVersionListCount();
		$vArray = $vl->getVersionListArray();
		$ph = Loader::helper('pagination');
		$ph->init($page, $total, '', 20, 'ccm_goToVersionPage');
	}



if (!$_GET['versions_reloaded']) { ?>
	<div id="ccm-versions-container">
<?php  } ?>

<?php  Loader::element('pane_header', array('c'=>$c, 'close'=>'ccm_exitVersionList')); ?>

<div class="ccm-pane-controls">

<script type="text/javascript">

var ccm_versionsChecked = 0;
/* if this gets set to true, exiting this pane reloads the page */
var ccm_versionsMustReload = false;

$(function() {
	
	$(".ccm-version").dialog();
	
	$("input[type=checkbox]").click(function() {
		if ($(this).get(0).checked) {
			ccm_versionsChecked++;
		} else {
			ccm_versionsChecked--;
		}
		
		ccm_setSelectors();
		
	});
	
	<?php  if ($_REQUEST['forcereload']) { ?>
		ccm_versionsMustReload = true;
	<?php  } ?>

});

ccm_setSelectors = function() {
	if (ccm_versionsChecked < 0) {
		ccm_versionsChecked = 0;
	}
	
	/* first, we grab whether an active version is checked, so we can use that later */
	var isActiveChecked = ( $("input.cb-version-active:checked").length > 0 );
	
	/* if two and only two are checked, we can compare */
	
	if (ccm_versionsChecked == 2) {
		$("input[name=vCompare]").get(0).disabled = false;
	} else {
		$("input[name=vCompare]").get(0).disabled = true;
	}
	
	
	if (ccm_versionsChecked > 0 && (!isActiveChecked)) {
		$("input[name=vRemove]").get(0).disabled = false;
	} else {
		$("input[name=vRemove]").get(0).disabled = true;
	}
	
	if (ccm_versionsChecked == 1 && (!isActiveChecked)) {
		$("input[name=vApprove]").get(0).disabled = false;
	} else {
		$("input[name=vApprove]").get(0).disabled = true;
	}
	
	
}

ccm_deselectVersions = function() {
	$("input[type=checkbox]").each(function() {
		$(this).get(0).checked = false;
	});
	
	ccm_versionsChecked = 0;
}

ccm_exitVersionList = function() {
	if (ccm_versionsMustReload) {
		window.location.reload();
	} else {
		ccm_hidePane();
	}
}

ccm_runAction = function(item) {
	$("#ccm-versions-container").load(item.href, function() {
		
	} );
	return false;
}

$("a#ccm-version-select-none").click(function() {
	ccm_deselectVersions();
	ccm_setSelectors();
});

$("a#ccm-version-select-old").click(function() {
	ccm_deselectVersions();
	$("input[class=cb-version-old]").each(function() {
		$(this).get(0).checked = true;
		ccm_versionsChecked++;
	});
	ccm_setSelectors();

});

$("input[name=vCompare]").click(function() {
	
	var cvID2 = $("input[type=checkbox]:checked").get(0).value;
	var cvID1 = $("input[type=checkbox]:checked").get(1).value;

	$.fn.dialog.open({
		title: ccmi18n.compareVersions,
		href: CCM_TOOLS_PATH + '/versions.php?cID=<?php echo $c->getCollectionID()?>&cvID1=' + cvID1 + '&cvID2=' + cvID2 + '&vtask=compare_iframe',
		width: '85%',
		modal: false,
		height: '80%'
	});
});

$("input[name=vApprove]").click(function() {
	
	var cvID = $("input[type=checkbox]:checked").get(0).value;
	jQuery.fn.dialog.showLoader();
	$("#ccm-versions-container").load(CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1&cID=<?php echo $c->getCollectionID()?>&cvID=' + cvID + '&vtask=approve<?php echo $token?>', function() {
		jQuery.fn.dialog.hideLoader();
	});
	
});

ccm_goToVersionPage = function(p, url) {
	jQuery.fn.dialog.showLoader();
	var dest = CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1&cID=<?php echo $c->getCollectionID()?>&ccm_paging_p=' + p;
	$("#ccm-versions-container").load(dest, function() {
		jQuery.fn.dialog.hideLoader();
	});
	return false;
}

$("input[name=vRemove]").click(function() {

	jQuery.fn.dialog.showLoader();
	
	var cvIDs = $("input[type=checkbox]:checked");
	var cvIDStr = '';
	for (i = 0; i < cvIDs.length; i++) {
		cvIDStr += "_";
		cvIDStr += cvIDs.get(i).value;
	}
	
	if (cvIDStr != '') {
		cvIDStr = cvIDStr.substring(1);
	}
	
	//ccm_showTopbarLoader();
	var params = {
		'vtask': 'remove_group',
		'ccm_token': '<?php echo $valt->generate()?>',
		'cID': <?php echo $c->getCollectionID()?>,
		'cvIDs': cvIDStr
	}
	
	$("#ccm-versions-container").load(CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1', params, function() {
		jQuery.fn.dialog.hideLoader();
	});
	
});


</script>
<div class="ccm-pane-controls">
<div id="ccm-edit-collection">

<h1><?php echo t('Page Versions')?></h1>
<p><?php echo t("The following is a list of all this page's versions. If you can edit a page you will automatically see its most recent version, but the approved version is what regular users will see.")?></p>

<div class="ccm-form-area">


	<?php  if ($isCheckedOut) { ?> 
		<?php echo t('Someone has already checked out this page for editing.')?>
	<?php  } else { ?>
	
	
	<form>
	<?php echo t('Select')?>: <a id="ccm-version-select-none" href="#"><?php echo t('None')?></a> | <a id="ccm-version-select-old" href="#"><?php echo t('Old Versions')?></a>
	&nbsp;&nbsp;
	<input type="button" name="vCompare" value="<?php echo t('Compare')?>" disabled />
	&nbsp;
	<input type="button" name="vApprove" value="<?php echo t('Approve')?>" disabled />
	
	&nbsp;
	<input type="button" name="vRemove" value="<?php echo t('Remove')?>" disabled />
	
	</form>
	<br/>
	<table border="0" cellspacing="0" width="100%" class="ccm-grid" cellpadding="0">
	<tr>
		<th>&nbsp;</th>
		<th><?php echo t('Name')?></th>
		<th><?php echo t('Comments')?></th>
		<th><?php echo t('Creator')?></th>
		<th><?php echo t('Approver')?></th>
		<th><?php echo t('Added On')?></th>
	</tr>
	<?php  
	$vIsPending = true;
	foreach ($vArray as $v) { 
		if ($v->isApproved()) {
			$vIsPending = false;
		}
		
		if ($vrAlt) { 
			$class = 'ccm-row-alt ';
			$vrAlt = false;
		} else {
			$class = '';
			$vrAlt = true;
		}
		
		if ($vIsPending) {
			$class .= 'version-pending';
		} else if ($v->isApproved()) {
			$class .= "version-active";
		}
		
	?> 
	<tr id="ccm-version-row<?php echo $v->getVersionID()?>" class="<?php echo $class?>">
		<td><input type="checkbox" <?php  if ($vIsPending) { ?> class="cb-version-pending"<?php  } else if ($v->isApproved()) { ?> class="cb-version-active"<?php  } else { ?> class="cb-version-old" <?php  } ?> id="cb<?php echo $v->getVersionID()?>" name="vID[]" value="<?php echo $v->getVersionID()?>" /></td>
		<td><a dialog-width="85%" dialog-height="80%" title="<?php echo t('Compare Versions')?>" class="ccm-version" dialog-modal="false" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&cvID=<?php echo $v->getVersionID()?>&vtask=view_version"><?php echo $v->getVersionName()?></a></td>
		<td><?php echo $v->getVersionComments()?></td>
		<td><?php 
			print $v->getVersionAuthorUserName();
			
			?></td>
		<td><?php 
			print $v->getVersionApproverUserName();
			
			?></td>
		<td><?php echo date(DATE_APP_PAGE_VERSIONS, strtotime($v->getVersionDateCreated('user')))?></td>
	</tr>	
	<?php  } ?>
	</table>
	<?php  if ($total > 20 ) { ?>
		<div class="ccm-pagination" style="margin-top: 8px">
			<span class="ccm-page-left"><?php echo $ph->getPrevious()?></span>
			<span class="ccm-page-right"><?php echo $ph->getNext()?></span>
			<?php echo $ph->getPages()?>
		</div>
	<?php  } ?>
	<br>
	
	<h2><?php echo t('Pending Actions')?></h2>
	
	<?php  

	$pendingAction = $c->getPendingAction();
	switch($pendingAction) {
		case 'DELETE': 
			$ud = UserInfo::getByID($c->getPendingActionUserID());
			$children = $c->getNumChildren();
			$pages = $children + 1;
			?>

			<div>
				<strong class="important"><?php echo t('DELETION')?></strong>
				<?php echo t('(Marked by: <strong>%s</strong> on <strong>%s</strong>)',$ud->getUserName(), date(DATE_APP_PAGE_VERSIONS, strtotime($c->getPendingActionDateTime())))?>
			</div>

			<?php  if ($cp->canApproveCollection()) { ?>
				<?php  if ($children == 0) { ?>
				
					<div class="ccm-buttons">
					<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&ctask=approve_pending_action<?php echo $token?>" class="ccm-button-right accept" onclick="return ccm_runAction(this)"><span><?php echo t('Approve')?></span></a>
					<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&ctask=clear_pending_action<?php echo $token?>" class="ccm-button-left cancel" onclick="return ccm_runAction(this)"><span><em class="ccm-button-close"><?php echo t('Deny')?></em></span></a>
					</div>
			
				<?php  } else if ($children > 0) { ?>
					<?php echo t('This will remove %s pages.',$pages)?>
					<?php  if (!$cp->canAdminPage()) { ?>
						<?php echo t('Only the super user may remove multiple pages.')?><br>
						<div class="ccm-buttons">
						<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&ctask=clear_pending_action<?php echo $token?>" class="ccm-button-left cancel" onclick="return ccm_runAction(this)"><span><em class="ccm-button-close"><?php echo t('Deny')?></em></span></a>
						</div>

					<?php  } else { ?>
						<div class="ccm-buttons">
						<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&ctask=approve_pending_action<?php echo $token?>" class="ccm-button-right accept" onclick="return ccm_runAction(this)"><span><?php echo t('Approve')?></span></a>
						<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&ctask=clear_pending_action<?php echo $token?>" class="ccm-button-left cancel" onclick="return ccm_runAction(this)"><span><em class="ccm-button-close"><?php echo t('Deny')?></em></span></a>
						</div>

					<?php  } ?>
				<?php  } ?>
			<?php  } ?>

		<?php  break;
		case 'MOVE':
			$ud = UserInfo::getByID($c->getPendingActionUserID());
			?>

			<div>
				<strong class="important"><?php echo t('MOVE')?></strong>  
				<?php echo t('(Marked by: <strong>%s</strong> on <strong>%s</strong>)',$ud->getUserName(), date(DATE_APP_PAGE_VERSIONS, strtotime($c->getPendingActionDateTime()) ))?>
			</div>
			<?php  $nc = Page::getByID($c->getPendingActionTargetCollectionID(), 'ACTIVE'); ?>
				<?php  if (is_object($nc)) { ?>
					<br><?php echo t('This page is being moved to')?> <strong><a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $nc->getCollectionID()?>" target="_blank"><?php echo $nc->getCollectionName()?></a></strong>
				<?php  } 
			?>
			<?php  if ($cp->canApproveCollection()) { ?>
				<div class="ccm-buttons">
				<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&ctask=approve_pending_action<?php echo $token?>" class="ccm-button-right accept" onclick="return ccm_runAction(this)"><span><?php echo t('Approve')?></span></a>
				<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&ctask=clear_pending_action<?php echo $token?>" class="ccm-button-left cancel" onclick="return ccm_runAction(this)"><span><em class="ccm-button-close"><?php echo t('Deny')?></em></span></a>
				</div>
			<?php  } ?>
		<?php  break;
		default: ?>
			
			<?php echo t('There are no pending actions for this page.')?>
			
		<?php  break;
		
		}
	
		}

?>
</div>

<div class="ccm-spacer">&nbsp;</div>

</div>
</div>

<?php  if (!$_GET['versions_reloaded']) { ?>
</div>
<?php  } ?>
