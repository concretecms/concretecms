<?php

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Workflow\Request\ChangePagePermissionsRequest $workflowRequest
 * @var Concrete\Core\Page\Page $page
 */

?>
<div class="ccm-ui">
    <table class="ccm-permission-grid table table-striped">
        <tbody>
            <?php
            $ps = $workflowRequest->getPagePermissionSet();
            foreach($ps->getPermissionAssignments() as $pkID => $paID) {
                $pk = Key::getByID($pkID);
                $pk->setPermissionObject($page);
                $pa = Access::getByID($paID, $pk);
                ?>
                <tr>
                    <td class="ccm-permission-grid-name"><strong><?= $pk->getPermissionKeyDisplayName() ?></strong></td>
                    <td>
                        <?php
                        View::element('permission/labels', ['pa' => $pa, 'pk' => $pk]);
                        ?>
                   </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
