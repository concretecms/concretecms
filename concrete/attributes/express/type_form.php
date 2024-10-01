<?php if(is_array($entities) && count($entities)) { ?>
    <fieldset class="ccm-attribute ccm-attribute-date-time">
    <legend><?=t('Express Options')?></legend>

    <div class="form-group">
    <?=$form->label('exEntityID', t('Entity'))?>
    <?=$form->select('exEntityID', $entities, $entityID)?>
    </div>

    </fieldset>
<?php 
} else { 
    ?>

    <div class="alert alert-danger"><?=t('You have not created any Express entities.
You must create an entity on the <a href="%s">Express Page</a>
before you can use this attribute type.', URL::to('/dashboard/express/entries'))?></div>

<?php 
} ?>