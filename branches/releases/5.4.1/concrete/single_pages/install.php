<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 

<?php  

$installErrorMsg = t('Unable to Install. Not all required items are available.');
$introMsg = t('To install Concrete, please fill out the form below.');
if (isset($message)) { ?>

<h1><?php echo t('Install Concrete')?></h1>

<div class="ccm-form">
<?php echo $message?>
<br/><br/>
<a href="<?php echo DIR_REL?>/"><?php echo t('Continue to your site.')?> &gt;</a>
</div>

<?php  } else { ?>
<script type="text/javascript">


activateInstallForm = function() {
	$("#ccm-install-form input").each(function() {
		$(this).attr('disabled', false);
	});
	$('#ccm-form-intro').html('<?php echo $introMsg?>');
	$("#ccm-form-intro").removeClass('ccm-error');
}

<?php  if ($this->controller->passedRequiredItems()) { ?>
	var showFormOnTestCompletion = true;
<?php  } else { ?>
	var showFormOnTestCompletion = false;
<?php  } ?>


$(function() {
	$("a.ccm-install-tooltip").click(function() {
		$(this).siblings('.ccm-install-info').show();
	});
	$("#ccm-test-js").removeClass('fail');
	$("#ccm-test-js").addClass('passed');
	$("#ccm-test-urls").ajaxError(function(event, request, settings) {
		$(this).removeClass('loading');
		$(this).addClass('fail');
	});
	$.getJSON('<?php echo $this->url("/install", "test_url", "20", "20")?>', function(json) {
		// test url takes two numbers and adds them together. Basically we just need to make sure that
		// our url() syntax works - we do this by sending a test url call to the server when we're certain 
		// of what the output will be
		if (json.response == 40) {
			$("#ccm-test-urls").removeClass('loading');
			$("#ccm-test-urls").addClass('passed');
			
			// now we check the other tests
			if (showFormOnTestCompletion) {
				activateInstallForm();
			}
		} else {
			$("#ccm-test-urls").removeClass('loading');
			$("#ccm-test-urls").addClass('fail');
		}
	});
	
});
</script>

<div id="ccm-install-intro">

<h1><?php echo t('Install Concrete')?></h1>
	
</div>

<div id="ccm-install-check">

<div id="ccm-install-check-items">
<h2 style="margin-top: 0px"><?php echo t('Testing Required Items')?></h2>
<div class="test <?php  if ($phpVtest) { ?>passed<?php  } else { ?>warning<?php  } ?>"><?php echo t('PHP 5.1 Available')?>
<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('PHP 5.2 or greater is recommended<br />
 for timezone support.')?></div>
</div>

<div id="ccm-test-js" class="test fail"><?php echo t('JavaScript Enabled')?>

<noscript>
<div class="ccm-install-info" style="display: block"><?php echo t('Please enable JavaScript in your browser.')?></div>
</noscript>

</div>


<div class="test <?php  if ($mysqlTest) { ?>passed<?php  } else { ?>fail<?php  } ?>"><?php echo t('MySQL Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo $this->controller->getDBErrorMsg()?></div>

</div>
<div class="test loading" id="ccm-test-urls"><?php echo t('Support for C5 Request URLs')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('Concrete cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.')?></div>

</div>
<div class="test <?php  if ($imageTest) { ?>passed<?php  } else { ?>fail<?php  } ?>"><?php echo t('Image Manipulation Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('Concrete requires GD library 2.0.1 or greater.')?></div>

</div>
<div class="test <?php  if ($xmlTest) { ?>passed<?php  } else { ?>fail<?php  } ?>"><?php echo t('XML Support')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('Concrete requires PHP XML Parser and SimpleXML extensions.')?></div>

</div>

<div class="test <?php  if ($fileWriteTest) { ?>passed<?php  } else { ?>fail<?php  } ?>"><?php echo t('Web Server Access to Files and Configuration Directories')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('The config/, packages/ and files/ directories must be writable by your web server.')?></div>

</div>

<h2><?php echo t('Testing Optional Items')?></h2>
<?php  /*
<div class="test <?php  if ($langTest) { ?>passed<?php  } else { ?>warning<?php  } ?>"><?php echo t('Multilingual Support')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('Multilingual support requires the gettext PHP extension and may not work with safe mode enabled.')?></div>

</div>
<div class="test <?php  if ($searchTest) { ?>passed<?php  } else { ?>warning<?php  } ?>"><?php echo t('Search Indexing Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('Search indexing requires the mbstring and iconv PHP extensions, and PCRE support.')?></div>

</div>
*/ ?>
<div class="test <?php  if ($diffTest) { ?>passed<?php  } else { ?>warning<?php  } ?>"><?php echo t('Version Comparison Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?php echo t('You must chmod 755 %s and disable PHP safe mode.', 'concrete/libraries/3rdparty/htmldiff.py')?></div>
</div>

<div style="text-align: center; margin-top: 16px">
<form action="<?php echo $this->url('/install')?>" method="get">
	<input type="submit" name="submit" class="ccm-input-submit" value="<?php echo t('Run Tests Again')?>" />
</form>
</div>

</div>
<div id="ccm-install-note">
<?php echo t('Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.', 'http://www.concrete5.org/community/forums/installation', 'http://www.getconcrete5.com')?>
</div>
</div>


<div id="ccm-install-form" class="ccm-form">


<form action="<?php echo $this->url('/install', 'configure')?>" method="post">

	<h2><?php echo t('Personal Information')?></h2>
	
	<label for="SITE"><?php echo t('Name Your Site')?>:</label><br/>
	<?php echo $form->text('SITE', array('disabled'=> 1)); ?>
	<br/><br/>

	<label for="uEmail"><?php echo t('Your Email Address')?></label><br/>
	<?php echo $form->text('uEmail', array('disabled'=> 1)); ?>
	
	<br/><br/>
	
	<h2><?php echo t('Database Information')?></h2>
	
	<label for="DB_SERVER"><?php echo t('Server')?></label><br/>
	<?php echo $form->text('DB_SERVER', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<label for="DB_USERNAME"><?php echo t('MySQL Username')?></label><br/>
	<?php echo $form->text('DB_USERNAME', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<label for="DB_PASSWORD"><?php echo t('MySQL Password')?></label><br/>
	<?php echo $form->text('DB_PASSWORD', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<label for="DB_DATABASE"><?php echo t('Database Name')?></label><br/>
	<?php echo $form->text('DB_DATABASE', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<h2><?php echo t('Sample Content')?></h2>
	
	<?php echo $form->checkbox('INSTALL_SAMPLE_CONTENT',1,true); ?>
	<label for="INSTALL_SAMPLE_CONTENT"><?php echo t('Install sample content')?></label><br/>	
	<br/>
	
	<div class="ccm-button">
	<?php echo $form->submit('submit', t('Install Concrete').' &gt;', array('disabled'=> 1))?>
	</div>
	<br/><br/>

</form>
</div>
<?php  } ?>
