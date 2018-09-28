<form action="<?= View::action('save') ?>" method="post">
    <?php
    Core::make('token')->output('accessibility');
    ?>
    <div class="checkbox">
        <label>
            <input name="show_titles" value="1" type="checkbox" <?= $show_titles ? 'checked' : '' ?> />
            <?= t('Enable Toolbar Titles') ?>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input name="show_tooltips" value="1" type="checkbox" <?= $show_tooltips ? 'checked' : '' ?> />
            <?= t('Enable Prominent Toolbar Tooltips') ?>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input name="increase_font_size" value="1" type="checkbox" <?= $increase_font_size ? 'checked' : '' ?> />
            <?= t('Increase Toolbar Font Size') ?>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input name="display_help" value="1" type="checkbox" <?= $display_help ? 'checked' : '' ?> />
            <?= t('Enable Help') ?>
        </label>
    </div>

    <hr/>

    <fieldset>
        <label class="control-label"><?=t('File Manager')?></label>
        <div class="checkbox">
            <label>
                <input name="full_lisiting_thumbnails" value="1" type="checkbox" <?= $full_lisiting_thumbnails ? 'checked' : '' ?> />
                <?= t('Enable Full Size Image Thumbnails') ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input name="preview_popover" value="1" type="checkbox" <?= $preview_popover ? 'checked' : '' ?> />
                <?= t('Enable Preview Image Popover') ?>
            </label>
        </div>
    </fieldset>
    <h2></h2>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary">
                <?=t('Save')?>
            </button>
        </div>
    </div>
</form>
