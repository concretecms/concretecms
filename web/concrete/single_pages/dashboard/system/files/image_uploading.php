<? defined('C5_EXECUTE') or die("Access Denied."); ?>

    <?=Core::make('helper/concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Image Uploading'), false, 'span8 offset2', false)?>

    <form method="post" id="file-access-extensions" action="<?=$view->action('save')?>" role="form">
        <?=$validation_token->output('image_uploading');?>

        <fieldset>
            <div class="row">
                <div class="col-md-12">
                    <legend>Image Resizing</legend>
                 </div>

                <div class="col-md-5">
                    <div class="checkbox">
                        <label>
                            <?=$form->checkbox('restrict_uploaded_image_sizes', '1', $restrict_uploaded_image_sizes)?>
                            <span><?=t('Automatically resize uploaded images')?></span>
                        </label>
                    </div>

                    <div id="resizing-values" <?=($restrict_uploaded_image_sizes ?  '' : 'style="display: none"');?>>
                        <div class="form-group">
                            <label for="restrict_max_width" class="control-label"><?= t('Maximum Width') ?></label>

                            <div class="input-group">
                                <input class="form-control" type="number" name="restrict_max_width" placeholder="1920" value="<?=$restrict_max_width?>">
                                <div class="input-group-addon">px</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="restrict_max_height" class="control-label"><?= t('Maximum Height') ?></label>

                            <div class="input-group">
                                <input class="form-control" type="number" name="restrict_max_height" placeholder="1080" value="<?=$restrict_max_height?>">
                                <div class="input-group-addon">px</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="restrict_resize_quality" class="control-label"><?= t('Image Compression Quality') ?></label>

                            <div class="input-group">
                                <input class="form-control" type="number" name="restrict_resize_quality" placeholder="85" value="<?=$restrict_resize_quality?>">
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </fieldset>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit" value="file-access-extensions"><?=t('Save')?></button>
            </div>
        </div>
    </form>

    <?=Core::make('helper/concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<script type="text/javascript">
$("input[name=restrict_uploaded_image_sizes]").click(function() {
   if ($(this).is(":checked")) {
       $("#resizing-values").show();
   } else {
       $("#resizing-values").hide();
   }
});
</script>

