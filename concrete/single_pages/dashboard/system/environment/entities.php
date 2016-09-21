<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<fieldset>
    <legend><?=t('Entity Locations')?></legend>

        <?php foreach($drivers as $namespace => $driver) { ?>

            <h4><?=t('Namespace: %s', $namespace)?></h4>

            <table class="" style="width: 100%; word-wrap:break-word; table-layout: fixed">
            <?php foreach($driver->getPaths() as $path) { ?>
                <tr>
                    <td style="width: 75%"><?=$path?></td>
                    <td style="width: 25%"></td>
                </tr>
            <?php } ?>
            </table>

        <?php } ?>

</fieldset>