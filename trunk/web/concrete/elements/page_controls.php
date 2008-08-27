<?
if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	$statusMessage = '';
	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
			$statusMessage .= "Another user is currently editing this page.";
		}
	}
	
	if ($c->getCollectionPointerID() > 0) {
		$statusMessage .= "This page is an alias of one that actually appears elsewhere. ";
		$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent'>View/Edit Original</a>";
		if ($cp->canApproveCollection()) {
			$statusMessage .= "&nbsp;|&nbsp;";
			$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias'>Remove Alias</a>";
		}
	} else {
	
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) {
				$statusMessage .= "This page is pending approval. ";
				if ($cp->canApproveCollection() && !$c->isCheckedOut()) {
					$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent'>Approve Version</a>";
				}
			}
		}
		
		$pendingAction = $c->getPendingAction();
		if ($pendingAction == 'MOVE') {
			$statusMessage .= $statusMessage ? "&nbsp;|&nbsp;" : "";
			$statusMessage .= "This page is being moved. ";
			if ($cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>Approve Move</a>";
			}
		} else if ($pendingAction == 'DELETE') {
			$statusMessage .= $statusMessage ? "<br/>" : "";
			$statusMessage .= "Page marked for removal. ";
			$children = $c->getNumChildren();
			if ($children > 0) {
				$pages = $children + 1;
				$statusMessage .= " This will remove " . $pages . " pages.";
				if ($cp->canAdminPage()) {
					$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>Approve Delete</a>";
				} else {
					$statusMessage .= " Only the super-user may approve a multi-page delete operation.";
				}
			} else if ($children == 0 && $cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>Approve Delete</a>";
			}
		}
	
	}

	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage()) { ?>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.form.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.easing.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.dimensions.js"></script>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/ui.core.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/ui.draggable.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/ui.droppable.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/ui.sortable.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/ui.datepicker.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/effects.core.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/effects.fade.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery_ui/effects.highlight.js"></script>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.dialog.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.base.js"></script>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/tiny_mce_309/tiny_mce.js"></script>

<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_dialog.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_ui.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_calendar.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_menus.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_forms.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm_asset_library.css";</style>

<div id="ccm-overlay"></div>
<div id="ccm-page-controls">
<div id="ccm-logo-wrapper"><img src="<?=ASSETS_URL_IMAGES?>/logo_menu.png" width="49" height="49" id="ccm-logo" /></div>
<!--<img src="<?=ASSETS_URL_IMAGES?>/logo_menu_throbber.gif" width="38" height="43" id="ccm-logo-loading" />//-->

<div id="ccm-system-nav-wrapper1">
<div id="ccm-system-nav-wrapper2">
<ul id="ccm-system-nav">
<li><a id="ccm-nav-dashboard" href="<?=$this->url('/dashboard')?>">Dashboard</a></li>
<?/*
<li><? if ($c->isEditMode()) { ?><span id="ccm-nav-dashboard">Dashboard</span><? } else { ?><a id="ccm-nav-dashboard" href="<?=DIR_REL?>/dashboard/">Dashboard</a><? } ?></li>
*/?>
<li><a id="ccm-nav-help" helpurl="<?=MENU_HELP_URL?>" href="javascript:void(0)" >Help</a></li>
<? /*
<li class="ccm-last"><? if ($c->isEditMode()) { ?><span id="ccm-nav-logout">Logout</span><? } else { ?><a id="ccm-nav-logout" href="<?=DIR_REL?>/login/-/logout/">Logout</a><? } ?></li>
*/ ?>
<li class="ccm-last"><a id="ccm-nav-logout" href="<?=$this->url('/login', 'logout')?>">Logout</a></li>
</ul>
</div>
</div>

<? if ($statusMessage != '') {?>
	<div id="ccm-notification"><?=$statusMessage?></div>
<? } ?>

<ul id="ccm-main-nav">
<? if ($c->isArrangeMode()) { ?>
<li><a href="#" id="ccm-nav-save-arrange">Save Positioning</a></li>
<? } else if ($c->isEditMode()) { ?>
<li><a href="javascript:void(0)" id="ccm-nav-exit-edit">Exit Edit Mode</a></li>
<li><a href="javascript:void(0)" id="ccm-nav-properties">Properties</a></li>
<li><a href="javascript:void(0)" id="ccm-nav-design">Design</a></li>
<? if ($cp->canAdminPage()) { ?><li><a href="javascript:void(0)" id="ccm-nav-permissions">Permissions</a></li><? } ?>
<li><a href="javascript:void(0)" id="ccm-nav-versions">Versions</a></li>
<li><a href="javascript:void(0)" id="ccm-nav-mcd">Move/Delete</a></li>
<? } else { ?>
<li><? if ($cantCheckOut) { ?><span id="ccm-nav-edit">Edit Page</span><? } else { ?><a href="javascript:void(0)" id="ccm-nav-edit">Edit Page</a><? } ?></li>
<li><a href="javascript:void(0)" id="ccm-nav-add">Add Page</a></li>
<? } ?>
</ul>
</div>
<div id="ccm-page-detail"><div id="ccm-page-detail-l"><div id="ccm-page-detail-r"><div id="ccm-page-detail-content"></div></div></div>
<div id="ccm-page-detail-lower"><div id="ccm-page-detail-bl"><div id="ccm-page-detail-br"><div id="ccm-page-detail-b"></div></div></div></div>
</div>

<? if ($c->getCollectionParentID() > 0) { ?>
	<div id="ccm-bc">
	<div id="ccm-bc-inner">
	<?
		$nh = Loader::helper('navigation');
		$trail = $nh->getTrailToCollection($c);
		$trail = array_reverse($trail);
		$trail[] = $c;
	?>
	<ul>
	<? foreach($trail as $_c) { ?>
		<li><a href="#" onclick="javascript:location.href='<?=$nh->getLinkToCollection($_c)?>'"><?=$_c->getCollectionName()?></a></li>
	<? } ?>
	</ul>
	
	</div>
	</div>
<? }
	}
} ?>