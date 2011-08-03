<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<? 

$installErrorMsg = t('Unable to Install. Not all required items are available.');
$introMsg = t('To install concrete5, please fill out the form below.');
if (isset($message)) { ?>

<h1><?=t('Install concrete5')?></h1>

<div class="ccm-form">
<?=$message?>
<br/><br/>
<a href="<?=DIR_REL?>/"><?=t('Continue to your site.')?> &gt;</a>
</div>

<? } else { ?>

<style type="text/css">
input.ccm-input-text:disabled {
	opacity: 0.8;
	-moz-opacity: 0.8;
}
</style>

<script type="text/javascript">


activateInstallForm = function() {
	$("#ccm-install-form input").each(function() {
		$(this).attr('disabled', false);
	});
	$('#ccm-form-intro').html('<?=$introMsg?>');
	$("#ccm-form-intro").removeClass('ccm-error');
}

<? if ($this->controller->passedRequiredItems()) { ?>
	var showFormOnTestCompletion = true;
<? } else { ?>
	var showFormOnTestCompletion = false;
<? } ?>


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
	$.getJSON('<?=$this->url("/install", "test_url", "20", "20")?>', function(json) {
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

<h1><?=t('Install concrete5')?></h1>
	
</div>

<div id="ccm-install-check">

<div id="ccm-install-check-items">
<h2 style="margin-top: 0px"><?=t('Testing Required Items')?></h2>
<div class="test <? if ($phpVtest) { ?>passed<? } else { ?>warning<? } ?>"><?=t('PHP 5.1 Available')?>
<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('PHP 5.2 or greater is recommended<br />
 for timezone support.')?></div>
</div>

<div id="ccm-test-js" class="test fail"><?=t('JavaScript Enabled')?>

<noscript>
<div class="ccm-install-info" style="display: block"><?=t('Please enable JavaScript in your browser.')?></div>
</noscript>

</div>


<div class="test <? if ($mysqlTest) { ?>passed<? } else { ?>fail<? } ?>"><?=t('MySQL Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=$this->controller->getDBErrorMsg()?></div>

</div>
<div class="test loading" id="ccm-test-urls"><?=t('Support for C5 Request URLs')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('concrete5 cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.')?></div>

</div>
<div class="test <? if ($imageTest) { ?>passed<? } else { ?>fail<? } ?>"><?=t('Image Manipulation Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('concrete5 requires GD library 2.0.1 or greater.')?></div>

</div>
<div class="test <? if ($xmlTest) { ?>passed<? } else { ?>fail<? } ?>"><?=t('XML Support')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('concrete5 requires PHP XML Parser and SimpleXML extensions.')?></div>

</div>

<div class="test <? if ($fileWriteTest) { ?>passed<? } else { ?>fail<? } ?>"><?=t('Web Server Access to Files and Configuration Directories')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('The config/, packages/ and files/ directories must be writable by your web server.')?></div>

</div>

<h2><?=t('Testing Optional Items')?></h2>
<? /*
<div class="test <? if ($langTest) { ?>passed<? } else { ?>warning<? } ?>"><?=t('Multilingual Support')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('Multilingual support requires the gettext PHP extension and may not work with safe mode enabled.')?></div>

</div>
*/ ?>

<div class="test <? if ($remoteFileUploadTest) { ?>passed<? } else { ?>warning<? } ?>"><?=t('Remote File Importing Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('Remote file importing through the file manager requires the iconv PHP extension.')?></div>

</div>

<div class="test <? if ($diffTest) { ?>passed<? } else { ?>warning<? } ?>"><?=t('Version Comparison Available')?>

<a href="javascript:void(0)" class="ccm-install-tooltip"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" border="0" width="16" height="16" alt="" /></a>
<div class="ccm-install-info"><?=t('You must chmod 755 %s and disable PHP safe mode.', 'concrete/libraries/3rdparty/htmldiff.py')?></div>
</div>

<div style="text-align: center; margin-top: 16px">
<form action="<?=$this->url('/install')?>" method="get">
	<input type="submit" name="submit" class="ccm-input-submit" value="<?=t('Run Tests Again')?>" />
</form>
</div>

</div>
<div id="ccm-install-note">
<?=t('Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.', 'http://www.concrete5.org/community/forums/installation', 'http://www.getconcrete5.com')?>
</div>
</div>


<div id="ccm-install-form" class="ccm-form">


<form action="<?=$this->url('/install', 'configure')?>" method="post">

	<h2><?=t('Personal Information')?></h2>
	
	<label for="SITE"><?=t('Name Your Site')?>:</label><br/>
	<?=$form->text('SITE', array('disabled'=> 1)); ?>
	<br/><br/>

	<label for="uEmail"><?=t('Your Email Address')?></label><br/>
	<?=$form->text('uEmail', array('disabled'=> 1)); ?>
	
	<br/><br/>
	
	<h2><?=t('Database Information')?></h2>
	
	<label for="DB_SERVER"><?=t('Server')?></label><br/>
	<?=$form->text('DB_SERVER', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<label for="DB_USERNAME"><?=t('MySQL Username')?></label><br/>
	<?=$form->text('DB_USERNAME', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<label for="DB_PASSWORD"><?=t('MySQL Password')?></label><br/>
	<?=$form->text('DB_PASSWORD', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<label for="DB_DATABASE"><?=t('Database Name')?></label><br/>
	<?=$form->text('DB_DATABASE', array('disabled'=> 1)); ?>
	<br/><br/>
	
	<h2><?=t('Sample Content')?></h2>
	
	<?=$form->checkbox('INSTALL_SAMPLE_CONTENT',1,true); ?>
	<label for="INSTALL_SAMPLE_CONTENT"><?=t('Install sample content')?></label><br/>	
	<br/>
	
	<div class="ccm-button">
	<?=$form->submit('submit', t('Install concrete5').' &gt;', array('disabled'=> 1))?>
	</div>
	<br/><br/>

</form>
</div>
<? } ?>
