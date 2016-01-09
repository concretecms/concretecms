<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_groups', 'edit', 'delete_set'))) { ?>

		<div class="row">
            <div class="col-md-8">

                <form method="post" action="<?php echo $view->action('update_set')?>">
                    <input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID()?>" />
                    <?php echo Loader::helper('validation/token')->output('update_set')?>

                    <fieldset>
                        <legend><?=t('Details')?></legend>
                        <div class="form-group">
                            <?php echo $form->label('gsName', t('Name'))?>
                            <?php echo $form->text('gsName', $set->getGroupSetName())?>
                        </div>
                        <div class="form-group">
                            <?php echo $form->submit('submit', t('Update Set'), array('class' => 'btn btn-primary'))?>
                        </div>
                    </fieldset>
                </form>

                <form method="post" action="<?php echo $view->action('update_set_groups')?>">
                    <?php echo Loader::helper('validation/token')->output('update_set_groups')?>
                    <input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID()?>" />

                    <fieldset>
                        <legend><?=t('Groups')?></legend>
                        <?php
                        $list = $set->getGroups();
                        if (count($groups) > 0) { ?>

                            <div class="control-group">
                                <?php foreach($groups as $g) { ?>
                                    <div class="checkbox">
                                        <label>
                                            <?php echo $form->checkbox('gID[]', $g->getGroupID(), $set->contains($g)) ?>
                                            <span><?php echo $g->getGroupDisplayName()?></span>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="control-group">
                                <?php echo $form->submit('submit', t('Update Groups'), array('class' => 'btn btn-primary'))?>
                            </div>
                        <?php } else { ?>
                            <div class="control-group">
                                <p><?php echo t('No groups found.')?></p>
                            </div>
                        <?php } ?>
                    </fieldset>
                </form>

            </div>

            <div class="col-md-4">

                <form method="post" action="<?php echo $view->action('delete_set')?>">
                    <?php echo Loader::helper('validation/token')->output('delete_set')?>
                    <input type="hidden" name="gsID" value="<?php echo $set->getGroupSetID()?>" />
                    <fieldset>
                        <legend><?=t('Delete Set')?></legend>
                        <div class="form-group">
                            <span class="help-block"><?php echo t('Warning, this cannot be undone. No groups will be deleted but they will no longer be grouped together.')?></span>
                        </div>
                        <div class="form-group">
                            <?php echo $form->submit('submit', t('Delete Group Set'), array('class' => 'btn btn-danger'))?>
                        </div>
                    </fieldset>
                </form>

            </div>
		</div>

<? } else { ?>
    <div class="row">
        <div class="col-md-12">
        <? if (Config::get('concrete.permissions.model') == 'advanced') { ?>
            <div>
                <?php if (count($groupSets) > 0) { ?>

                    <ul class="item-select-list" id="ccm-group-list">
                        <?php foreach($groupSets as $gs) { ?>
                            <li>
                                <a href="<?php echo $view->url('/dashboard/users/group_sets', 'edit', $gs->getGroupSetID())?>">
                                    <i class="fa fa-users"></i> <?php echo $gs->getGroupSetDisplayName()?>
                                </a>
                            </li>
                        <? } ?>
                    </ul>

                <?php } else { ?>
                    <p><?php echo t('You have not added any group sets.')?></p>
                <?php } ?>
            </div>

            <form method="post" action="<?php echo $view->action('add_set')?>">
                <?php echo Loader::helper('validation/token')->output('add_set')?>
                <fieldset>
                    <legend><?=t('Add Set')?></legend>
                    <input type="hidden" name="categoryID" value="<?php echo $categoryID?>" />

                    <div class="control-group">
                        <?php echo $form->label('gsName', t('Name'))?>
                        <div class="controls">
                            <?php echo $form->text('gsName')?>
                        </div>
                    </div>

                    <div class="control-group" style="margin-top: 10px;">
                        <label><?=t('Groups')?></label>
                        <? foreach($groups as $g) { ?>
                            <div class="checkbox">
                                <label><?=$form->checkbox('gID[]', $g->getGroupID())?> <span><?=$g->getGroupDisplayName()?></span></label>
                            </div>
                        <? } ?>
                    </div>

                    <div class="control-group">
                        <?php echo $form->submit('submit', t('Add Set'), array('class' => 'btn btn-primary'))?>
                    </div>
                </fieldset>
            </form>
        <? } else { ?>
            <p><?=t('You must enable <a href="%s">advanced permissions</a> to use group sets.', $view->url('/dashboard/system/permissions/advanced'))?></p>
        <? } ?>
            </div>
        </div>
<? } ?>
