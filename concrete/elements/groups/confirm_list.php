<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\User\Group\Group[] $groups
 */

// used when confirming bulk group operations.. would you like to do xyz to the following groups??

if (is_array($groups ?? null) && $groups !== []) {
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= t('Group Name') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($groups as $group) {
                ?>
                <tr>
                    <td><?= $group->getGroupDisplayName(true, false) ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
