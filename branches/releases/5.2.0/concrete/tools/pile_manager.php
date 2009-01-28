<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
if (!$u->isRegistered()) {
	die(_("Access Denied."));
}
Loader::model('pile');
$p = false;
if ($_REQUEST['btask'] == 'add' || $_REQUEST['ctask'] == 'add') {
	// add a block to a pile
	$c = Page::getByID($_REQUEST['cID']);
	$cp = new Permissions($c);
	if (!$cp->canRead()) {
		exit;
	}
	if ($_REQUEST['btask'] == 'add') {
		$a = Area::get($c, $_REQUEST['arHandle']);
		$b = Block::getByID($_REQUEST['bID'], $c, $a);
		$ap = new Permissions($a);
		if (!$ap->canRead()) {
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
			if (!$cp->canRead()) {
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
			if (!$cp->canRead()) {
				exit;
			}
			$a = Area::get($c, $_REQUEST['arHandle']);
			$ap = new Permissions($a);
			if (!$ap->canRead() || !$ap->canAddBlocks()) {
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
			
				header('Location: ' . $_SERVER['PHP_SELF'] . '?pID=' . $p->getPileID() . '&cID=' . $_REQUEST['cID'] . '&arHandle=' . $_REQUEST['arHandle']);
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

$sp = Pile::getDefault();

?>

<?php echo t('Block added to scrapbook.')?>

<br/><br/>

<a href="javascript:void(0)" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?php echo t('Close Window')?></em></span></a>