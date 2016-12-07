<?php
defined('C5_EXECUTE') or die("Access Denied.");

/* @var Concrete\Core\Form\Service\Form $form */
$image = date('Ymd') . '.jpg';
$imagePath = Config::get('concrete.urls.background_feed') . '/' . $image;

$install_config = Config::get('install_overrides');
$uh = Core::make('helper/concrete/urls');
if ($install_config) {
    $_POST = $install_config;
}
?>
    <script type="text/javascript" src="<?= ASSETS_URL_JAVASCRIPT ?>/bootstrap/tooltip.js"></script>
    <script type="text/javascript" src="<?= ASSETS_URL_JAVASCRIPT ?>/jquery-cookie.js"></script>
    <script type="text/javascript">
        $(function () {
            $(".launch-tooltip").tooltip({
                placement: 'bottom'
            });
        });
    </script>
    <script type="text/javascript">
    $(function() {
        $.backstretch("<?=$imagePath?>", {
            fade: <?=intval($backgroundFade)?>
        });
    });
    </script>

    <div class="ccm-install-version">
        <span class="label label-info"><?= t('Version %s', Config::get('concrete.version')) ?></span>
    </div>

<?php

if (isset($successMessage)) {
    ?>
    <script type="text/javascript">
        $(function () {
            var inviteToStayHere = false;

            showFailure = function(message) {
                NProgress.done();
                inviteToStayHere = false;
                $("#install-progress-errors").append('<div class="alert alert-danger">' + message + '</div>');
                $("#ccm-install-intro").hide();
                $("#install-progress-error-wrapper").show();
                $('button[data-button=installation-complete]').prop('disabled', false).html(<?=json_encode(t('Back'))?>).on('click', function() {
                    window.location.href='<?= $view->url('/install')?>';
                });
                $("#install-progress-summary").html('<span class="text-danger"><?=t('An error occurred.')?></span>');
                $('div.ccm-install-title ul.breadcrumb li.active').text('<?=t('Installation Failed.')?>');
            }

            window.onbeforeunload = function() {
                if (inviteToStayHere) {
                    return <?=json_encode(t("concrete5 installation is still in progress: you shouldn't close this page at the moment"))?>;
                }
            };
            NProgress.configure({ showSpinner: false });
            <?php
            for ($i = 1; $i <= count($installRoutines); ++$i) {
            $routine = $installRoutines[$i - 1];
            ?>
            ccm_installRoutine<?=$i?> = function () {
                <?php
                if ($routine->getText() != '') {
                ?>
                $("#install-progress-summary").html('<?=addslashes($routine->getText())?>');
                <?php
                }
                ?>
                $.ajax(
                    '<?=$view->url("/install", "run_routine", $installPackage, $routine->getMethod())?>',
                    {
                        dataType: 'json'
                    }
                )
                .fail(function (r) {
                    showFailure(r.responseText);
                })
                .done(function (r) {
                    if (r.error) {
                        showFailure(r.message);
                    } else {
                        NProgress.set(<?=$routine->getProgress()/100?>);
                        <?php
                        if ($i < count($installRoutines)) {
                        ?>
                        ccm_installRoutine<?=$i + 1?>();
                        <?php
                        } else {
                        ?>
                        inviteToStayHere = false;
                        $("#install-progress-summary").html('<?=t('All Done.')?>');
                        NProgress.done();
                        $('button[data-button=installation-complete]').prop('disabled', false).html(<?=json_encode(t('Edit Your Site').' <i class="fa fa-thumbs-up"></i>')?>);
                        $('div.ccm-install-title ul.breadcrumb li.active').text('<?=t('Installation Complete.')?>');

                        setTimeout(function() {
                            $("#interstitial-message").hide();
                            $("#success-message").show().addClass('animated fadeInDown');
                        },500);
                        <?php
                        }
                        ?>
                    }
                });
            }
            <?php
            }
            ?>
            inviteToStayHere = true;
            ccm_installRoutine1();
        });
    </script>

    <div class="ccm-install-title">
        <ul class="breadcrumb">
            <li><?= t('Install concrete5') ?></li>
            <li class="active"><?= t('Installing...') ?></li>
        </ul>
    </div>

    <div id="ccm-install-intro">

        <div id="interstitial-message">
            <div class="panel panel-info">
                <div class="panel-heading"><?=t('While You Wait')?></div>
                <div class="panel-body">

                    <div class="media">
                        <div class="media-left" style="padding-right: 1em">
                            <i class="fa fa-comments-o fa-2x"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?=t('Forums')?></h4>
                            <?=t('<a href="%s" target="_blank">The forum</a> on concrete5.org is full of helpful community members that make concrete5 so great.', Config::get('concrete.urls.help.forum'))?>
                        </div>
                    </div>

                    <div class="media">
                        <div class="media-left" style="padding-right: 1em">
                            <i class="fa fa-book fa-2x"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?=t('User Documentation')?></h4>
                            <?=t('Read the <a href="%s" target="_blank">User Documentation</a> to learn editing and site management with concrete5.', Config::get('concrete.urls.help.user'))?>
                        </div>
                    </div>

                    <div class="media">
                        <div class="media-left" style="padding-right: 1em">
                            <i class="fa fa-youtube fa-2x"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?=t('Screencasts')?></h4>
                            <?=t('The concrete5 <a href="%s" target="_blank">YouTube Channel</a> is full of useful videos covering how to use concrete5.', Config::get('concrete.urls.videos'))?>
                        </div>
                    </div>

                    <div class="media">
                        <div class="media-left" style="padding-right: 1em">
                            <i class="fa fa-code fa-2x"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?=t('Developer Documentation')?></h4>
                            <?=t('The <a href="%s" target="_blank">Developer Documentation</a> covers theming, building add-ons and custom concrete5 development.', Config::get('concrete.urls.help.developer'))?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="success-message">
            <div class="panel panel-success">
                <div class="panel-heading"><?=t('Installation Complete')?></div>
                <div class="panel-body">
                    <?= $successMessage ?>
                </div>
            </div>
        </div>

    </div>

    <div id="install-progress-error-wrapper">
        <div class="spacer-row-6"></div>
        <div id="install-progress-errors">
        </div>
    </div>

    <div class="ccm-install-actions">
        <div class="pull-left" id="install-progress-summary"><?=t('Beginning Installation')?></div>
        <button type="submit" disabled="disabled" onclick="window.location.href='<?= URL::to('/') ?>'" data-button="installation-complete" class="btn btn-primary">
            <?= t('Installing...') ?>
            <i class="fa fa-spinner fa-spin"></i>
        </button>
    </div>


    <?php
} elseif ($this->controller->getTask() == 'setup' || $this->controller->getTask() == 'configure') {
    ?>
    <script type="text/javascript">
        $(function () {
            $("#sample-content-selector td").click(function () {
                $(this).parent().find('input[type=radio]').prop('checked', true);
                $(this).parent().parent().find('tr').removeClass();
                $(this).parent().addClass('package-selected');
            });
            function updateCanonicalURLState() {
                $.each([
                    [$('#canonicalUrlChecked').is(':checked'), $('#canonicalUrl')],
                    [$('#canonicalSSLUrlChecked').is(':checked'), $('#canonicalSSLUrl')]
                ], function () {
                    if (this[0]) {
                        this[1].attr('required', 'required');
                        this[1].removeAttr('disabled');
                    } else {
                        this[1].removeAttr('required');
                        this[1].attr('disabled', 'disabled');
                    }
                });
            }

            $('#canonicalUrlChecked,#canonicalSSLUrlChecked').change(updateCanonicalURLState);
            <?php
            if ($setInitialState) {
            ?>
            $('#canonicalUrlChecked').prop('checked', <?=$canonicalUrlChecked ? 'true' : 'false'?>);
            $('#canonicalSSLUrlChecked').prop('checked', <?=$canonicalSSLUrlChecked ? 'true' : 'false'?>);
            <?php
            }
            ?>
            updateCanonicalURLState();
        });
    </script>

    <div class="ccm-install-title">
        <ul class="breadcrumb">
            <li><?= t('Install concrete5') ?></li>
            <li class="active"><?= t('Site Information') ?></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">

        <form action="<?= $view->url('/install', 'configure') ?>" method="post">


        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <?= t('Site') ?>
                    </h4>
                </div>
                <div id="site" class="">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="SITE" class="control-label"><?= t('Name') ?></label>
                                    <?= $form->text('SITE',
                                        ['autofocus' => 'autofocus', 'required' => 'required']) ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uEmail"
                                           class="control-label"><?= t('Administrator Email Address') ?></label>
                                    <?= $form->email('uEmail', ['required' => 'required']) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uPassword"
                                           class="control-label"><?= t('Administrator Password') ?></label>
                                    <?= $form->password('uPassword', $passwordAttributes) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uPassword"
                                           class="control-label"><?= t('Confirm Password') ?></label>
                                    <?= $form->password('uPasswordConfirm', $passwordAttributes) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <?= t('Starting Point') ?>
                    </h4>
                </div>
                <div id="starting-point" class="">
                    <div class="panel-body">
                        <div class="row">
                            <?php
                            $availableSampleContent = StartingPointPackage::getAvailableList();
                            foreach ($availableSampleContent as $spl) {
                                $pkgHandle = $spl->getPackageHandle();
                                ?>
                                <div class="col-md-6">
                                    <div class="radio">
                                        <label>
                                            <?=$form->radio('SAMPLE_CONTENT', $pkgHandle, ($pkgHandle == 'elemental_full' || count($availableSampleContent) == 1))?>
                                            <strong><?=$spl->getPackageName()?></strong><br/>
                                            <?=$spl->getPackageDescription()?>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <?= t('Database') ?>
                    </h4>
                </div>
                <div id="database" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="DB_SERVER"><?= t('Server') ?></label>
                                <?= $form->text('DB_SERVER', ['required' => 'required']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"
                                       for="DB_USERNAME"><?= t('MySQL Username') ?></label>
                                <?= $form->text('DB_USERNAME') ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"
                                       for="DB_PASSWORD"><?= t('MySQL Password') ?></label>
                                <?= $form->password('DB_PASSWORD', ['autocomplete' => 'off']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"
                                       for="DB_DATABASE"><?= t('Database Name') ?></label>
                                <?= $form->text('DB_DATABASE', ['required' => 'required']) ?>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingThree">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" href="#advanced"><?= t('Advanced Options') ?>
                        </a>
                    </h4>
                </div>

                <div id="advanced" class="panel-collapse collapse">
                    <div class="panel-body">

                        <div class="row">

                            <div class="col-sm-6">
                                <h4><?=t('URLs & Session')?></h4>

                                <div class="form-group">
                                    <label class="control-label">
                                        <?=$form->checkbox('canonicalUrlChecked', '1')?>
                                        <?=t('Set canonical URL for HTTP')?>:
                                    </label>
                                    <?=$form->url('canonicalUrl', h($canonicalUrl), ['pattern' => 'http:.+', 'placeholder' => 'http://'])?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">
                                        <?=$form->checkbox('canonicalSSLUrlChecked', '1')?>
                                        <?=t('Set canonical URL over SSL')?>:
                                    </label>
                                    <?=$form->url('canonicalSSLUrl', h($canonicalSSLUrl), ['pattern' => 'https:.+', 'placeholder' => 'https://'])?>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="sessionHandler"><?=t('Session Handler')?></label>
                                    <?=$form->select('sessionHandler', ['' => t('Default Handler (Recommended)'), 'database' => t('Database')])?>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <h4><?=t('Locale')?></h4>

                                <div class="form-group">
                                    <label class="control-label" for="sessionHandler"><?=t('Language')?></label>
                                    <?=$form->select('siteLocaleLanguage', $languages, $computedSiteLocaleLanguage)?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="sessionHandler"><?=t('Country')?></label>
                                    <select name="siteLocaleCountry" class="form-control">
                                        <optgroup label="<?=t('** Recommended Countries')?>"></optgroup>
                                        <?php foreach($recommendedCountries as $key => $value) { ?>
                                            <option value="<?=$key?>" <?php if ($computedSiteLocaleCountry == $key || isset($_POST['siteLocaleCountry']) && $_POST['siteLocaleCountry'] == $key) { ?>selected<?php } ?>><?=$value?></option>
                                        <?php } ?>
                                        <optgroup label="<?=t('** Other Countries')?>"></optgroup>
                                        <?php foreach($otherCountries as $key => $value) { ?>
                                            <option value="<?=$key?>" <?php if ($computedSiteLocaleCountry == $key || isset($_POST['siteLocaleCountry']) && $_POST['siteLocaleCountry'] == $key) { ?>selected<?php } ?>><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="locale" value="<?= h($locale) ?>"/>

        <div class="ccm-install-actions">
            <button type="submit" class="btn btn-primary">
                <?= t('Install concrete5') ?>
                <i class="fa fa-arrow-right fa-white"></i>
            </button>
        </div>


    </form>

    <div class="spacer-row-6"></div>
        </div>
    </div>


    <?php
} elseif (isset($locale) || count($locales) == 0) {
    ?>

    <div class="ccm-install-title">
        <ul class="breadcrumb">
            <li><?= t('Install concrete5') ?></li>
            <li class="active"><?= t('Testing Environment') ?></li>
        </ul>
    </div>

    <script type="text/javascript">
        $(function () {
            $("#install-errors").hide();
        });
        <?php
        if ($this->controller->passedRequiredItems()) {
        ?>
        var showFormOnTestCompletion = true;
        <?php
        } else {
        ?>
        var showFormOnTestCompletion = false;
        <?php
        }
        ?>
        $(function () {
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
            $("#ccm-test-request-loading").ajaxError(function (event, request, settings) {
                $(this).attr('src', '<?=ASSETS_URL_IMAGES?>/icons/error.png');
                $("#ccm-test-request-tooltip").show();
                showFormOnTestCompletion = false;
            });
            $.getJSON('<?=$view->url("/install", "test_url", "20", "20")?>', function (json) {
                // test url takes two numbers and adds them together. Basically we just need to make sure that
                // our url() syntax works - we do this by sending a test url call to the server when we're certain
                // of what the output will be
                if (json.response == 40) {
                    $("#ccm-test-request-loading").attr('class', 'fa fa-check');
                    if (showFormOnTestCompletion) {
                        $("#install-success").show();
                        $('form[data-form=continue-to-installation]').show();
                    } else {
                        $("#install-errors").show();
                        $('form[data-form=rerun-tests]').show();
                    }
                    $("#ccm-test-request-tooltip").hide();
                } else {
                    $("#ccm-test-request-loading").attr('class', 'fa fa-exclamation-circle');
                    $("#ccm-test-request-tooltip").show();
                    $("#install-errors").show();
                    $('form[data-form=rerun-tests]').show();
                }
            });
        });
    </script>

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1">


            <div class="spacer-row-3"></div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"><?= t('Required Items') ?></h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">

                    <table class="table requirements-table">
                        <tbody>
                        <tr>
                            <td class="ccm-test-phpversion">
                                <?php
                                if ($phpVtest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t(/*i18n: %s is the php version*/
                                    'PHP %s', $phpVmin) ?>
                            </td>
                            <td>
                                <?php
                                if (!$phpVtest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('concrete5 requires at least PHP %s', $phpVmin) ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="ccm-test-js">
                                <i id="ccm-test-js-success" class="fa fa-check" style="display: none"></i>
                                <i class="fa fa-exclamation-circle"></i>
                            </td>
                            <td style="width: 100%">
                                <?= t('JavaScript Enabled') ?>
                            </td>
                            <td class="ccm-test-js">
                                <i class="fa fa-question-circle launch-tooltip"
                                   title="<?= t('Please enable JavaScript in your browser.') ?>"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($mysqlTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('MySQL PDO Extension Enabled') ?>
                            </td>
                            <td>
                                <?php
                                if (!$mysqlTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= $this->controller->getDBErrorMsg() ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i id="ccm-test-request-loading" class="fa fa-spinner fa-spin"></i>
                            </td>
                            <td style="width: 100%">
                                <?= t('Supports concrete5 request URLs') ?>
                            </td>
                            <td>
                                <i id="ccm-test-request-tooltip" class="fa fa-question-circle launch-tooltip"
                                   title="<?= t('concrete5 cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.') ?>"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($jsonTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('JSON Extension Enabled') ?>
                            </td>
                            <td>
                                <?php
                                if (!$jsonTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('You must enable PHP\'s JSON support. This should be enabled by default in PHP 5.2 and above.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($domTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?php echo t('DOM Extension Enabled') ?>
                            </td>
                            <td>
                                <?php
                                if (!$domTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?php echo t('You must enable PHP\'s DOM support.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($aspTagsTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('ASP Style Tags Disabled') ?>
                            </td>
                            <td>
                                <?php
                                if (!$aspTagsTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('You must disable PHP\'s ASP Style Tags.') ?>"></i>
                                    <?php
                                }
                                ?></td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($finfoTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('Fileinfo Extension Enabled') ?>
                            </td>
                            <td>
                                <?php
                                if (!$finfoTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('You must enable the PHP Fileinfo extension.') ?>"></i>
                                    <?php
                                }
                                ?></td>
                        </tr>

                        </tbody>
                    </table>

                </div>

                <div class="col-sm-6">

                    <table class="table requirements-table">
                        <tbody>
                        <tr>
                            <td>
                                <?php
                                if ($imageTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('Image Manipulation Available') ?>
                            </td>
                            <td>
                                <?php
                                if (!$imageTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('concrete5 requires GD library 2.0.1 with JPEG, PNG and GIF support. Doublecheck that your installation has support for all these image types.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($xmlTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('XML Support') ?>
                            </td>
                            <td>
                                <?php
                                if (!$xmlTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('concrete5 requires PHP XML Parser and SimpleXML extensions') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($fileWriteTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('Writable Files and Configuration Directories') ?>
                            </td>
                            <td>
                                <?php
                                if (!$fileWriteTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('The packages/, application/config/ and application/files/ directories must be writable by your web server.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i id="ccm-test-cookies-enabled-loading" class="fa fa-spinner fa-spin"></i>
                            </td>
                            <td style="width: 100%">
                                <?= t('Cookies Enabled') ?>
                            </td>
                            <td>
                                <i id="ccm-test-cookies-enabled-tooltip" class="fa fa-question-circle launch-tooltip"
                                   title="<?= t('Cookies must be enabled in your browser to install concrete5.') ?>"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($i18nTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('Internationalization Support') ?>
                            </td>
                            <td>
                                <?php
                                if (!$i18nTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('You must enable ctype and multibyte string (mbstring) support in PHP.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($docCommentTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('PHP Comments Preserved') ?>
                            <td>
                                <?php
                                if (!$docCommentTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('concrete5 is not compatible with opcode caches that strip PHP comments. Certain configurations of eAccelerator and Zend opcode caching may use this behavior, and it must be disabled.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($memoryTest === -1) {
                                    ?>
                                    <i class="fa fa-exclamation-circle"></i>
                                    <?php
                                } elseif ($memoryTest === 1) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-warning"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?php
                                if ($memoryTest === -1) {
                                    ?>
                                    <span class="text-danger">
                        <?= t('concrete5 will not install with less than %1$s of RAM. Your memory limit is currently %2$s. Please increase your memory_limit using ini_set.',
                            Core::make('helper/number')->formatSize($memoryThresoldMin),
                            Core::make('helper/number')->formatSize($memoryBytes)
                        ) ?>
                    </span>
                                    <?php
                                }
                                ?>
                                <?php
                                if ($memoryTest === 0) {
                                    ?>
                                    <span class="text-warning">
                        <?= t('concrete5 runs best with at least %1$s of RAM. Your memory limit is currently %2$s. You may experience problems uploading and resizing large images, and may have to install concrete5 without sample content.',
                            Core::make('helper/number')->formatSize($memoryThresold),
                            Core::make('helper/number')->formatSize($memoryBytes)
                        ) ?>
                    </span>
                                    <?php
                                }
                                ?>
                                <?php
                                if ($memoryTest === 1) {
                                    ?>
                                    <span class="text-success">
                        <?= t('Memory limit %s.',
                            Core::make('helper/number')->formatSize($memoryBytes)) ?>
                    </span>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"><?= t('Optional Items') ?></h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">

                    <table class="table requirements-table">
                        <tbody>
                        <tr>
                            <td>
                                <?php
                                if ($remoteFileUploadTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-warning"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('Remote File Importing Available') ?>
                            </td>
                            <td>
                                <?php
                                if (!$remoteFileUploadTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('Remote file importing through the file manager requires the iconv PHP extension.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>

                <div class="col-sm-6">

                    <table class="table requirements-table">
                        <tbody>
                        <tr>
                            <td>
                                <?php
                                if ($fileZipTest) {
                                    ?>
                                    <i class="fa fa-check"></i>
                                    <?php
                                } else {
                                    ?>
                                    <i class="fa fa-warning"></i>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="width: 100%">
                                <?= t('Zip Support') ?>
                            </td>
                            <td>
                                <?php
                                if (!$fileZipTest) {
                                    ?>
                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?= t('Downloading zipped files from the file manager, remote updating and marketplace integration requires the Zip extension.') ?>"></i>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-danger" id="install-errors">
        <?= t('There are problems with your installation environment. Please correct them and click the button below to re-run the pre-installation tests.') ?>
        <?= t('Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.',
            'http://www.concrete5.org/community/forums', 'http://www.concrete5.org/services/hosting') ?>
    </div>

    <div class="ccm-install-actions">
        <form method="post" action="<?= $view->url('/install', 'setup') ?>" data-form="continue-to-installation"
              style="display: none">
            <input type="hidden" name="locale" value="<?= h($locale) ?>"/>
            <a class="btn btn-primary" href="javascript:void(0)" onclick="$(this).parent().submit()">
                <?= t('Continue to Installation') ?>
                <i class="fa fa-arrow-right fa-white"></i>
            </a>
        </form>
        <form method="post" action="<?= $view->url('/install') ?>" data-form="rerun-tests" style="display: none">
            <input type="hidden" name="locale" value="<?= h($locale) ?>"/>
            <button class="btn btn-danger" type="submit">
                <?= t('Run Tests Again') ?>
                <i class="fa fa-refresh"></i>
            </button>
        </form>

    </div>

    <div class="spacer-row-6"></div>

        </div>
    </div>

<?php
} else {
    ?>

    <div class="ccm-install-title">
        <ul class="breadcrumb">
            <li><?= t('Install concrete5') ?></li>
            <li class="active"><?= t('Choose Language') ?></li>
        </ul>
    </div>

    <div id="ccm-install-intro">
        <form method="post" id="ccm-install-language-form" action="<?= $view->url('/install', 'select_language') ?>">
        <div class="form-group">
            <div class="input-group-lg input-group">
                <?= $form->select('locale', $locales, 'en_US'); ?>
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
        </form>
    </div>

    <?php
}?>
