<?php defined('C5_EXECUTE') or die('Access Denied.');
if (!strlen($titleText)) {
    $titleText = $controller->getTitleText();
}
?>

<div class="form-group">
    <label class="control-label"><?php echo t("Custom Title"); ?></label>
    <div class="checkbox">
        <label>
        <?php echo $form->checkbox('useCustomTitle', 1, $useCustomTitle); ?>
        <?php echo t('Override page name with custom title?'); ?>
        </label>
    </div>
</div>

<div class="form-group">
    <?php echo $form->label('titleText', t('Custom Title Text')); ?>
    <?php echo $form->text('titleText', $titleText ? $titleText : $controller->getTitleText()); ?>
</div>

<div class="form-group">
    <?php echo $form->label('formatting', t('Formatting Style')); ?>
    <select class="form-control" name="formatting" id="formatting">
        <option value="h1" <?php echo $this->controller->formatting == 'h1' ? 'selected' : ''; ?>><?php echo t('H1'); ?></option>
        <option value="h2" <?php echo $this->controller->formatting == 'h2' ? 'selected' : ''; ?>><?php echo t('H2'); ?></option>
        <option value="h3" <?php echo $this->controller->formatting == 'h3' ? 'selected' : ''; ?>><?php echo t('H3'); ?></option>
        <option value="h4" <?php echo $this->controller->formatting == 'h4' ? 'selected' : ''; ?>><?php echo t('H4'); ?></option>
        <option value="h5" <?php echo $this->controller->formatting == 'h5' ? 'selected' : ''; ?>><?php echo t('H5'); ?></option>
        <option value="h6" <?php echo $this->controller->formatting == 'h6' ? 'selected' : ''; ?>><?php echo t('H6'); ?></option>
    </select>
</div>

<div class="form-group">
    <label class="control-label"><?php echo t("Page Title Filtering"); ?></label>
    <div class="checkbox">
        <label>
        <?php echo $form->checkbox('useFilterTitle', 1, $useFilterTitle); ?>
        <?php echo t('Enable other blocks to filter this page title'); ?>
        </label>
    </div>

    <div class="well filterTitleOptions">
        <div class="checkbox">
            <label>
            <?php echo $form->checkbox('useFilterTopic', 1, $useFilterTopic); ?>
            <?php echo t('Topic'); ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
            <?php echo $form->checkbox('useFilterTag', 1, $useFilterTag); ?>
            <?php echo t('Tag'); ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
            <?php echo $form->checkbox('useFilterDate', 1, $useFilterDate); ?>
            <?php echo t('Date'); ?>
            </label>
        </div>

        <div class="filterTopicOptions">
            <div class="form-group">
                <?php echo $form->label('topicTextFormat', t('Topic Text Formatting')); ?>
                <?php echo $form->select('topicTextFormat',
                    array(
                        0 => t('Default'),
                        'upperWord' => t('Capitalize all words'),
                        'upperFirst' => t('Capitalize first word'),
                        'lowercase' => t('Lowercase'),
                        'uppercase' => t('Uppercase')
                    ), $topicTextFormat); ?>
            </div>
        </div>
        <div class="filterTagOptions">
            <div class="form-group">
                <?php echo $form->label('tagTextFormat', t('Tag Text Formatting')); ?>
                <?php echo $form->select('tagTextFormat',
                    array(
                        'upperWord' => t('Capitalize all words'),
                        'upperFirst' => t('Capitalize first word'),
                        'lowercase' => t('Lowercase'),
                        'uppercase' => t('Uppercase')
                    ), $tagTextFormat); ?>
            </div>
        </div>
        <div class="filterDateOptions">
            <div class="form-group">
                <?php echo $form->label('dateTextFormat', t('Date Text Formatting')); ?>
                <?php echo $form->select('dateTextFormat',
                    array(
                        0 => t('Default'),
                        'upperWord' => t('Capitalize all words'),
                        'upperFirst' => t('Capitalize first word'),
                        'lowercase' => t('Lowercase'),
                        'uppercase' => t('Uppercase')
                    ), $dateTextFormat); ?>
            </div>
            <div class="form-group">
                <?php echo $form->label('filterDateFormat', t('Date Year and Month Format')); ?>
                <?php echo $form->text('filterDateFormat', $filterDateFormat ? $filterDateFormat : t('F Y')); ?>
            </div>
            <div class="help-block"><?php echo sprintf(t('See the formatting options for year and month at %s.'), '<a href="http://www.php.net/date" target="_blank">php.net/date</a>'); ?></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($('#useFilterTitle').prop('checked')) {
        $('.filterTitleOptions').show();
    } else {
        $('.filterTitleOptions').hide();
    }
    $('#useFilterTitle').on('change', function() {
        $('.filterTitleOptions').toggle();
    });

    if ($('#useFilterTopic').prop('checked')) {
        $('.filterTopicOptions').show();
    } else {
        $('.filterTopicOptions').hide();
    }
    $('#useFilterTopic').on('change', function() {
        $('.filterTopicOptions').toggle();
    });

    if ($('#useFilterTag').prop('checked')) {
        $('.filterTagOptions').show();
    } else {
        $('.filterTagOptions').hide();
    }
    $('#useFilterTag').on('change', function() {
        $('.filterTagOptions').toggle();
    });

    if ($('#useFilterDate').prop('checked')) {
        $('.filterDateOptions').show();
    } else {
        $('.filterDateOptions').hide();
    }
    $('#useFilterDate').on('change', function() {
        $('.filterDateOptions').toggle();
    });
});
</script>
