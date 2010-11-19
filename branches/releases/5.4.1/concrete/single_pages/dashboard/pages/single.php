<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper('concrete/interface');
Loader::model('single_page');
$valt = Loader::helper('validation/token');
if ($_REQUEST['p'] && $_REQUEST['task'] == 'refresh' && $valt->validate('refresh')) { 
	$p = SinglePage::getByID($_REQUEST['p']);
	$p->refresh();
	$this->controller->redirect('/dashboard/pages/single?refreshed=1');
	exit;
}

if ($_POST['add_static_page']) {
	if ($valt->validate("add_single_page")) {
		$pathToNode = SinglePage::getPathToNode($_POST['pageURL'], false);
		$path = SinglePage::sanitizePath($_POST['pageURL']);
		
		if (strlen($pathToNode) > 0) {
			// now we check to see if this is already added
			$pc = Page::getByPath('/' . $path, 'RECENT');
			
			if ($pc->getError() == COLLECTION_NOT_FOUND) {
				SinglePage::add($_POST['pageURL']);
				$this->controller->redirect('/dashboard/pages/single?page_created=1');
			} else {
				$error[] = t("That page has already been added.");
			}
		} else {
			$error[] = t('That specified path doesn\'t appear to be a valid static page.');
		}
	} else {
		$error[] = $valt->getErrorMessage();
	}
}
$generated = SinglePage::getList();
 if ($_REQUEST['refreshed']) {
	$message = t('Page refreshed.');
} else if ($_REQUEST['page_created']) {
	$message = t('Static page created.');
}
?>
<h1><span><?php echo t('Single Pages')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
	<tr>
		<td colspan="4" class="header"><?php echo t('Already Installed')?></td>
	</tr>
	<tr>
		<td class="subheader" width="100%"><?php echo t('Name')?></td>
		<td class="subheader"><?php echo t('Path')?></td>
		<td class="subheader"><?php echo t('Package')?></td>
		<td class="subheader"><div style="width: 90px"></div></td>
	</tr>
	<?php  if (count($generated) == 0) { ?>
		<tr><td colspan="4"><?php echo t('No pages found.')?></td></tr>
	<?php  } else { ?>
	
	<?php  foreach ($generated as $p) { ?>
	<?php 
		if ($p->getPackageID() > 0) {
			$package = Package::getByID($p->getPackageID());
			if(is_object($package)) {
				$packageHandle = $package->getPackageHandle();
				$packageName = $package->getPackageName();
			}
		} else {
			$packageName = t('None');
		}
		
	?>
	<tr <?php  if ($packageHandle == DIRNAME_PACKAGE_CORE) { ?> class="ccm-core-package-row" <?php  } ?>>
		<td><a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $p->getCollectionID()?>"><?php echo $p->getCollectionName()?></a></td>
		<td><?php echo $p->getCollectionPath()?></td>
		<td><?php  print $packageName; ?></td>
		<td>
			<?php  print $ih->button(t('Refresh'),$this->url('/dashboard/pages/single/?p=' . $p->getCollectionID() . '&task=refresh&' . $valt->getParameter('refresh')), 'left', false, array('title'=>t('Refreshes the page, rebuilding its permissions and its name.')));?>
		</td>
	</tr>
	<?php  }
	
	} ?>
	<tr>
		<td colspan="4" class="header"><?php echo t('Add Single Page')?></td>
	</tr>
	<tr>
		<td colspan="4"><?php echo t('The page you want to add is available at:')?>
		<br>
		<form method="post" id="add_static_page_form" action="<?php echo $this->url('/dashboard/pages/single/')?>">
		<?php echo $valt->output('add_single_page')?>
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td>
		<?php echo BASE_URL . DIR_REL?>/<input type="text" name="pageURL" value="<?php echo $_POST['pageURL']?>" style="width: 200px" /></td>
		<td>
		<?php  print $ih->submit(t('Add'), 'add_static_page_form', 'left');?></td>
		</tr>
		</table>
		
		<input type="hidden" value="1" name="add_static_page" />
		</form>
		</td>
	</tr>
	
	
	</table>
	</div>
	</div>
