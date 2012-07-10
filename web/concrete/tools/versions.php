<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$valt = Loader::helper('validation/token');
	$fh = Loader::helper('file');
	
	$token = '&' . $valt->getParameter();
	
	$c = Page::getByID($_REQUEST['cID']);
	$cID = $c->getCollectionID();
	$cp = new Permissions($c);
	$u = new User();
	$isCheckedOut = $c->isCheckedOut() && !$c->isEditMode();
	
	if (!$cp->canViewPageVersions() && !$cp->canApprovePageVersions()) {
		die(t("Access Denied."));
	}
	
	if ($_GET['vtask'] == 'view_version') { ?>
		<? /*
		we use the always-updated ID below so that Safari doesn't cache the iframe's contents. We probably shouldn't be
		making a new iframe on every request to this anyway, but it doesn't happen very often and it represents a significant
		hurdle to making it a bit of a better citizen, so we'll do it this way for now.
		
		*/
		?>
		
		<iframe border="0" id="v<?=time()?>" frameborder="0" height="100%" width="100%" src="<?=BASE_URL . DIR_REL?>/<?=DISPATCHER_FILENAME?>?cvID=<?=$_REQUEST['cvID']?>&cID=<?=$_REQUEST['cID']?>&vtask=view_versions" />
	
	<? 
		exit;
	}
	
	if (isset($_GET['cvID1']) && isset($_GET['cvID2']) && (isset($_GET['vtask']))) {
		
		if ($_GET['vtask'] == 'compare') {
			
			if ($_GET['cvID2'] > $_GET['cvID1']) { 
				$newCVID = $_GET['cvID2'];
				$oldCVID = $_GET['cvID1'];
			} else {
				$newCVID = $_GET['cvID1'];
				$oldCVID = $_GET['cvID2'];
			}
			// compare
			$src1 = time() . '_' . $_GET['cID'] . '_' . $oldCVID . '.html';
			$src2 = time() . '_' . $_GET['cID'] . '_' . $newCVID . '.html';
			
			$c = Page::getByID($_GET['cID'], $oldCVID);
			$v = View::getInstance();
			$v->disableEditing();
			$v->disableLinks();

			ob_start();
			$v->render($c);
			$ret = ob_get_contents();
			ob_end_clean();

			file_put_contents($fh->getTemporaryDirectory() . '/' . $src1, $ret);
			
			$c->refreshCache();
			
			$c = Page::getByID($_GET['cID'], $newCVID);
			$v = View::getInstance();
			$v->disableEditing();
			$v->disableLinks();
			ob_start();
			$v->render($c);
			$ret = ob_get_contents();
			ob_end_clean();

			file_put_contents($fh->getTemporaryDirectory() . '/' . $src2, $ret);
			
			if (is_executable(DIR_FILES_BIN_HTMLDIFF)) {
				$val = system(DIR_FILES_BIN_HTMLDIFF . ' ' . escapeshellcmd($fh->getTemporaryDirectory() . '/' . $src1) . ' ' . escapeshellcmd($fh->getTemporaryDirectory() . '/' . $src2));
				$val = str_replace($val, '</head>', '<style type="text/css">@import "' . ASSETS_URL_CSS . '/ccm.compare.css";</style></head>');
				print $val;
			} else {
				print t('You must make %s executable in order to compare versions of pages.',DIR_FILES_BIN_HTMLDIFF);
			}
			exit;
		
		} else if ($_GET['vtask'] == 'compare_iframe') { ?>
		
			<iframe id="v<?=time()?>" border="0" frameborder="0" height="100%" width="100%" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cvID1=<?=$_REQUEST['cvID1']?>&cvID2=<?=$_REQUEST['cvID2']?>&vtask=compare&cID=<?=$_REQUEST['cID']?>" />
			
		
		<? }
		
		exit;
		
	}
	
	if (!$isCheckedOut) {
		
		if ($valt->validate()) {
			switch($_REQUEST['vtask']) {
				case 'remove_group':
					if ($cp->canApprovePageVersions() && !$isCheckedOut) {
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
					if ($cp->canApprovePageVersions() && !$isCheckedOut) {
						$u = new User();
						$pkr = new ApprovePagePageWorkflowRequest();
						$pkr->setRequestedPage($c);
						$v = CollectionVersion::get($c, $_GET['cvID']);
						$pkr->setRequestedVersionID($v->getVersionID());
						$pkr->setRequesterUserID($u->getUserID());
						$u->unloadCollectionEdit($c);
						$response = $pkr->trigger();
						if (!($response instanceof WorkflowProgressResponse)) {
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&deferred=true&cID=" . $cID . "&cvID=" . $_GET['cvID']);
							exit;
						} else {
							// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&cID=" . $cID . "&cvID=" . $_GET['cvID']);
							exit;
						}
					}
					break;
			}
			
		}
		
		$page = $_REQUEST[PAGING_STRING];
		if (!$page) {
			$page = 1;
		}
		$vl = new VersionList($c,20, $page);
		$total = $vl->getVersionListCount();
		$vArray = $vl->getVersionListArray();
		$ph = Loader::helper('pagination');
		$ph->init($page, $total, '',20, 'ccm_goToVersionPage');
	}



if (!$_GET['versions_reloaded']) { ?>
	<div id="ccm-versions-container">
	<? if ($_REQUEST['deferred']) { ?>
		<div class="alert alert-info">
			<?=t('<strong>Request Saved.</strong> You must complete the workflow before this change is active.')?>
		</div>
	<? } ?>
<? } ?>

<div class="ccm-pane-controls ccm-ui">

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
	
	<? if ($_REQUEST['forcereload']) { ?>
		ccm_versionsMustReload = true;
	<? } ?>

});

ccm_setSelectors = function() {
	if (ccm_versionsChecked < 0) {
		ccm_versionsChecked = 0;
	}
	
	/* first, we grab whether an active version is checked, so we can use that later */
	var isActiveChecked = ( $("input.cb-version-active:checked").length > 0 );
	
	/* if two and only two are checked, we can compare */
	
	if (ccm_versionsChecked == 2) {
		$("input[name=vCompare]").prop('disabled', false);
	} else {
		$("input[name=vCompare]").prop('disabled', true);
	}
	
	
	if (ccm_versionsChecked > 0 && (!isActiveChecked)) {
		$("input[name=vRemove]").prop('disabled', false);
	} else {
		$("input[name=vRemove]").prop('disabled', true);
	}
	
	if (ccm_versionsChecked == 1 && (!isActiveChecked)) {
		$("input[name=vApprove]").prop('disabled', false);
	} else {
		$("input[name=vApprove]").prop('disabled', true);
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
		jQuery.fn.dialog.closeTop();
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
		href: CCM_TOOLS_PATH + '/versions.php?cID=<?=$c->getCollectionID()?>&cvID1=' + cvID1 + '&cvID2=' + cvID2 + '&vtask=compare_iframe',
		width: '85%',
		modal: false,
		height: '80%'
	});
});

$("input[name=vApprove]").click(function() {
	
	var cvID = $("table#ccm-versions-list input[type=checkbox]:checked").get(0).value;
	jQuery.fn.dialog.showLoader();
	$.get(CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1&cID=<?=$c->getCollectionID()?>&cvID=' + cvID + '&vtask=approve<?=$token?>', function(r) {	
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
	
});

ccm_goToVersionPage = function(p, url) {
	jQuery.fn.dialog.showLoader();
	var dest = CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1&cID=<?=$c->getCollectionID()?>&<?php echo PAGING_STRING?>' + p;
	$.get(dest, function(r) {
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
	return false;
}

$("input[name=vRemove]").click(function() {

	jQuery.fn.dialog.showLoader();
	
	var cvIDs = $("table#ccm-versions-list input[type=checkbox]:checked");
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
		'ccm_token': '<?=$valt->generate()?>',
		'cID': <?=$c->getCollectionID()?>,
		'cvIDs': cvIDStr
	}
	
	$.get(CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1', params, function(r) {
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
	
});


</script>

<p><?=t("The following is a list of all this page's versions. If you can edit a page you will automatically see its most recent version, but the approved version is what regular users will see.")?></p>



	<? if ($isCheckedOut) { ?> 
		<?=t('Someone has already checked out this page for editing.')?>
	<? } else { ?>
	
	
	<div class="btn-group" style="float: left">
		<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
		<?=t('Select')?>
		<span class="caret"></span>
  		</a>
		<ul class="dropdown-menu">
		<li><a id="ccm-version-select-none" href="#"><?=t('None')?></a></li>
		<li><a id="ccm-version-select-old" href="#"><?=t('Old Versions')?></a></li>
		</ul>
	
	</div>
	
	<div class="btn-group" style="float: right">
	<?
	$ih = Loader::helper("concrete/dashboard");
	if (!$ih->inDashboard($c)) { ?><input class="btn" type="button" name="vCompare" value="<?=t('Compare')?>" disabled /><? } ?>
	<input class="btn" type="button" name="vApprove" value="<?=t('Approve')?>" disabled />
	<input class="btn" type="button" name="vRemove" value="<?=t('Remove')?>" disabled />
	</div>

	<br/><br/>

	<table border="0" cellspacing="0" width="100%" class="table table-striped" cellpadding="0" id="ccm-versions-list">
	<tr>
		<th>&nbsp;</th>
		<th><?=t('Name')?></th>
		<th><?=t('Comments')?></th>
		<th><?=t('Creator')?></th>
		<th><?=t('Approver')?></th>
		<th class="headerSortDown"><?=t('Added On')?></th>
	</tr>
	<? 
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
	<tr id="ccm-version-row<?=$v->getVersionID()?>" class="<?=$class?>">
		<td><input type="checkbox" <? if ($vIsPending) { ?> class="cb-version-pending"<? } else if ($v->isApproved()) { ?> class="cb-version-active"<? } else { ?> class="cb-version-old" <? } ?> id="cb<?=$v->getVersionID()?>" name="vID[]" value="<?=$v->getVersionID()?>" /></td>
		<td><a dialog-width="85%" dialog-height="80%" title="<?=t('Compare Versions')?>" class="ccm-version" dialog-title="<?=t('Compare Versions')?>" dialog-modal="false" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?=$cID?>&cvID=<?=$v->getVersionID()?>&vtask=view_version"><?=$v->getVersionName()?></a></td>
		<td><?=$v->getVersionComments()?></td>
		<td><?
			print $v->getVersionAuthorUserName();
			
			?></td>
		<td><?
			print $v->getVersionApproverUserName();
			
			?></td>
		<td><?=date(DATE_APP_PAGE_VERSIONS, strtotime($v->getVersionDateCreated('user')))?></td>
	</tr>	
	<? } ?>
	</table>
	<? if ($total > 20 ) { ?>
	<div class="ccm-ui">
		<div class="pagination ccm-pagination">
		<ul>
			<li class="prev"><?=$ph->getPrevious()?></li>
			<?=$ph->getPages('li'); ?>
			<li class="next"><?=$ph->getNext()?></li>
		</ul>
		</div>
	</div>
	<? } ?>
	<br>
	
<? 	}

?>

</div>

<? if (!$_GET['versions_reloaded']) { ?>
</div>
<? } ?>
