<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $types \Concrete\Core\Menu\Type\TypeInterface[]
 */

if (count($menus)) { ?>

    <table class="table">
    <thead>
    <tr>
        <th><?=t("Name")?></th>
        <th><?=t('Type')?></th>
    </tr>
    </thead>
        <tbody>
        <?php foreach ($menus as $menu) { ?>
        <tr>
            <td><a href="<?=URL::to('/dashboard/system/basics/menus/details', $menu->getId())?>"><?=$menu->getName()?></a></td>
            <td>
                <?=$menu->getType()?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } else { ?>

    <p><?=t('You have not created any navigation menus.')?></p>
<?php }

?>


<div class="ccm-dashboard-header-buttons">
    <button class="btn btn-primary" @click="showAddMenu"><?php echo t("Add Menu") ?></button>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="add-menu-modal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="post" action="<?=$view->action('add_menu')?>">
                <?=$token->output('add_menu')?>
                <div class="modal-header">
                    <h4 class="modal-title"><?= t('Add Menu') ?></h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="menuName"><?=t('Name')?></label>
                        <input class="form-control" id="menuName" required name="name" type="text">
                    </div>
                    <div>
                        <label class="form-label" for="menuType"><?=t('Type')?></label>
                        <select required class="form-select" id="menuType" name="type">
                            <option value=""><?=t("** Select Type")?></option>
                            <?php foreach ($types as $type) { ?>
                                <option value="<?=$type->getDriverHandle()?>"><?=$type->getName()?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between w-100">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary border float-start"><?php echo t('Cancel') ?></button>
                    <button class="ms-auto btn btn-primary" name="action" value="run_tests" type="submit"><?=t('Add Menu')?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {

        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '#ccm-dashboard-content',
                components: config.components,
                data: {
                },
                methods: {
                    showAddMenu(e) {
                        e.preventDefault()
                        const addMenuModal = document.getElementById('add-menu-modal');
                        const modal = bootstrap.Modal.getOrCreateInstance(addMenuModal);
                        if (modal) {
                            modal.show();
                        }

                    }
                }
            })
        })

    });
</script>

