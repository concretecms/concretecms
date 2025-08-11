<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?=$view->action('save')?>">
    <?=$token->output('save')?>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="w-75"><?=t('Name')?></th>
                    <?php foreach ($categories as $category) { ?>
                        <th class="text-nowrap text-center"><?= $category->getDisplayName() ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $template) { ?>
                    <tr>
                        <td><?= $template->getDisplayName() ?></td>
                        <?php foreach ($categories as $category) { ?>
                            <td class="text-center">
                                <input type="checkbox"
                                       <?php if (in_array($template->getId(), $categoryTemplates[$category->getId()])) { ?>
                                            checked
                                       <?php } ?>
                                        value="<?=$template->getId()?>"
                                       name="category_template[<?= $category->getID() ?>][<?= $template->getID() ?>]">
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>

</form>
