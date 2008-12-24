<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$valt = Loader::helper('validation/token');
$sh = Loader::helper('concrete/dashboard/sitemap');
$dh = Loader::helper('concrete/dashboard');


$token = '&' . $valt->getParameter();
if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	$statusMessage = '';
	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
			$statusMessage .= t("Another user is currently editing this page.");
		}
	}
	
	if ($c->getCollectionPointerID() > 0) {
		$statusMessage .= t("This page is an alias of one that actually appears elsewhere. ");
		$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent'>" . t('View/Edit Original') . "</a>";
		if ($cp->canApproveCollection()) {
			$statusMessage .= "&nbsp;|&nbsp;";
			$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token . "'>" . t('Remove Alias') . "</a>";
		}
	} else {
	
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) {
				$statusMessage .= t("This page is pending approval.");
				if ($cp->canApproveCollection() && !$c->isCheckedOut()) {
					$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token . "'>" . t('Approve Version') . "</a>";
				}
			}
		}
		
		$pendingAction = $c->getPendingAction();
		if ($pendingAction == 'MOVE') {
			$statusMessage .= $statusMessage ? "&nbsp;|&nbsp;" : "";
			$statusMessage .= t("This page is being moved.");
			if ($cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>" . t('Approve Move') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		} else if ($pendingAction == 'DELETE') {
			$statusMessage .= $statusMessage ? "<br/>" : "";
			$statusMessage .= t("This page is marked for removal.");
			$children = $c->getNumChildren();
			if ($children > 0) {
				$pages = $children + 1;
				$statusMessage .= " " . t('This will remove %s pages.', $pages);
				if ($cp->canAdminPage()) {
					$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
				} else {
					$statusMessage .= " " . t('Only administrators can approve a multi-page delete operation.');
				}
			} else if ($children == 0 && $cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		}
	
	}

	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage()) { ?>

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.form.2.0.2.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.ui.1.5.2.no_datepicker.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.ui.datepicker.js"></script>
<? if (LANGUAGE != 'en') { ?>
	<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/i18n/ui.datepicker-<?=LANGUAGE?>.js"></script>
<? } ?>
<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>

</script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.dialog.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.ui.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/ccm.themes.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/tiny_mce_309/tiny_mce.js"></script>

<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.dialog.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.ui.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.calendar.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.menus.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.forms.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.asset.library.css";</style>

<div id="ccm-overlay"></div>
<div id="ccm-page-controls">
<div id="ccm-logo-wrapper"><img src="<?=ASSETS_URL_IMAGES?>/logo_menu.png" width="49" height="49" id="ccm-logo" /></div>
<!--<img src="<?=ASSETS_URL_IMAGES?>/logo_menu_throbber.gif" width="38" height="43" id="ccm-logo-loading" />//-->

<div id="ccm-system-nav-wrapper1">
<div id="ccm-system-nav-wrapper2">
<ul id="ccm-system-nav">
<? if ($dh->canRead()) { ?>
	<li><a id="ccm-nav-dashboard" href="<?=$this->url('/dashboard')?>"><?=t('Dashboard')?></a></li>
<? } ?>
<li><a id="ccm-nav-help" helpurl="<?=MENU_HELP_URL?>" href="javascript:void(0)" ><?=t('Help')?></a></li>
<li class="ccm-last"><a id="ccm-nav-logout" href="<?=$this->url('/login', 'logout')?>"><?=t('Sign Out')?></a></li>
</ul>
</div>
</div>

<ul id="ccm-main-nav">
<? if ($c->isArrangeMode()) { ?>
<li><a href="#" id="ccm-nav-save-arrange"><?=t('Save Positioning')?></a></li>
<? } else if ($c->isEditMode()) { ?>
<li><a href="javascript:void(0)" id="ccm-nav-exit-edit"><?=t('Exit Edit Mode')?></a></li>
<li><a href="javascript:void(0)" id="ccm-nav-properties"><?=t('Properties')?></a></li>
<? if ($cp->canAdminPage()) { ?>
<li><a href="javascript:void(0)" id="ccm-nav-design"><?=t('Design')?></a></li>
<li><a href="javascript:void(0)" id="ccm-nav-permissions"><?=t('Permissions')?></a></li>
<? } ?>
<? if ($cp->canReadVersions()) { ?><li><a href="javascript:void(0)" id="ccm-nav-versions"><?=t('Versions')?></a></li><? } ?>
<? if ($sh->canRead() || $cp->canDeleteCollection()) { ?>
	<li><a href="javascript:void(0)" id="ccm-nav-mcd"><?=t('Move/Delete')?></a></li>
<? } ?>
<? } else { ?>
<li><? if ($cantCheckOut) { ?><span id="ccm-nav-edit"><?=t('Edit Page')?></span><? } else { ?><a href="javascript:void(0)" id="ccm-nav-edit"><?=t('Edit Page')?></a><? } ?></li>
<? if ($cp->canAddSubContent()) { ?>
	<li><a href="javascript:void(0)" id="ccm-nav-add"><?=t('Add Page')?></a></li>
<? } ?>
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
<? } ?>

<?
if ($statusMessage != '') {?>
<div id="ccm-notification"><div id="ccm-notification-inner"><?=$statusMessage?></div></div>
<? } ?>

<?
	}
} ?>