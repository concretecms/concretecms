<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<form method="post" action="<?php echo $controller->action('synchronize'); ?>">
    <?=$token->output('synchronize')?>
    <div class="ccm-dashboard-header-buttons">
        <button class="btn btn-secondary" type="submit" name="action" value="reload"><?php echo t('Synchronize Scopes'); ?></button>
    </div>
</form>

<p><?=t('The following API scopes are installed and available to integrations in your site.')?></p>

<table class="table table-striped">
    <thead>
    <tr>
        <th><?=t('Scope')?></th>
        <th><?=t('Description')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($scopes as $scope) { ?>
    <tr>
        <td class="w-25"><b><?=$scope->getIdentifier()?></b></td>
        <td class="w-75"><?=t($scope->getDescription())?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>
