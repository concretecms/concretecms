<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- insert CSS for Default Concrete Theme //-->
<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.default.theme.css";</style>
<?php  
if (is_object($c)) {
	$v = View::getInstance();
	$v->disableEditing();
 	Loader::element('header_required');
} else { 
	print Loader::helper('html')->javascript('jquery.js');
}
?>
</head>
<body>

<div id="ccm-logo"><img src="<?php echo ASSETS_URL_IMAGES?>/logo_menu.png" width="49" height="49" alt="Concrete CMS" /></div>

<div id="ccm-theme-wrapper">

<?php  if (isset($error) && $error != '') { ?>
	<?php  
	if ($error instanceof Exception) {
		$_error[] = $error->getMessage();
	} else if ($error instanceof ValidationErrorHelper) { 
		$_error = $error->getList();
	} else if (is_array($error)) {
		$_error = $error;
	} else if (is_string($error)) {
		$_error[] = $error;
	}
	
		?>
		<ul class="ccm-error">
		<?php  foreach($_error as $e) { ?><li><?php echo $e?></li><?php  } ?>
		</ul>
	<?php  
} ?>

<?php  print $innerContent ?>

</div>

</body>
</html>