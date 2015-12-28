<?php
defined('C5_EXECUTE') or die("Access Denied.");

$install_config = Config::get('install_overrides');
if ($install_config) {
    $_POST = $install_config;
}
?>

<style type="text/css">@import "<?=ASSETS_URL_CSS?>/views/install.css";</style>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/bootstrap/tooltip.js"></script>
<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery-cookie.js"></script>
<script type="text/javascript">
$(function() {
	$(".launch-tooltip").tooltip({
		placement: 'bottom'
	});
});
</script>

<?php

$introMsg = t('To install concrete5, please fill out the form below.');

if (isset($successMessage)) { ?>

<script type="text/javascript">
$(function() {

<?php for ($i = 1; $i <= count($installRoutines); ++$i) {
	$routine = $installRoutines[$i - 1];
	?>

	ccm_installRoutine<?=$i?> = function() {
		<?php if ($routine->getText() != '') { ?>
			$("#install-progress-summary").html('<?=addslashes($routine->getText())?>');
		<?php } ?>
		$.ajax('<?=$view->url("/install", "run_routine", $installPackage, $routine->getMethod())?>', {
			dataType: 'json',
			error: function(r) {
				$("#install-progress-wrapper").hide();
				$("#install-progress-errors").append('<div class="alert alert-danger">' + r.responseText + '</div>');
				$("#install-progress-error-wrapper").fadeIn(300);
			},
			success: function(r) {
				if (r.error) {
					$("#install-progress-wrapper").hide();
					$("#install-progress-errors").append('<div class="alert alert-danger">' + r.message + '</div>');
					$("#install-progress-error-wrapper").fadeIn(300);
				} else {
					$('#install-progress-bar div.progress-bar').css('width', '<?=$routine->getProgress()?>%');
					<?php if ($i < count($installRoutines)) { ?>
						ccm_installRoutine<?=$i + 1?>();
					<?php } else { ?>
						$("#install-progress-wrapper").fadeOut(300, function() {
							$("#success-message").fadeIn(300);
						});
					<?php } ?>
				}
			}
		});
	}

<?php } ?>

	ccm_installRoutine1();

});

</script>

<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<div class="page-header">
<h1><?=t('Install concrete5')?></h1>
<p><?=t('Version %s', Config::get('concrete.version'))?></p>
</div>
</div>
</div>


<div class="row">
<div class="col-sm-10 col-sm-offset-1">

<div id="success-message">
<?=$successMessage?>
<br/><br/>
<div class="well">
<input type="button" class="btn btn-large btn-primary" onclick="window.location.href='<?=URL::to('/')?>'" value="<?=t('Continue to your site')?>" />
</div>
</div>

<div id="install-progress-wrapper">
<div class="alert alert-info">
<div id="install-progress-summary">
<?=t('Beginning Installation')?>
</div>
</div>

<div id="install-progress-bar">
<div class="progress progress-striped active">
<div class="progress-bar" style="width: 0%;"></div>
</div>
</div>

</div>

<div id="install-progress-error-wrapper">
<div id="install-progress-errors"></div>
<div id="install-progress-back">
<input type="button" class="btn" onclick="window.location.href='<?=$view->url('/install')?>'" value="<?=t('Back')?>" />
</div>
</div>
</div>
</div>

<?php } elseif ($this->controller->getTask() == 'setup' || $this->controller->getTask() == 'configure') { ?>

<script type="text/javascript">
$(function() {
	$("#sample-content-selector td").click(function() {
		$(this).parent().find('input[type=radio]').prop('checked', true);
		$(this).parent().parent().find('tr').removeClass();
		$(this).parent().addClass('package-selected');
	});
});
</script>

<div class="row">
<div class="col-md-10 col-md-offset-1">

<div class="page-header">
<h1><?=t('Install concrete5')?></h1>
<p><?=t('Version %s', Config::get('concrete.version'))?></p>
</div>

</div>
</div>


<form action="<?=$view->url('/install', 'configure')?>" method="post" class="form-horizontal">

<div class="row">
<div class="col-md-5 col-md-offset-1">

	<input type="hidden" name="locale" value="<?=h($locale)?>" />

	<fieldset>
		<legend><?=t('Site Information')?></legend>
		<div class="form-group">
		<label for="SITE" class="control-label col-md-4"><?=t('Site Name')?>:</label>
		<div class="col-md-8">
			<?=$form->text('SITE', array('class' => ''))?>
		</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?=t('Administrator Information')?></legend>
		<div class="form-group">
		<label for="uEmail" class="control-label col-md-4"><?=t('Email Address')?>:</label>
		<div class="col-md-8">
		<?=$form->email('uEmail', array('class' => ''))?>
		</div>
		</div>
		<div class="form-group">
		<label for="uPassword" class="control-label col-md-4"><?=t('Password')?>:</label>
		<div class="col-md-8">
		<?=$form->password('uPassword', array('class' => '', 'autocomplete' => 'off'))?>
		</div>
		</div>
		<div class="form-group">
		<label for="uPasswordConfirm" class="control-label col-md-4"><?=t('Confirm Password')?>:</label>
		<div class="col-md-8">
			<?=$form->password('uPasswordConfirm', array('class' => '', 'autocomplete' => 'off'))?>
		</div>
		</div>

	</fieldset>

</div>
<div class="col-sm-5">

	<fieldset>
		<legend><?=t('Database Information')?></legend>

	<div class="form-group">
	<label class="control-label col-md-4" for="DB_SERVER"><?=t('Server')?>:</label>
	<div class="col-md-8">
	<?=$form->text('DB_SERVER', array('class' => ''))?>
	</div>
	</div>

	<div class="form-group">
	<label class="control-label col-md-4" for="DB_USERNAME"><?=t('MySQL Username')?>:</label>
	<div class="col-md-8">
		<?=$form->text('DB_USERNAME', array('class' => ''))?>
	</div>
	</div>

	<div class="form-group">
	<label class="control-label col-md-4" for="DB_PASSWORD"><?=t('MySQL Password')?>:</label>
	<div class="col-md-8">
		<?=$form->password('DB_PASSWORD', array('class' => '', 'autocomplete' => 'off'))?>
	</div>
	</div>

	<div class="form-group">
	<label class="control-label col-md-4" for="DB_DATABASE"><?=t('Database Name')?>:</label>
	<div class="col-md-8">
		<?=$form->text('DB_DATABASE', array('class' => ''))?>
	</div>
	</div>
	</fieldset>
</div>
</div>

<div class="row">
<div class="col-sm-10 col-sm-offset-1">

<h3><?=t('Sample Content')?></h3>


		<?php
		$uh = Loader::helper('concrete/urls');
		?>

		<table class="table table-striped" id="sample-content-selector">
		<tbody>
		<?php
		$availableSampleContent = StartingPointPackage::getAvailableList();
		foreach ($availableSampleContent as $spl) {
			$pkgHandle = $spl->getPackageHandle();
		?>

		<tr class="<?php if ($this->post('SAMPLE_CONTENT') == $pkgHandle || (!$this->post('SAMPLE_CONTENT') && $pkgHandle == 'elemental_full') || count($availableSampleContent) == 1) { ?>package-selected<?php } ?>">
			<td><?=$form->radio('SAMPLE_CONTENT', $pkgHandle, ($pkgHandle == 'elemental_full' || count($availableSampleContent) == 1))?></td>
			<td class="sample-content-thumbnail"><img src="<?=$uh->getPackageIconURL($spl)?>" width="97" height="97" alt="<?=$spl->getPackageName()?>" /></td>
			<td class="sample-content-description"><h4><?=$spl->getPackageName()?></h4><p><?=$spl->getPackageDescription()?></td>
		</tr>

		<?php } ?>

		</tbody>
		</table>
		<br/>
		<?php if (!StartingPointPackage::hasCustomList()) { ?>
			<div class="alert alert-info"><?=t('concrete5 veterans can choose "Empty Site," but otherwise we recommend starting with some sample content.')?></div>
		<?php } ?>


</div>
</div>

<div class="row">
<div class="col-sm-10 col-sm-offset-1">

<div class="well">
	<button class="btn btn-large btn-primary" type="submit"><?=t('Install concrete5')?> <i class="fa fa-thumbs-up fa-white"></i></button>
</div>

</div>
</div>

</form>


<?php } elseif (isset($locale) || count($locales) == 0) { ?>

<script type="text/javascript">

$(function() {
	$("#install-errors").hide();
});

<?php if ($this->controller->passedRequiredItems()) { ?>
	var showFormOnTestCompletion = true;
<?php } else { ?>
	var showFormOnTestCompletion = false;
<?php } ?>


$(function() {
	$(".ccm-test-js i").hide();
	$("#ccm-test-js-success").show();
	if ($.cookie('CONCRETE5_INSTALL_TEST')) {
		$("#ccm-test-cookies-enabled-loading").attr('class', 'fa fa-check');
	} else {
        $("#ccm-test-cookies-enabled-loading").attr('class', 'fa fa-exclamation-circle');
		$("#ccm-test-cookies-enabled-tooltip").show();
		$("#install-errors").show();
		showFormOnTestCompletion = false;
	}
	$("#ccm-test-request-loading").ajaxError(function(event, request, settings) {
		$(this).attr('src', '<?=ASSETS_URL_IMAGES?>/icons/error.png');
		$("#ccm-test-request-tooltip").show();
		showFormOnTestCompletion = false;
	});
	$.getJSON('<?=$view->url("/install", "test_url", "20", "20")?>', function(json) {
		// test url takes two numbers and adds them together. Basically we just need to make sure that
		// our url() syntax works - we do this by sending a test url call to the server when we're certain
		// of what the output will be
		if (json.response == 40) {
			$("#ccm-test-request-loading").attr('class', 'fa fa-check');
			if (showFormOnTestCompletion) {
				$("#install-success").show();
			} else {
				$("#install-errors").show();
			}
            $("#ccm-test-request-tooltip").hide();
		} else {
			$("#ccm-test-request-loading").attr('class', 'fa fa-exclamation-circle');
			$("#ccm-test-request-tooltip").show();
			$("#install-errors").show();
		}
	});

});
</script>

<div class="row">

<div class="col-sm-10 col-sm-offset-1">
<div class="page-header">
	<h1><?=t('Install concrete5')?></h1>
	<p><?=t('Version %s', Config::get('concrete.version'))?></p>
</div>

<h3><?=t('Testing Required Items')?></h3>
</div>
</div>

<div class="row">
<div class="col-sm-5 col-sm-offset-1">

<table class="table table-striped requirements-table">
<tbody>
<tr>
	<td class="ccm-test-phpversion"><?php if ($phpVtest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
	<td width="100%"><?=t(/*i18n: %s is the php version*/'PHP %s', $phpVmin)?></td>
	<td><?php if (!$phpVtest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('concrete5 requires at least PHP %s', $phpVmin)?>"></i><?php } ?></td>
</tr>
<tr>
	<td class="ccm-test-js"><i id="ccm-test-js-success" class="fa fa-check" style="display: none"></i>
	<i class="fa fa-exclamation-circle"></i></td>
	<td width="100%"><?=t('JavaScript Enabled')?></td>
	<td class="ccm-test-js"><i class="fa fa-question-circle launch-tooltip" title="<?=t('Please enable JavaScript in your browser.')?>"></i></td>
</tr>
<tr>
	<td><?php if ($mysqlTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
	<td width="100%"><?=t('MySQL PDO Extension Enabled')?>
	</td>
	<td><?php if (!$mysqlTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=$this->controller->getDBErrorMsg()?>"></i><?php } ?></td>
</tr>
<tr>
	<td><i id="ccm-test-request-loading"  class="fa fa-spinner fa-spin"></i></td>
	<td width="100%"><?=t('Supports concrete5 request URLs')?>
	</td>
	<td><i id="ccm-test-request-tooltip" class="fa fa-question-circle launch-tooltip" title="<?=t('concrete5 cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.')?>"></i></td>
</tr>
<tr>
	<td><?php if ($jsonTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
	<td width="100%"><?=t('JSON Extension Enabled')?>
	</td>
	<td><?php if (!$jsonTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('You must enable PHP\'s JSON support. This should be enabled by default in PHP 5.2 and above.')?>"></i><?php } ?></td>
</tr>
<tr>
    <td><?php if ($aspTagsTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
    <td width="100%"><?=t('ASP Style Tags Disabled')?></td>
    <td><?php if (!$aspTagsTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('You must disable PHP\'s ASP Style Tags.')?>"></i><?php } ?></td>
</tr>

</table>

</div>
<div class="col-sm-5">

<table class="table table-striped requirements-table">
<tr>
	<td><?php if ($imageTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
	<td width="100%"><?=t('Image Manipulation Available')?>
	</td>
	<td><?php if (!$imageTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('concrete5 requires GD library 2.0.1 with JPEG, PNG and GIF support. Doublecheck that your installation has support for all these image types.')?>"></i><?php } ?></td>
</tr>
<tr>
	<td><?php if ($xmlTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
	<td width="100%"><?=t('XML Support')?>
	</td>
	<td><?php if (!$xmlTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('concrete5 requires PHP XML Parser and SimpleXML extensions')?>"></i><?php } ?></td>
</tr>
<tr>
	<td><?php if ($fileWriteTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
	<td width="100%"><?=t('Writable Files and Configuration Directories')?>
	</td>
	<td><?php if (!$fileWriteTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('The packages/, application/config/ and application/files/ directories must be writable by your web server.')?>"></i><?php } ?></td>
</tr>
<tr>
	<td><i id="ccm-test-cookies-enabled-loading" class="fa fa-spinner fa-spin"></i></td>
	<td width="100%"><?=t('Cookies Enabled')?>
	</td>
	<td><i id="ccm-test-cookies-enabled-tooltip" class="fa fa-question-circle launch-tooltip" title="<?=t('Cookies must be enabled in your browser to install concrete5.')?>"></i></td>
</tr>
<tr>
    <td><?php if ($i18nTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
    <td width="100%"><?=t('Internationalization Support')?>
    </td>
    <td><?php if (!$i18nTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('You must enable ctype and multibyte string (mbstring) support in PHP.')?>"></i><?php } ?></td>
</tr>
<tr>
    <td><?php if ($docCommentTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-exclamation-circle"></i><?php } ?></td>
    <td width="100%"><?=t('PHP Comments Preserved')?>
    <td><?php if (!$docCommentTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('concrete5 is not compatible with opcode caches that strip PHP comments. Certain configurations of eAccelerator and Zend opcode caching may use this behavior, and it must be disabled.')?>"></td><?php } ?></td>
</tr>
</table>

</div>
</div>


<div class="row">
<div class="col-sm-10 col-sm-offset-1">

<h3><?=t('Testing Optional Items')?></h3>

</div>
</div>

<div class="row">
<div class="col-sm-5 col-sm-offset-1">

<table class="table table-striped requirements-table">
<tbody>
<tr>
	<td><?php if ($remoteFileUploadTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-warning"></i><?php } ?></td>
	<td width="100%"><?=t('Remote File Importing Available')?>
	</td>
	<td><?php if (!$remoteFileUploadTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('Remote file importing through the file manager requires the iconv PHP extension.')?>"></i><?php } ?></td>
</tr>
</table>

</div>

<div class="col-sm-5">

    <table class="table table-striped requirements-table">
        <tbody>
        <tr>
            <td><?php if ($fileZipTest) { ?><i class="fa fa-check"></i><?php } else { ?><i class="fa fa-warning"></i><?php } ?></td>
            <td width="100%"><?=t('Zip Support')?>
            </td>
            <td><?php if (!$fileZipTest) { ?><i class="fa fa-question-circle launch-tooltip" title="<?=t('Downloading zipped files from the file manager, remote updating and marketplace integration requires the Zip extension.')?>"></i><?php } ?></td>
        </tr>
    </table>

</div>

</div>

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">

            <h3><?=t('Memory Requirements')?></h3>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">

            <table class="table table-striped requirements-table">
                <tbody>
                <tr>
                    <td>
                        <?php if ($memoryTest === -1) { ?>
                            <i class="fa fa-exclamation-circle"></i>
                        <?php } elseif ($memoryTest === 1) { ?>
                            <i class="fa fa-check"></i>
                        <?php } else { ?>
                            <i class="fa fa-warning"></i>
                        <?php } ?>
                    </td>
                    <td width="100%">
                        <?php if ($memoryTest === -1) { ?>
                            <span class="text-danger"><?=t('concrete5 will not install with less than 24MB of RAM.
                            Your memory limit is currently %sMB. Please increase your memory_limit using ini_set.', round(Core::make('helper/number')->formatSize($memoryBytes, 'MB'), 2))?>
                            </span>
                        <?php } ?>
                        <?php if ($memoryTest === 0) { ?>
                            <span class="text-warning"><?=t('concrete5 runs best with at least 64MB of RAM.
                            Your memory limit is currently %sMB. You may experience problems uploading and resizing large images, and may have to install concrete5 without sample content.', round(Core::make('helper/number')->formatSize($memoryBytes, 'MB'), 2))?></span>
                        <?php } ?>
								<?php if ($memoryTest === 1) { ?>
                            <span class="text-success"><?=t('Memory limit %sMB.', round(Core::make('helper/number')->formatSize($memoryBytes, 'MB'), 2))?></span>
                        <?php } ?>

                    </td>
                </tr>
            </table>

        </div>

    </div>

<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<div class="well" id="install-success">
	<form method="post" action="<?=$view->url('/install', 'setup')?>">
	<input type="hidden" name="locale" value="<?=h($locale)?>" />
	<a class="btn btn-large btn-primary" href="javascript:void(0)" onclick="$(this).parent().submit()"><?=t('Continue to Installation')?> <i class="fa fa-arrow-right fa-white"></i></a>
	</form>
</div>

<div class="alert alert-error" id="install-errors">
	<?=t('There are problems with your installation environment. Please correct them and click the button below to re-run the pre-installation tests.')?>
    <br/><br/>
	<form method="post" action="<?=$view->url('/install')?>">
    	<input type="hidden" name="locale" value="<?=h($locale)?>" />
    	<button class="btn btn-default" type="submit"><?=t('Run Tests')?> <i class="fa fa-refresh"></i></button>
	</form>
</div>

<div class="alert alert-info">
<?php $install_forum_url = tc('InstallationHelpForums', 'http://www.concrete5.org/community/forums/installation')?>
<?=t('Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.', $install_forum_url, 'http://www.concrete5.org/services/hosting')?>
</div>
</div>
</div>

<?php } else { ?>

<div class="row">
<div class="col-sm-8 col-sm-offset-2">
<div class="page-header">
	<h1><?=t('Install concrete5')?></h1>
	<p><?=t('Version %s', Config::get('concrete.version'))?></p>
</div>
</div>
</div>

<div class="row">
<div class="col-sm-8 col-sm-offset-2">

<div id="ccm-install-intro">

<form method="post" class="form-horizontal" action="<?=$view->url('/install', 'select_language')?>">
<fieldset>
	<div class="form-group">
	<label for="locale" class="control-label col-sm-3"><?=t('Language')?></label>
	<div class="col-sm-7">
		<?=$form->select('locale', $locales, 'en_US'); ?>
	</div>
	</div>
	<div class="form-group col-sm-10">
		<button type="submit" class="btn btn-primary pull-right"><?=t('Choose Language')?></button>
	</div>

</fieldset>
</form>

</div>
</div>
</div>

<?php } ?>
