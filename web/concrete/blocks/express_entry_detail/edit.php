<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
?>

<div id="ccm-block-express-entry-detail-edit">

    <div class="form-group">
        <?=$form->label('exEntityID', t('Entity'))?>
        <?=$form->select('exEntityID', $entities, $exEntityID, [
            'data-action' => $view->action('load_entity_data')
        ]);?>
    </div>

    <div class="form-group">
        <?=$form->label('entryMode', t('Entry'))?>
        <?=$form->select('entryMode', [
            'E' => t('Get entry from list block on another page'),
            'S' => t('Display specific entry')
            ], $entryMode);
        ?>
    </div>

    <div class="form-group" data-container="express-entry-specific-entry">
        <?php if (is_object($entity)) {
            $form_selector = \Core::make('form/express/entry_selector');
            print $form_selector->selectEntry($entity, 'exSpecificEntryID', $entry);
        } else { ?>
            <?=t('You must select an entity before you can choose a specific entry from it.')?>
        <?php } ?>
    </div>


    <div class="form-group">
        <?=$form->label('exFormID', t('Display Data in Entity Form'))?>
        <div data-container="express-entry-detail-form">
            <?php if (is_object($entity)) { ?>
                <select name="exFormID" class="form-control">
                    <?php foreach($entity->getForms() as $form) { ?>
                        <option <?php if ($exFormID == $form->getID()) { ?>selected="selected" <?php } ?> value="<?=$form->getID()?>"><?=$form->getName()?></option>
                    <?php } ?>
                </select>
            <?php } else { ?>
                <?=t('You must select an entity before you can choose its display form.')?>
            <?php } ?>
        </div>

    </div>


</div>

<script type="text/template" data-template="express-attribute-form-list">
    <select name="exFormID" class="form-control">
    <% _.each(forms, function(form) { %>
        <option value="<%=form.exFormID%>"><%=form.exFormName%></option>
    <% }); %>
    </select>
</script>



<script type="application/javascript">
    Concrete.event.publish('block.express_entry_detail.open', {
    });
</script>