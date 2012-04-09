<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?=APP_CHARSET?>" />
<!-- insert CSS for Default Concrete Theme //-->
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.default.theme.css";</style>
<style type="text/css">@import "<?=ASSETS_URL_CSS?>/ccm.app.css";</style>

</head>
<body>

<div id="ccm-logo"><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></div>

<div id="ccm-theme-wrapper" class="ccm-ui">
<?				Loader::element('error_fatal', array('innerContent' => $innerContent, 
					'titleContent' => $titleContent));
?>
<p><a href="<?=DIR_REL?>" class="btn"><?=t('&lt; Back to Home')?></a></p>
</div>

</body>
</html>