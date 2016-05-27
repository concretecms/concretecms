<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<div class="ccm-dashboard-header-buttons">

    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>

</div>


<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">

        <form method="post" action="<?=$view->action('update', $entity->getID())?>">
            <?=$token->output('update_entity')?>

            <fieldset>
                <legend><?=t("Basics")?></legend>
                <div class="form-group">
                    <label for="name"><?=t('Name')?></label>
                    <?=$form->text('name', $entity->getName())?>
                </div>
                <div class="form-group">
                    <label for="name"><?=t('Handle')?></label>
                    <?=$form->text('handle', $entity->getHandle())?>
                </div>
                <div class="form-group">
                    <label for="name"><?=t('Description')?></label>
                    <?=$form->textarea('description', $entity->getDescription(), array('rows' => 5))?>
                </div>
            </fieldset>
            <fieldset>
                <legend><?=t('Views')?></legend>
                <div class="form-group">
                    <label for="name"><?=t('Default Edit Form')?></label>
                    <?=$form->select('default_edit_form_id', $forms, $defaultEditFormID)?>
                </div>
                <div class="form-group">
                    <label for="name"><?=t('Default View Form')?></label>
                    <?=$form->select('default_view_form_id', $forms, $defaultViewFormID)?>
                </div>

            </fieldset>
            <fieldset>
                <legend><?=t('Results Folder')?></legend>
                <input type="hidden" name="entity_results_node_id" value="<?=$folder->getTreeNodeID()?>">

                <div data-tree="<?=$tree->getTreeID()?>">
                </div>

                <script type="text/javascript">
                    $(function() {

                        $('[data-tree]').concreteTree({
                            treeID: '<?=$tree->getTreeID()?>',
                            ajaxData: {
                                displayOnly: 'category'
                            },
                            <?php if (is_object($folder)) { ?>
                                selectNodesByKey: [<?=$folder->getTreeNodeID()?>],
                                onSelect : function(nodes) {
                                    if (nodes.length) {
                                        $('input[name=entity_results_node_id]').val(nodes[0]);
                                    } else {
                                        $('input[name=entity_results_node_id]').val('');
                                    }
                                },
                            <?php } ?>
                            'chooseNodeInForm': 'single'
                        });

                    });
                </script>

            </fieldset>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
                </div>
            </div>
        </form>

    </div>
</div>
