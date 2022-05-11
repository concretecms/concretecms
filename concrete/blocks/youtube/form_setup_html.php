<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Support\Facade\Application;

/** @var string | null $title */
/** @var string | null $videoURL */
/** @var string | null $vHeight */
/** @var string | null $vWidth */
/** @var string | null $sizing */
/** @var bool | null $startTimeEnabled */
/** @var string | null $startTime */
/** @var bool | null $noCookie */
/** @var bool | null $autoplay */
/** @var string | null $color  */
/** @var int | null $iv_load_policy */
/** @var bool | null $loopEnd */
/** @var bool | null $lazyLoad */
/** @var bool | null $rel */
/** @var bool | null $showCaptions */
/** @var Concrete\Core\Form\Service\Form $form */

if (empty($vWidth)) {
    $vWidth = '640';
}

if (empty($vHeight)) {
    $vHeight = '360';
}

if (empty($sizing)) {
    $sizing = '16:9';
}

$app = Application::getFacadeApplication();
/** @var UserInterface $ui */
$ui = $app->make(UserInterface::class);

echo $ui->tabs([
    ['video', t('Video'), true],
    ['settings', t('Settings')],
]);

?>

<div class="tab-content">
    <div class="tab-pane active" id="video" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label('videoURL', t("YouTube URL")); ?>
            <?php echo $form->text('videoURL', $videoURL ?? '', ['required' => 'required']); ?>
        </div>

        <div class="form-group">
            <label class="control-label form-label">
                <?php echo t('Size'); ?>
            </label>

            <div class="form-check">
                <?php echo $form->radio('sizing', '16:9', $sizing, ["id" => "sizingWhiteScreen", "name" => "sizing"]); ?>
                <?php echo $form->label('sizingWhiteScreen', t('16:9 (widescreen)'), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio('sizing', '4:3', $sizing, ["id" => "sizingRegular", "name" => "sizing"]); ?>
                <?php echo $form->label('sizingRegular', t('4:3'), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio('sizing', 'fixed', $sizing, ["id" => "sizingFixed", "name" => "sizing"]); ?>
                <?php echo $form->label('sizingFixed', t('Fixed Size (non-responsive)'), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('title', t("Title")); ?>
            <?php echo $form->text('title', $title ?? ''); ?>
        </div>

        <div id="fixedsizes" class="<?php echo $sizing == 'fixed' ? '' : 'd-none'; ?>">
            <div class="form-group">
                <?php echo $form->label('YouTubeVideoWidth', t("Width"), ["class" => "form-check-label"]); ?>

                <div class="input-group">
                    <?php echo $form->number('vWidth', $vWidth, ["id" => "YouTubeVideoWidth", "name" => "vWidth", "min" => 0, "step" => "1"]); ?>

                    <span class="input-group-text">
                        <?php echo t('px'); ?>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('YouTubeVideoHeight', t("Height"), ["class" => "form-check-label"]); ?>

                <div class="input-group">
                    <?php echo $form->number('vHeight', $vHeight, ["id" => "YouTubeVideoHeight", "name" => "vHeight", "min" => 0, "step" => "1"]); ?>

                    <span class="input-group-text">
                        <?php echo t('px'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="settings" role="tabpanel">
        <fieldset>
            <legend>
                <?php echo t('Appearance'); ?>
            </legend>

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <div class="form-check">
                            <?php echo $form->checkbox('controls', '1', ($controls ?? true)); ?>
                            <?php echo $form->label("controls", t('Show controls'), ["class" => "form-check-label"]); ?>
                        </div>

                        <div class="form-check">
                            <?php
                            $additionalAttributes = [];

                            if (isset($color) && $color == 'white') {
                                $additionalAttributes['disabled'] = 'disabled';
                            }

                            echo $form->checkbox('modestbranding', '1', ($modestbranding ?? true), $additionalAttributes); ?>

                            <?php echo $form->label("modestbranding", t('Hide YouTube Logo'), ["class" => "form-check-label"]); ?>
                        </div>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="form-group controls-only <?php echo isset($controls) && $controls == 0 ? 'd-none' : ''; ?>">
                        <?php echo $form->label('color', t('Progress Bar Color'), ["class" => "form-check-label"]); ?>
                        <?php echo $form->select('color', ['red' => t('Red'), 'white' => t('White')], isset($color) ? $color : null); ?>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo t('Playback Options'); ?>
            </legend>

            <div class="form-group">
                <div class="form-check">
                    <?php echo $form->checkbox('rel', '1', !empty($rel)); ?>
                    <?php echo $form->label('rel', t('Show related videos from different channels when playback ends'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-check">
                    <?php echo $form->checkbox('iv_load_policy', '1', isset($iv_load_polict) && $iv_load_policy == 3); ?>
                    <?php echo $form->label("iv_load_policy", t('Hide annotations by default'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-check">
                    <?php echo $form->checkbox('autoplay', '1', !empty($autoplay)); ?>
                    <?php echo $form->label("autoplay", t('Automatically play'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-check">
                    <?php echo $form->checkbox('loopEnd', '1', !empty($loopEnd)); ?>
                    <?php echo $form->label("loopEnd", t('Loop video'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-check">
                    <?php echo $form->checkbox('showCaptions', '1', !empty($showCaptions)); ?>
                    <?php echo $form->label("showCaptions", t('Show captions'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-check">
                    <?php echo $form->checkbox('startTimeEnabled', '1', !empty($startTimeEnabled)); ?>
                    <?php echo $form->label("startTimeEnabled", t('Start video at:'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->text('startTime', $startTime ?? null); ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo t('Privacy Options'); ?>
            </legend>

            <div class="form-check">
                <?php echo $form->checkbox('noCookie', '1', ($noCookie ?? false)); ?>
                <?php echo $form->label("noCookie", t('No cookie'), ["class" => "form-check-label"]); ?>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo t('Loading Options'); ?>
            </legend>

            <div class="form-check">
                <?php echo $form->checkbox('lazyLoad', '1', ($lazyLoad ?? false)); ?>
                <?php echo $form->label("lazyLoad", t('Lazy load video'), ["class" => "form-check-label"]) ?>
            </div>
        </fieldset>
    </div>
</div>

<script>
    $(function () {
        $('input[name=sizing]').change(function () {
            if ($('#sizingFixed').is(':checked')) {
                $('#fixedsizes').removeClass('d-none');
            } else {
                $('#fixedsizes').addClass('d-none');
            }
        });

        $('#controls').change(function () {
            if ($('#controls').is(':checked')) {
                $('.controls-only').removeClass('d-none');
            } else {
                $('.controls-only').addClass('d-none');
            }
        });

        $('#color').change(function () {
            if ($(this).val() === 'white') {
                $('#modestbranding').prop('disabled', 'disabled').prop('checked', false);
            } else {
                $('#modestbranding').removeAttr('disabled')
            }
        });

        $('#ccm-form-submit-button').click(function () {
            if (!$('#videoURL').val()) {
                $('#video').trigger("click");
            }
        });
    });
</script>
