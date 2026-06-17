<form action="<?= View::action('save') ?>" method="post">
    <?php
    Core::make('token')->output('appearance');
    ?>
    <fieldset>
        <div class="form-group">
            <legend><?=t('Toolbar')?></legend>
            <div class="form-check">
                <input class="form-check-input" id="show_titles" name="show_titles" value="1" type="checkbox" <?= $show_titles ? 'checked' : '' ?> />
                <label class="form-check-label" for="show_titles">
                    <?= t('Enable Toolbar Titles') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="show_tooltips" name="show_tooltips" value="1" type="checkbox" <?= $show_tooltips ? 'checked' : '' ?> />
                <label class="form-check-label" for="show_tooltips">
                    <?= t('Enable Prominent Toolbar Tooltips') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="increase_font_size" name="increase_font_size" value="1" type="checkbox" <?= $increase_font_size ? 'checked' : '' ?> />
                <label class="form-check-label" for="increase_font_size">
                    <?= t('Increase Toolbar Font Size') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('File Manager')?></legend>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" id="full_lisiting_thumbnails" name="full_lisiting_thumbnails" value="1" type="checkbox" <?= $full_lisiting_thumbnails ? 'checked' : '' ?> />
                <label class="form-check-label" for="full_lisiting_thumbnails">
                    <?= t('Enable Full Size Image Thumbnails') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="preview_popover" name="preview_popover" value="1" type="checkbox" <?= $preview_popover ? 'checked' : '' ?> />
                <label class="form-check-label" for="preview_popover">
                    <?= t('Enable Preview Image Popover') ?>
                </label>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <div class="form-group">
            <legend><?=t('Dashboard Color Scheme')?></legend>
            <div class="form-check">
                <input class="form-check-input" id="colorSchemeAuto" name="colorScheme" value="auto" type="radio" <?= $colorScheme === 'auto' ? 'checked' : '' ?> />
                <label class="form-check-label" for="colorSchemeAuto">
                    <?= t('Auto – use system preferences to determine Dashboard theme color scheme.') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="colorSchemeLight" name="colorScheme" value="light" type="radio" <?= $colorScheme === 'light' ? 'checked' : '' ?> />
                <label class="form-check-label" for="colorSchemeLight">
                    <?= t('Light Theme') ?>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="colorSchemeDark" name="colorScheme" value="dark" type="radio" <?= $colorScheme === 'dark' ? 'checked' : '' ?> />
                <label class="form-check-label" for="colorSchemeDark">
                    <?= t('Dark Theme') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary">
                <?=t('Save')?>
            </button>
        </div>
    </div>
</form>
