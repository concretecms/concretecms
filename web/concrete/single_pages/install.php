<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/bootstrap.tooltip.js"></script>
<script type="text/javascript">
$(function() {
	$(".launch-tooltip").tooltip({
		placement: 'bottom'
	});
});
</script>

<? 

$introMsg = t('To install concrete5, please fill out the form below.');

if (isset($successMessage)) { ?>

<script type="text/javascript">
$(function() {
	
<? for ($i = 1; $i <= count($installRoutines); $i++) {
	$routine = $installRoutines[$i-1]; ?>

	ccm_installRoutine<?=$i?> = function() {
		<? if ($routine->getText() != '') { ?>
			$("#install-progress-summary").html('<?=$routine->getText()?>');
		<? } ?>
		$.ajax('<?=$this->url("/install", "run_routine", $installPackage, $routine->getMethod())?>', {
			dataType: 'json',
			error: function(r) {
				$("#install-progress-wrapper").hide();
				$("#install-progress-errors").append('<div class="alert-message error">' + r.responseText + '</div>');
				$("#install-progress-error-wrapper").fadeIn(300);
			},
			success: function(r) {
				if (r.error) {
					$("#install-progress-wrapper").hide();
					$("#install-progress-errors").append('<div class="alert-message error">' + r.message + '</div>');
					$("#install-progress-error-wrapper").fadeIn(300);
				} else {
					$('#install-progress-bar div.bar').css('width', '<?=$routine->getProgress()?>%');
					<? if ($i < count($installRoutines)) { ?>
						ccm_installRoutine<?=$i+1?>();
					<? } else { ?>
						$("#install-progress-wrapper").fadeOut(300, function() {
							$("#success-message").fadeIn(300);
						});
					<? } ?>
				}
			}
		});
	}
	
<? } ?>

	ccm_installRoutine1();

});

</script>


<div class="page-header">
<h1><?=t('Install concrete5')?></h1>
</div>

<div class="row">
<div class="span10 offset3">

<div id="success-message">
<?=$successMessage?>
<br/><br/>
<div class="well">
<input type="button" class="btn large primary" onclick="window.location.href='<?=DIR_REL?>/'" value="<?=t('Continue to your site')?>" />
</div>
</div>

<div id="install-progress-wrapper">

<div class="alert-message info">
<div id="install-progress-summary">
<?=t('Beginning Installation')?>
</div>
</div>

<div id="install-progress-bar">

<div class="progress progress-striped active">
<div class="bar" style="width: 0%;"></div>
</div>

</div>

</div>

<div id="install-progress-error-wrapper">

<div id="install-progress-errors"></div>
<div id="install-progress-back">
<input type="button" class="btn" onclick="window.location.href='<?=$this->url('/install')?>'" value="<?=t('Back')?>" />
</div>

</div>
</div>
</div>

<? } else if ($this->controller->getTask() == 'setup' || $this->controller->getTask() == 'configure') { ?>

<script type="text/javascript">
$(function() {
	$("#sample-content-selector td").click(function() {
		$(this).parent().find('input[type=radio]').prop('checked', true);
		$(this).parent().parent().find('tr').removeClass();
		$(this).parent().addClass('package-selected');
	});
});
</script>

<div class="page-header">
<h1><?=t('Install concrete5')?></h1>
</div>

<form action="<?=$this->url('/install', 'configure')?>" method="post" class="form-horizontal">

<div class="row">
<div class="span8 columns">

	<input type="hidden" name="locale" value="<?=$locale?>" />
	
	<fieldset>
		<legend><?=t('Site Information')?></legend>
		<div class="control-group">
		<label for="SITE" class="control-label"><?=t('Name Your Site')?>:</label>
		<div class="controls">
			<?=$form->text('SITE', array('class' => 'xlarge'))?>
		</div>
		</div>
			
	</fieldset>
	
	<fieldset>
		<legend><?=t('Administrator Information')?></legend>
		<div class="clearfix">
		<label for="uEmail"><?=t('Email Address')?>:</label>
		<div class="input">
			<?=$form->email('uEmail', array('class' => 'xlarge'))?>
		</div>
		</div>
		<div class="clearfix">
		<label for="uPassword"><?=t('Password')?>:</label>
		<div class="input">
			<?=$form->password('uPassword', array('class' => 'xlarge'))?>
		</div>
		</div>
		<div class="clearfix">
		<label for="uPasswordConfirm"><?=t('Confirm Password')?>:</label>
		<div class="input">
			<?=$form->password('uPasswordConfirm', array('class' => 'xlarge'))?>
		</div>
		</div>
		
	</fieldset>

</div>
<div class="span8 columns">

	<fieldset>
		<legend><?=t('Database Information')?></legend>

	<div class="clearfix">
	<label for="DB_SERVER"><?=t('Server')?>:</label>
	<div class="input">
		<?=$form->text('DB_SERVER', array('class' => 'xlarge'))?>
	</div>
	</div>

	<div class="clearfix">
	<label for="DB_USERNAME"><?=t('MySQL Username')?>:</label>
	<div class="input">
		<?=$form->text('DB_USERNAME', array('class' => 'xlarge'))?>
	</div>
	</div>

	<div class="clearfix">
	<label for="DB_PASSWORD"><?=t('MySQL Password')?>:</label>
	<div class="input">
		<?=$form->password('DB_PASSWORD', array('class' => 'xlarge'))?>
	</div>
	</div>

	<div class="clearfix">
	<label for="DB_DATABASE"><?=t('Database Name')?>:</label>
	<div class="input">
		<?=$form->text('DB_DATABASE', array('class' => 'xlarge'))?>
	</div>
	</div>
</div>
</div>

<div class="row">
<div class="span16 columns">

<h3><?=t('Sample Content')?></h3>

		
		<?
		$uh = Loader::helper('concrete/urls');
		?>
		
		<table class="table table-striped" id="sample-content-selector">
		<tbody>
		<? 
		$availableSampleContent = StartingPointPackage::getAvailableList();
		foreach($availableSampleContent as $spl) { 
			$pkgHandle = $spl->getPackageHandle();
		?>

		<tr class="<? if ($this->post('SAMPLE_CONTENT') == $pkgHandle || (!$this->post('SAMPLE_CONTENT') && $pkgHandle == 'standard') || count($availableSampleContent) == 1) { ?>package-selected<? } ?>">
			<td><?=$form->radio('SAMPLE_CONTENT', $pkgHandle, ($pkgHandle == 'standard' || count($availableSampleContent) == 1))?></td>
			<td><img src="<?=$uh->getPackageIconURL($spl)?>" width="97" height="97" alt="<?=$spl->getPackageName()?>" /></td>
			<td width="100%"><h4><?=$spl->getPackageName()?></h4><p><?=$spl->getPackageDescription()?></td>
		</tr>
		
		<? } ?>
		
		</tbody>
		</table>
		<br/>
		<? if (!StartingPointPackage::hasCustomList()) { ?>
			<div class="alert-message block-message info"><?=t('concrete5 veterans can choose "Empty Site," but otherwise we recommend starting with some sample content.')?></div>
		<? } ?>

	
</div>
</div>

<div class="well">
	<button class="btn btn-large primary" type="submit"><i class="icon-thumbs-up icon-white"></i> <?=t('Install concrete5')?></button>
</div>
</form>

<? } else if (isset($locale) || count($locales) == 0) { ?>

<script type="text/javascript">

$(function() {
	$("#install-errors").hide();
});

<? if ($this->controller->passedRequiredItems()) { ?>
	var showFormOnTestCompletion = true;
<? } else { ?>
	var showFormOnTestCompletion = false;
<? } ?>


$(function() {
	$(".ccm-test-js img").hide();
	$("#ccm-test-js-success").show();
	$("#ccm-test-request-loading").ajaxError(function(event, request, settings) {
		$(this).attr('src', '<?=ASSETS_URL_IMAGES?>/icons/error.png');
		$("#ccm-test-request-tooltip").show();
	});
	$.getJSON('<?=$this->url("/install", "test_url", "20", "20")?>', function(json) {
		// test url takes two numbers and adds them together. Basically we just need to make sure that
		// our url() syntax works - we do this by sending a test url call to the server when we're certain 
		// of what the output will be
		if (json.response == 40) {
			$("#ccm-test-request-loading").attr('src', '<?=ASSETS_URL_IMAGES?>/icons/success.png');
			if (showFormOnTestCompletion) {
				$("#install-success").show();
			} else {
				$("#install-errors").show();
			}
		} else {
			$("#ccm-test-request-loading").attr('src', '<?=ASSETS_URL_IMAGES?>/icons/error.png');
			$("#ccm-test-request-tooltip").show();
			$("#install-errors").show();
		}
	});
	
});
</script>


<div class="page-header">
	<h1><?=t('Install concrete5')?></h1>
</div>


<h3><?=t('Testing Required Items')?></h3>
<div class="row">
<div class="span8 columns">

<table class="requirements-table table">
<tbody>
<tr>
	<td><? if ($phpVtest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/warning.png" /><? } ?></td>
	<td width="100%"><?=t('PHP 5.2')?></td>
	<td><? if (!$phpVtest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('While concrete5 will mostly run on PHP 5.1, 5.2 is strongly encouraged and some functions will not work properly without it.')?>" /><? } ?></td>
</tr>
<tr>
	<td class="ccm-test-js"><img id="ccm-test-js-success" src="<?=ASSETS_URL_IMAGES?>/icons/success.png" style="display: none" />
	<img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /></td>
	<td width="100%"><?=t('JavaScript Enabled')?></td>
	<td class="ccm-test-js"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('Please enable JavaScript in your browser.')?>" /></td>
</tr>
<tr>
	<td><? if ($mysqlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('MySQL Available')?>
	</td>
	<td><? if (!$mysqlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=$this->controller->getDBErrorMsg()?>" /><? } ?></td>
</tr>
<tr>
	<td><img id="ccm-test-request-loading"  src="<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" /></td>
	<td width="100%"><?=t('Supports concrete5 request URLs')?>
	</td>
	<td><img id="ccm-test-request-tooltip" src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('concrete5 cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.')?>" /></td>
</tr>
</table>

</div>
<div class="span8 columns">

<table class="requirements-table table">

<tr>
	<td><? if ($imageTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('Image Manipulation Available')?>
	</td>
	<td><? if (!$imageTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('concrete5 requires GD library 2.0.1 or greater')?>" /><? } ?></td>
</tr>
<tr>
	<td><? if ($xmlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('XML Support')?>
	</td>
	<td><? if (!$xmlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('concrete5 requires PHP XML Parser and SimpleXML extensions')?>" /><? } ?></td>
</tr>
<tr>
	<td><? if ($fileWriteTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('Web Server Access to Files and Configuration Directories')?>
	</td>
	<td><? if (!$fileWriteTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('The config/, packages/ and files/ directories must be writable by your web server.')?>" /><? } ?></td>
</tr>

</tbody>
</table>

</div>
</div>

<br/><br/>

<h3><?=t('Testing Optional Items')?></h3>

<div class="row">
<div class="span8 columns">


<table class="requirements-table table">
<tbody>
<tr>
	<td><? if ($remoteFileUploadTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/warning.png" /><? } ?></td>
	<td width="100%"><?=t('Remote File Importing Available')?>
	</td>
	<td><? if (!$remoteFileUploadTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('Remote file importing through the file manager requires the iconv PHP extension.')?>" /><? } ?></td>
</tr>
</table>

</div>
<div class="span8 columns">
<table class="requirements-table table">

<tr>
	<td><? if ($diffTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/warning.png" /><? } ?></td>
	<td width="100%"><?=t('Version Comparison Available')?>
	</td>
	<td><? if (!$diffTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="launch-tooltip" title="<?=t('You must chmod 755 %s and disable PHP safe mode.', 'concrete/libraries/3rdparty/htmldiff.py')?>" /><? } ?></td>
</tr>
</tbody>
</table>
</div>
</div>

<div class="well" id="install-success">
	<form method="post" action="<?=$this->url('/install','setup')?>">
	<input type="hidden" name="locale" value="<?=$locale?>" />
	<a class="btn btn-large primary" href="javascript:void(0)" onclick="$(this).parent().submit()"><i class="icon-arrow-right icon-white"></i> <?=t('Continue to Installation')?></a>
	</form>
</div>

<div class="block-message alert-message error" id="install-errors">
	<p><?=t('There are problems with your installation environment. Please correct them and click the button below to re-run the pre-installation tests.')?></p>
	<div class="block-actions">
	<form method="post" action="<?=$this->url('/install')?>">
	<input type="hidden" name="locale" value="<?=$locale?>" />
	<a class="btn" href="javascript:void(0)" onclick="$(this).parent().submit()"><i class="icon-refresh"></i> <?=t('Run Tests')?></a>
	</form>
	</div>	
</div>

<div class="block-message alert-message info">
<?=t('Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.', 'http://www.concrete5.org/community/forums/installation', 'http://www.concrete5.org/services/hosting')?>
</div>

<? } else { ?>


<div class="page-header">
	<h1><?=t('Install concrete5')?></h1>
</div>

<div id="ccm-install-intro">

<form method="post" action="<?=$this->url('/install', 'select_language')?>">
<fieldset>
	<div class="clearfix">
	
	<label for="locale"><?=t('Language')?></label>
	<div class="input">
		<?=$form->select('locale', $locales, 'en_US'); ?>
	</div>
	
	</div>
	
	<div class="actions">
	<?=$form->submit('submit', t('Choose Language'))?>
	</div>
</fieldset>
</form>

</div>

<? } ?>