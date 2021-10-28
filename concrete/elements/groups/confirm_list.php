<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\User\Group\Group;

/** @var Group[] $groups */

// used when confirming bulk group operations.. would you like to do xyz to the following groups??
if (is_array($groups)) { ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>
                <?php echo t('Group Name') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($groups as $group) { ?>
            <tr>
                <td>
                    <?php echo $group->getGroupName(); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
