<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<!-- insert CSS for Default Concrete Theme //-->
<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/jquery1.2.6.js"></script>
<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.default.theme.css";</style>

</head>
<body>

<div id="ccm-logo"><img src="<?php echo ASSETS_URL_IMAGES?>/logo_menu.png" width="49" height="49" alt="Concrete CMS" /></div>

<div id="ccm-theme-wrapper">

<?php  if (isset($error)) { ?>
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