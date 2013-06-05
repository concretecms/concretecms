<?

defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if (!$cp->canEditPageProperties() && (!$cp->canEditPageContents())) {
	die(t("Access Denied."));
}

$v = CollectionVersion::get($c, "RECENT", true);

if ($cp->canApprovePageVersions()) {
	$approveChecked = "";
	if (isset($_SESSION['checkInApprove'])) {
		if ($_SESSION['checkInApprove'] == true) {
			$approveChecked = " checked";
		}
	}
}

Loader::element('pane_header', array('c'=>$c)); 
?>
<div class="ccm-pane-controls">
    <div id="ccm-edit-collection">
        <form method="post" id="ccm-check-in" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-in">
        	<? $valt = Loader::helper('validation/token'); $valt->output(); ?>
            <h1><?=t('Exit Edit Mode')?></h1>
            
            <div class="ccm-form-area">
                <div class="ccm-field">
                    <h2><?=t('Version Comments')?></h2>
                    <input type="text" class="ccm-input-text" name="comments" id="ccm-check-in-comments" value="<?=$v->getVersionComments()?>" style="width:565px"/>
                </div>
                <div class="ccm-buttons">
                    <? if ($cp->canApprovePageVersions()) { ?>
                    <a href="javascript:void(0)" id="ccm-check-in-publish" class="ccm-button-right accept"><span><?=t('Publish My Edits')?></span></a>
                    <? } ?>
                    <a href="javascript:void(0)" id="ccm-check-in-preview" class="ccm-button-right accept" style="margin-right: 5px"><span><?=t('Preview My Edits')?></span></a>
                    <? if ($v->canDiscard()) { ?>
                    	<a href="javascript:void(0)" id="ccm-check-in-discard" class="ccm-button-left"><span><?=t('Discard My Edits')?></span></a>
                    <? } ?>
                </div>
                <input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />        
	        	<div class="ccm-spacer">&nbsp;</div>
            </div>        
        </form>
       
        <script type="text/javascript">
        $(function() {
            setTimeout("$('#ccm-check-in-comments').focus();",300);
            $("#ccm-check-in-preview").click(function() {
                $("#ccm-approve-field").val('PREVIEW');
                $("#ccm-check-in").submit();
            });
        
            $("#ccm-check-in-discard").click(function() {
                $("#ccm-approve-field").val('DISCARD');
                $("#ccm-check-in").submit();
            });
        
            $("#ccm-check-in-publish").click(function() {
                $("#ccm-approve-field").val('APPROVE');
                $("#ccm-check-in").submit();
            });
        });
        </script>
    </div>
</div>