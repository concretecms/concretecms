<?

defined('C5_EXECUTE') or die("Access Denied.");


$u = new User();
$scrapbookHelper=Loader::helper('concrete/scrapbook'); 

if (!$u->isRegistered()) {
	die(t("Access Denied."));
}
Loader::model('pile');
$p = false;
$scrapbookName=$_REQUEST['scrapbookName'];
if($scrapbookName) $scrapbookHelper->setDefault($scrapbookName);
$c = Page::getByID($_REQUEST['cID']);
// add a block to a pile	
$cp = new Permissions($c);
if (!$cp->canViewPage()) {
	exit;
}

if (($_REQUEST['btask'] == 'add' || $_REQUEST['ctask'] == 'add') && $scrapbookName) {
	
	if ($_REQUEST['btask'] == 'add') {
		$a = Area::get($c, $_REQUEST['arHandle']);
		if ($a->isGlobalArea()) {
			$ax = STACKS_AREA_NAME;
			$cx = Stack::getByName($_REQUEST['arHandle']);
		}
		$b = Block::getByID($_REQUEST['bID'], $cx, $ax);
		if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
			$bi = $b->getInstance();
			$b = Block::getByID($bi->getOriginalBlockID());
		}
		$ap = new Permissions($a);
		if (!$ap->canViewArea()) {
			exit;
		}
		$obj = &$b;
	} else {
		$obj = &$c;
	}	


	if ($_REQUEST['pID']) {
		$p = Pile::get($_REQUEST['pID']);
		if (is_object($p)) {
			if (!$p->isMyPile()) {
				unset($p);
			}
		}
	}
	if (!is_object($p)) {
		$p = Pile::getDefault();
	}
	$p->add($obj);
	
	$added = true;
	
	
	
} else {

	switch($_REQUEST['ptask']) { 
		case 'add_contents':
			$c = Page::getByID($_REQUEST['cID']);
			$cp = new Permissions($c);
			if (!$cp->canViewPage()) {
				exit;
			}
			
			if ($_REQUEST['pID']) {
				$p = Pile::get($_REQUEST['pID']);
				if (is_object($p)) {
					if (!$p->isMyPile()) {
						unset($p);
					}
				}
			}
			if (!is_object($p)) {
				$p = Pile::getDefault();
			}
			
			$a = Area::get($c, $_REQUEST['arHandle']);
			$ap = new Permissions($a);
			$aBlocks = $a->getAreaBlocksArray($c, $ap);
			foreach($aBlocks as $ab) {
				$abp = new Permissions($ab);
				if ($abp->canRead()) {
					$p->add($ab);
				}
			}
			break;
			
		case 'add_prepare':
			$c = Page::getByID($_REQUEST['cID']);
			$cp = new Permissions($c);
			if (!$cp->canViewPage()) {
				exit;
			}
			$a = Area::get($c, $_REQUEST['arHandle']);
			$ap = new Permissions($a);
			if (!$ap->canViewArea() || !$ap->canAddBlocks()) {
				exit;
			}
			break;
			
		case 'delete_content':
			if (is_array($_POST['pcID'])) {
				foreach($_POST['pcID'] as $pcID) {
					$pc = PileContent::get($pcID);
					$p = $pc->getPile();
					if ($p->isMyPile()) {
						$pc->delete();
					}
				}
			}
			
			break;
			
		case 'delete_pile':
			$p = Pile::get($_REQUEST['pID']);
			if ($p->isMyPile() && !$p->isDefault()) {
				$p->delete();
			}			
			break;
		
		case 'add_to_pile':
			if ($_REQUEST['existingPID']) {
				$p = Pile::get($_REQUEST['existingPID']);
				if (is_object($p)) {
					if (!$p->isMyPile()) {
						unset($p);
					}
				}
			}
			if (!is_object($p)) {
				$p = Pile::getDefault();
			}
			
			if (is_array($_POST['pcID'])) {
				foreach($_POST['pcID'] as $pcID) {
					$pc = PileContent::get($pcID);
					$p->add($pc);
				}
			}
			
			break;
			
		case 'create':
			if ($_REQUEST['name']) {
				$p = Pile::create($_REQUEST['name']);
				if (is_object($p) && is_array($_POST['pcID'])) {
					foreach($_POST['pcID'] as $pcID) {
						$pc = PileContent::get($pcID);
						$p->add($pc);
					}
				}

				$securityHelper = Loader::helper('security');
				$PHP_SELF = $securityHelper->sanitizeURL($_SERVER['PHP_SELF']);
				$cID = $securityHelper->sanitizeInt($_REQUEST['cID']);
				$arHandle = $securityHelper->sanitizeString($_REQUEST['arHandle']);
			
				header('Location: ' . $PHP_SELF . '?pID=' . $p->getPileID() . '&cID=' . $cID . '&arHandle=' . $arHandle);
				exit;
			}
			break;
			
		case 'output':
			$p = ($_REQUEST['pID']) ? Pile::get($_REQUEST['pID']) : Pile::getDefault();
			if (is_object($p)) {
				if ($p->isMyPile()) {
					$p->output($_REQUEST['module']);
					exit;
				}
			}
			break;
	
	}
	
}


if($_REQUEST['btask']=='add'){ 
	
	$a = Area::get($c, $_REQUEST['arHandle']);
	$b = Block::getByID($_REQUEST['bID'], $c, $a);
	
	if( !$a ){
		echo t('Error: Area not found.');
		
	}elseif( !intval($b->bID) ){
		echo t('Error: Block not found.');	
		
	}elseif( !$_REQUEST['scrapbookName'] && $_REQUEST['btask']=='add' ){
			
		$sp = Pile::getDefault();
		$scrapBookAreasData = $scrapbookHelper->getAvailableScrapbooks(); 
		$ih = Loader::helper('concrete/interface'); 
		
		$defaultScrapbook=$scrapbookHelper->getDefault(); ?>		
		<script type="text/javascript">
		if(!ccmSaveToScrapbookDialogTarget)
			var ccmSaveToScrapbookDialogTarget=null;
			
		ccmSaveToScrapbook = function(sel){
			var sel=$('#ccm-addToScrapbookName');
			var modeAliased=document.getElementById('blockAddModeAliased');
			var blockAddMode=(modeAliased.checked)?'alias':'duplicate';
			ccmSaveToScrapbookDialogTarget = sel.closest('.ccm-dialog-content'); 
			var scrapbook=sel.val(); 
			if(!scrapbook){
				alert("<?=t("Please choose a scrapbook.") ?>");
				return false;
			}
			jQuery.fn.dialog.showLoader();
			
			$.ajax({
			type: 'POST',
			url: CCM_TOOLS_PATH+"/pile_manager.php",
			data: 'cID=<?=intval($_REQUEST['cID'])?>&bID=<?=intval($_REQUEST['bID'])?>&arHandle=<?=urlencode($_REQUEST['arHandle'])?>&btask=add&scrapbookName='+scrapbook+'&blockAddMode='+blockAddMode,
			success: function(resp) { 
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
			}});		
			return false;
		}
		
		ccmShowBlockAddModeRadios=function(sel){
			if(!$(sel).val() || $(sel).val()=='userScrapbook')
				 $('#ccm-blockAddModeWrap').css('display','none');
			else $('#ccm-blockAddModeWrap').css('display','block');
		}
		</script>
		
		
		<div>
		<select id="ccm-addToScrapbookName" name="scrapbookName" onchange="ccmShowBlockAddModeRadios(this)" style="width: 205px">
			<option value="userScrapbook" <?=($defaultScrapbook==$scrapbookHelper->getPersonalScrapbookName())?'selected':''?>>
				<?=t("%s's Personal Scrapbook", $u->getUserName()) ?> 
			</option>
			<? foreach($scrapBookAreasData as $scrapBookAreaData){ ?>
				<option value="<?=addslashes($scrapBookAreaData['arHandle'])?>" 
				<?=($defaultScrapbook==$scrapBookAreaData['arHandle'])?'selected':''?> >
					<?=$scrapBookAreaData['arHandle'] ?>
				</option>
			<? } ?>
		</select> 
		</div>
		
		<div id="ccm-blockAddModeWrap" style="display:<?=($defaultScrapbook!=$scrapbookHelper->getPersonalScrapbookName())?'block':'none'?>">
			&nbsp;<br />
			<input name="blockAddMode" type="radio" value="duplicate" checked="checked" /> <?=t('New copy to Scrapbook')?><br />
			<input id="blockAddModeAliased" name="blockAddMode" type="radio" value="alias" /> <?=t('Alias original to Scrapbook')?>
		</div>
	
		<br/> 
		
		<div class="sillyIE7"><?= $ih->button_js( t('Add Block to Scrapbook'), 'ccmSaveToScrapbook()','left'); ?></div>
		
	<? }elseif($added){ ?>
		
		<br/> 
		
		<?=t('Block added to scrapbook.')?>
		
		<br/><br/>
		<div style="text-align: center">
		<img src="<?=ASSETS_URL_IMAGES?>/throbber_white_32.gif" width="32" height="32" />
		</div>
		
		<? /*<a href="javascript:void(0)" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?=t('Close Window')?></em></span></a>*/ ?>
		
	<? }
}	
?>