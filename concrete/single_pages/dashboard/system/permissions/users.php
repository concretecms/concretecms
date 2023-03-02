<?php defined('C5_EXECUTE') or die("Access Denied.");
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
?>

<?php ob_start(); ?>
<?=Loader::element('permission/help');?>
<?php $help = ob_get_contents(); ?>
<?php ob_end_clean(); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Permissions'), $help, 'span8 offset2', false)?>
<form method="post" action="<?=$view->action('save')?>" role="form">
	<?=Loader::helper('validation/token')->output('save_permissions')?>

	<?php
    $tp = new TaskPermission();
    if ($tp->canAccessTaskPermissions()) {
        ?>
		<?php Loader::element('permission/lists/user')?>
	<?php
    } else {
        ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<?php
    } ?>

	<div class="ccm-dashboard-form-actions-wrapper">
	    <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/system/permissions/users')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
	    </div>
	</div>
</form>

<script type="text/template" id="access-warning-template">
    <div>
        <% for (const {message, id, name} of warnings) { %>
            <h6 class="col-4"><%= name %></h6>
            <div>
                <p class="alert alert-warning"><%= message %></p>
            </div>
        <% } %>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
            <button class="btn btn-danger float-end accept"><?= t('I understand') ?></button>
        </div>
    </div>
</script>

<script>
    (function() {
        const warnings = JSON.parse(<?= json_encode(json_encode(isset($permissionWarnings) ? $permissionWarnings : [])) ?>)
        let skipWarnings = false
        function populateCache() {
            const cache = {}
            for (const keyId in warnings) {
                cache[keyId] = $('[name="pkID[' + keyId + ']"]').val()
            }
            return cache
        }

        const cache = populateCache()
        const form = $('table.ccm-permission-grid').closest('form')

        form.on('submit', function() {
            if (skipWarnings) {
                return true
            }

            const changes = populateCache();
            const warn = Object.keys(changes)
                .map(k => changes[k] !== cache[k] ? k : null)
                .filter(k => k !== null)
                .map(k => ({
                   message: warnings[k],
                   id: k,
                   name: $('a[data-pkid="' + k + '"]').attr('dialog-title'),
                }))

            if (warn.length) {
                const warningHtml = _.template($('#access-warning-template').text())({
                    warnings: warn
                })

                const dialog = $(warningHtml)
                dialog.find('button.accept').click(function() {
                    skipWarnings = true
                    form.trigger('submit')
                })

                jQuery.fn.dialog.open({
                    title: "<?= t('Access Warnings') ?>",
                    element: dialog,
                    modal: true,
                    width: 500,
                    height: 380
                })

                return false
            }

            return true
        })

    }())
</script>
