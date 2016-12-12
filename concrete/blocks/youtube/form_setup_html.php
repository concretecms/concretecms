<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
if (!$vWidth) {
    $vWidth=640;
}
if (!$vHeight) {
    $vHeight=360;
}

if (!$sizing) {
    $sizing = '16:9';
}
?>

<?php
echo Core::make('helper/concrete/ui')->tabs(array(
    array('video', t('Video'), true),
    array('settings', t('Settings'))
));
?>

<div class="ccm-tab-content" id="ccm-tab-content-video">
    <fieldset>
        <legend><?php echo t('YouTube URL'); ?></legend>
        <div class="form-group">
            <?php echo $form->text('videoURL', $videoURL, array('required'=>'required'));?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?php echo t('Size'); ?></legend>
        <div class="radio">
            <label>
                <?php echo $form->radio('sizing', '16:9', $sizing); ?>
                <?php echo t("16:9 (widescreen)")?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?php echo $form->radio('sizing', '4:3', $sizing); ?>
                <?php echo t("4:3")?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?php echo $form->radio('sizing', 'fixed', $sizing); ?>
                <?php echo t("Fixed Size (non-responsive)")?>
            </label>
        </div>
        <div id="fixedsizes" class="row <?php echo ($sizing == 'fixed' ? '' : 'hidden');?>">
            <div class="form-group col-xs-6">
                <label><?php echo t('Width')?></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="YouTubeVideoWidth" name="vWidth" value="<?php echo $vWidth?>" />
                    <div class="input-group-addon"><?php echo t('px');?></div>
                </div>
            </div>
            <div class="form-group col-xs-6">
                <label><?php echo t('Height')?></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="YouTubeVideoHeight" name="vHeight" value="<?php echo $vHeight?>" />
                    <div class="input-group-addon"><?php echo t('px');?></div>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<div class="ccm-tab-content" id="ccm-tab-content-settings">
    <fieldset>
        <legend><?php echo t('Appearance'); ?></legend>
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <div class="checkbox">
                    <label>
                        <?php echo $form->checkbox('showinfo', 1, (isset($showinfo) ? $showinfo : true)); ?>
                        <?php echo t("Show video information")?>
                    </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <?php echo $form->checkbox('controls', 1, (isset($controls) ? $controls : true)); ?>
                            <?php echo t("Show controls")?>
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <?php
                            $disabledattr = array();
                            if ($color == 'white') {
                                $disabledattr['disabled'] = 'disabled';
                            }
                            echo $form->checkbox('modestbranding', 1,  (isset($modestbranding) ? $modestbranding : true),$disabledattr); ?>
                            <?php echo t("Hide YouTube Logo")?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="form-group controls-only <?php echo (isset($controls) && $controls == 0 ? 'hidden' : '');?>">
                    <?php  echo $form->label('color', t('Progress Bar Color'))?>
                    <?php  echo $form->select('color', array('red'=>t('Red'), 'white'=>t('White')), $color)?>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><?php echo t('Playback Options'); ?></legend>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox('rel', 1, $rel); ?>
                    <?php echo t("Show related videos when playback ends") ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox('iv_load_policy', 1, ($iv_load_policy == 3)); ?>
                    <?php echo t("Hide annotations by default") ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox('autoplay', 1, $autoplay); ?>
                    <?php echo t("Automatically play") ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox('loopEnd', 1, $loopEnd); ?>
                    <?php echo t("Loop video") ?>
                </label>
            </div>

        </div>
    </fieldset>
</div>

<script>
    $(document).ready(function() {
        $('#sizing1, #sizing2, #sizing3').change(function(){
            if ($('#sizing3').is(':checked')) {
                $('#fixedsizes').removeClass('hidden');
            } else {
                $('#fixedsizes').addClass('hidden');
            }
        });

        $('#controls').change(function(){
            if ($('#controls').is(':checked')) {
                $('.controls-only').removeClass('hidden');
            } else {
                $('.controls-only').addClass('hidden');
            }
        });

        $('#color').change(function(){
            if ($(this).val() == 'white') {
                $('#modestbranding').prop('disabled','disabled').prop('checked',false);
            } else {
                $('#modestbranding').removeProp('disabled');
            }
        });

        $('#ccm-form-submit-button').click(function(){
            if (!$('#videoURL').val()) {
                $('[data-tab="video"').click();
            }
        });
    });
</script>