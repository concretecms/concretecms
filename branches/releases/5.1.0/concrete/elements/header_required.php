<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;
global $cp;

if (is_object($c)) {
	$pageTitle = (!$pageTitle) ? $c->getCollectionName() : $pageTitle;
	$pageDescription = (!$pageDescription) ? $c->getCollectionDescription() : $pageDescription;
	$cID = $c->getCollectionID(); 
	$isEditMode = ($c->isEditMode()) ? "true" : "false";
	$isArrangeMode = ($c->isArrangeMode()) ? "true" : "false";
	
} else {
	$cID = 1;
}
?>

<?php 
$akt = $c->getCollectionAttributeValue('meta_title'); 
$akd = $c->getCollectionAttributeValue('meta_description');
$akk = $c->getCollectionAttributeValue('meta_keywords');
if ($akt) { 
	$pageTitle = $akt; ?>
	<title><?php echo $akt?></title>
<?php  } else { 
	$pageTitle = $c->getCollectionName();
	?>
	<title><?php echo SITE . ' :: ' . $pageTitle?></title>
<?php  } 

if ($akd) { ?>
	<meta name="description" content="<?php echo htmlspecialchars($akd)?>" />
<?php  } else { ?>	
	<meta name="description" content="<?php echo htmlspecialchars($pageDescription)?>" />
<?php  }

if ($akk) { ?>
	<meta name="keywords" content="<?php echo htmlspecialchars($akk)?>" />
<?php  } ?>

<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<?php  $u = new User(); ?>
<script type="text/javascript">
<?php 
	if ($u->config('UI_BREADCRUMB')) { 
		echo("var CCM_ENABLE_BREADCRUMB = true;\r");
	} else {
		echo("var CCM_ENABLE_BREADCRUMB = false;\r");
	}
	echo("var CCM_DISPATCHER_FILENAME = '" . DIR_REL . '/' . DISPATCHER_FILENAME . "';\r");
	echo("var CCM_CID = {$cID};\r");
	if (MENU_FEEDBACK_DISPLAY) {
		echo("var CCM_FEEDBACK = true;\r");
	} else {
		echo("var CCM_FEEDBACK = false;\r");
	}
	if (isset($isEditMode)) {
		echo("var CCM_EDIT_MODE = {$isEditMode};\r");
	}
	if (isset($isEditMode)) {
		echo("var CCM_ARRANGE_MODE = {$isArrangeMode};\r");
	}
?>
var CCM_IMAGE_PATH = "<?php echo ASSETS_URL_IMAGES?>";
var CCM_TOOLS_PATH = "<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>";
var CCM_REL = "<?php echo DIR_REL?>";

</script>
<script type="text/javascript" src="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/i18n_js"></script>
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/jquery1.2.6.js"></script>
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/swfobject2.1.js"></script>

<?php  
// output header items
print $this->controller->outputHeaderItems();
?>

<?php  
	if (is_object($cp)) {
		$v = View::getInstance();

		if ($v->editingEnabled()) {
			require(DIR_FILES_ELEMENTS_CORE . '/page_controls.php');
		}
	}
?>