<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'instances']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h3><?=t('Instances')?></h3>
        
        <?php if ($instances) { ?>
            
            <table class="table table-striped">
            <thead>
                <tr>
                    <th class="w-100" colspan="2"><?=t('Date Created')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($instances as $instance) {
                    ?>
                    <tr>
                        <td class="w-100"><?=$instance->getDateCreatedObject()->format('Y-m-d H:i:s')?></td>
                        <td>
                            <div class="text-nowrap">
                                <a href="#" class="launch-tooltip icon-link mr-1" data-toggle="modal" data-target="#update-instance-<?=$instance->getBoardInstanceID()?>"><i class="fas fa-sync-alt"></i></a>
                                <a class="icon-link mr-1" href="<?=$view->action('view_instance', $instance->getBoardInstanceID())?>" target="_blank"><i class="fas fa-search"></i></a>
                                <a href="#" data-toggle="modal" data-target="#delete-instance-<?=$instance->getBoardInstanceID()?>" class="icon-link"><i class="fas fa-trash"></i></a>
                            </div>
                            
                            <div class="modal fade" id="delete-instance-<?=$instance->getBoardInstanceID()?>" tabindex="-1">
                                <form method="post" action="<?=$view->action('delete_instance', $instance->getBoardInstanceID())?>">
                                    <?=$token->output('delete_instance')?>
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?=t('Delete Instance')?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <?=t('Are you sure you want to remove this board instance? If it is referenced on the front-end anywhere that block will be removed.')?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal"><?=t('Cancel')?></button>
                                                <button type="submit" class="btn btn-danger float-right"><?=t('Delete Instance')?></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="modal fade" id="update-instance-<?=$instance->getBoardInstanceID()?>" tabindex="-1">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><?=t('Update')?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            
                                            <form method="post" action="<?=$view->action('refresh_instance', $instance->getBoardInstanceID())?>">
                                                <?=$token->output('refresh_instance')?>

                                                <h5 class="font-weight-light"><?=t('Refresh')?></h5>
                                                <p><?=t('Refresh the dynamic elements within board slots without getting new items or changing any positioning.')?></p>
                                                <button type="submit" class="btn btn-block btn-secondary"><?=t("Refresh")?></button>
                                            </form>

                                            <hr>
                                            
                                            <form method="post" action="<?=$view->action('add_content', $instance->getBoardInstanceID())?>">
                                                <?=$token->output('add_content')?>

                                                <h5 class="font-weight-light"><?=t('Add Content')?></h5>
                                                <p><?=t('Refreshes dynamic elements within board slots, and adds new items to the board in applicable spots.')?></p>
                                                <button type="submit" class="btn btn-block btn-secondary"><?=t("Add Content")?></button>
                                            </form>

                                            <hr>
                                            
                                            <form method="post" action="<?=$view->action('regenerate_instance', $instance->getBoardInstanceID())?>">
                                                <?=$token->output('regenerate_instance')?>

                                                <h5 class="font-weight-light"><?=t('Regenerate')?></h5>
                                                <p><?=t('Regenerate board instance based on current items. Completely removes and rebuilds any board contents.')?></p>
                                                <button type="submit" class="btn btn-block btn-secondary"><?=t("Regenerate")?></button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
                
            </table>
        <?php } else { ?>
        
            <p><?=t('No board instances found.')?></p>
            
        <?php } ?>
        
        <hr>

        <form method="post" action="<?=$view->action('generate_instance', $board->getBoardID())?>">
            <?=$token->output('generate_instance')?>
            <h3><?=t("Generate New Instance")?></h3>

            <p><?=t("Create a completely new instance of this board, based on the current data sources, templates and rules. ")?></p>

            <button type="submit" class="btn btn-lg btn-primary"><?=t("Generate")?></button>
        </form>



    </div>
</div>
