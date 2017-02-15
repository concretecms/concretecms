<fieldset class="ccm-attribute ccm-attribute-date-time">

    <legend><?=t('Date/Time Options')?></legend>

    <div class="form-group">
        <div class="checkbox">
            <label class="checkbox">
                <?=$form->checkbox('akUseNowIfEmpty', '1', isset($akUseNowIfEmpty) ? $akUseNowIfEmpty : false)?>
                <?=t('Suggest the current date/time if empty')?>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?=$form->label('akDateDisplayMode', t('Ask User For'))?>
        <?php
        $akDateDisplayModeOptions = [
            'date_time' => t('Both Date and Time'),
            'date' => t('Date Only'),
            'text' => t('Text Input Field'),
        ];
        if (!isset($akDateDisplayMode) || !in_array($akDateDisplayMode, $akDateDisplayModeOptions)) {
            $akDateDisplayMode = key($akDateDisplayModeOptions);
        }
        ?>
        <?=$form->select('akDateDisplayMode', $akDateDisplayModeOptions, $akDateDisplayMode, [
            'onchange' => <<<'EOT'
if (this.value === 'date_time') {
    $('#akTimeResolution').removeAttr('disabled');
} else {
    $('#akTimeResolution').attr('disabled', 'disabled');
}
EOT
            ,
        ])?>
    </div>

    <div class="form-group">
        <?=$form->label('akTimeResolution', t('Time Resolution'))?>
        <?php
        $akTimeResolutionOptions = [];
        foreach ([
            // With seconds
            1, 5, 10, 15, 30, 60,
            // Minutes only
            60 * 1, 60 * 5, 60 * 10, 60 * 15, 60 * 30,
            // Hours only
            60 * 60, 60 * 60 * 3, 60 * 60 * 4, 60 * 60 * 6, 60 * 60 * 12
        ] as $totalSeconds) {
            $seconds = $totalSeconds;
            $hours = (int) ($totalSeconds / 3600);
            $seconds -= $hours * 3600;
            $minutes = (int) ($seconds / 60);
            $seconds -= $minutes * 60;
            $parts = [];
            if ($hours !== 0) {
                $parts[] = Punic\Unit::format($hours, 'duration/hour', 'long');
            }
            if ($minutes !== 0) {
                $parts[] = Punic\Unit::format($minutes, 'duration/minute', 'long');
            }
            if (empty($parts) || $seconds !== 0) {
                $parts[] = Punic\Unit::format($seconds, 'duration/second', 'long');
            }
            $akTimeResolutionOptions[$totalSeconds] = Punic\Misc::join($parts);
        }
        $akTimeResolutionExtra = [];
        if ($akDateDisplayMode !== 'date_time') {
            $akTimeResolutionExtra['disabled'] = 'disabled';
        }
        if (!isset($akTimeResolution) || !isset($akTimeResolutionOptions[$akTimeResolution])) {
            $akTimeResolution = '60';
        }
        ?>
        <?=$form->select('akTimeResolution', $akTimeResolutionOptions, $akTimeResolution, $akTimeResolutionExtra)?>
    </div>

</fieldset>
