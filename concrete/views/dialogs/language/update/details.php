<?php
// Arguments
/* @var Concrete\Core\Localization\Service\Date $dateHelper */
/* @var Concrete\Core\Localization\Translation\Local\Stats $local */
/* @var Concrete\Core\Localization\Translation\Remote\Stats $remote */

?>
<table class="table">
    <caption><h4><?= t('Local translations file') ?></h4></caption>
    <tbody>
        <tr>
            <th><?= t('File path') ?></th>
            <td><code><?= h($local->getFileDisplayName()) ?></code></td>
        </tr>
        <tr>
            <th><?= t('Version') ?></th>
            <td><?= ($local->getVersion() === '') ? '' : ('<code>' . h($local->getVersion()) . '</code>') ?></code></td>
        </tr>
        <tr>
            <th><?= t('Updated on') ?></th>
            <td><?= ($local->getUpdatedOn() === null) ? '' : $dateHelper->formatPrettyDateTime($local->getUpdatedOn(), true, true) ?></td>
        </tr>
    </tbody>
</table>
<table class="table">
    <caption><h4><?= t('Remote translations file') ?></h4></caption>
    <tbody>
        <tr>
            <th><?= t('Version') ?></th>
            <td><?= ($remote->getVersion() === '') ? '' : ('<code>' . h($remote->getVersion()) . '</code>') ?></td>
        </tr>
        <tr>
            <th><?= t('Updated on') ?></th>
            <td><?= ($remote->getUpdatedOn() === null) ? '' : $dateHelper->formatPrettyDateTime($remote->getUpdatedOn(), true, true) ?></td>
        </tr>
        <tr>
            <th><?= t('Total strings') ?></th>
            <td><?= $remote->getTotal() ?></td>
        </tr>
        <tr>
            <th><?= t('Translated strings') ?></th>
            <td><?= $remote->getTranslated() ?></td>
        </tr>
        <tr>
            <th><?= t('Untranslated strings') ?></th>
            <td><?= $remote->getTotal() - $remote->getTranslated() ?></td>
        </tr>
        <tr>
            <th><?= t('Translation progress') ?></th>
            <td><?= $remote->getProgress() ?>%</td>
        </tr>
    </tbody>
</table>
