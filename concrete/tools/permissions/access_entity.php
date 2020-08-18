<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use Concrete\Core\Permission\Checker as PermissionChecker;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
$form = $app->make('helper/form');
$tu = $app->make('helper/concrete/user');
$dt = $app->make('helper/form/date_time');

$request = $app->make(Request::class);

$tp = new PermissionChecker();
if (!$tu->canAccessUserSearchInterface() && !$tp->canAccessGroupSearch()) {
    die(t('You do not have user search or group search permissions.'));
}

$pae = false;
if (!empty($peID = $request->get('peID'))) {
    $pae = PermissionAccessEntity::getByID($peID);
}

if (!is_object($pae)) {
    $pae = false;
}

$pd = false;
if (!empty($pdID = $request->get('pdID'))) {
    $pd = PermissionDuration::getByID($pdID);
}
if (!is_object($pd)) {
    $pd = false;
}

if ($request->request->get('task') === 'save_permissions') {
    $js = $app->make('helper/json');
    $r = new stdClass();

    if (is_object($pae)) {
        $pd = PermissionDuration::createFromRequest();
    } else {
        $r->error = true;
        $r->message = t('You must choose who this permission is for.');
    }

    if (empty($r->error)) {
        $r->peID = $pae->getAccessEntityID();
        if (is_object($pd)) {
            $r->pdID = $pd->getPermissionDurationID();
        } else {
            $r->pdID = 0;
        }
    }

    echo $js->encode($r);
    exit;
}

?>
<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">
    <form id="ccm-permissions-access-entity-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity">
        <input type="hidden" name="task" value="save_permissions" />
        <?=$form->hidden('accessType'); ?>
        <?=$form->hidden('peID'); ?>
        <?=$form->hidden('pdID'); ?>

        <h4><?= t('Access')?></h4>
        <p><?= t('Who gets access to this permission?') ?></p>

        <div id="ccm-permissions-access-entity-label"><?php if (is_object($pae)) {
            ?><div class="alert alert-info"><?= $pae->getAccessEntityLabel() ?></div><?php
        } else {
            ?><div class="alert alert-warning"><?= t('None Selected') ?></div><?php
        } ?></div>

<?php
if (!is_object($pae)) {
    ?>

    <div class="btn-group">
        <a class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fas fa-plus"></i> <?=t('Select')?> <span class="caret"></span>
        </a>
        <div class="dropdown-menu">
        <?php
            $category = PermissionKeyCategory::getByHandle($request->get('pkCategoryHandle'));
            $entitytypes = PermissionAccessEntityType::getList($category);
            foreach ($entitytypes as $type) {
                ?>
                <?= $type->getAccessEntityTypeLinkHTML() ?>
            <?php
            }
        ?>
        </div>
    </div>
    <br/><br/>

<?php
    foreach ($entitytypes as $type) {
        View::element(
            "permission/access/entity/types/{$type->getAccessEntityTypeHandle()}",
            ['type' => $type],
            ($type->getPackageID() > 0) ? $type->getPackageHandle() : null
        );
    }
}

    if (!$request->request->has('disableDuration') && !$request->query->has('disableDuration')) {
        ?>
        <h4><?=t('Time Settings')?></h4>

        <?php View::element('permission/duration', ['pd' => $pd]);
    }
?>
        <div class="dialog-buttons">
            <input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?=t('Cancel')?>" class="btn btn-secondary" />
            <input type="submit" onclick="$('#ccm-permissions-access-entity-form').submit()" value="<?=t('Save')?>" class="btn btn-primary ml-auto" />
        </div>
    </form>
</div>

<script type="text/javascript">
	$("#ccm-permissions-access-entity-form").ajaxForm({
		beforeSubmit: function(r) {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			if (r.error) {
				ConcreteAlert.dialog('<?=t('Error')?>', r.message);
			} else {
				if (typeof(ccm_addAccessEntity) == 'function') {
					ccm_addAccessEntity(r.peID, r.pdID, <?= json_encode(h((string) $request->get('accessType', ''))) ?>);
				} else {
					alert(r.peID);
					alert(r.pdID);
				}
			}
		}
	});
</script>
