<? 
defined('C5_EXECUTE') or die("Access Denied.");

// HELPERS
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
$dh = Loader::helper('date');


// VARIABLES

// Check if entries to show, assign to boolean var.
$areEntries = count($entries) > 0 ? true : false;

?>
    <? if(!$areEntries) { ?>
    
    <div class="ccm-pane-body ccm-pane-body-footer">
    
    	<p><?=t('No log entries found.')?></p>
    
    </div>
    

    <? } else { ?>

<div class="ccm-dashboard-content-full">

    <script type="text/javascript">
        $(function() {
            $('#level').chosen();
        });
    </script>

    <div data-search-element="wrapper">
        <form role="form" data-search-form="logs" action="<?=$controller->action('view')?>" class="form-inline ccm-search-fields">
        <div class="ccm-search-fields-row">
		<div class="form-group">
            <?=$form->label('level', t('Level'))?>
            <div class="ccm-search-field-content">
            <div class="ccm-search-main-lookup-field">
                <i class="glyphicon glyphicon-search"></i>
                <?=$form->search('keywords', array('placeholder' => t('Keywords')))?>
                <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
            </div>
            </div>
		</div>
        </div>
        <div class="ccm-search-fields-row">
        <div class="form-group">
            <?=$form->label('channel', t('Channel'))?>
            <div class="ccm-search-field-content">
            <?=$form->select('channel', $channels, array('style'=>'width:180px;'))?>
            </div>
        </div>
        </div>

        <div class="ccm-search-fields-row">
            <div class="form-group" style="width: 95%">
                <?=$form->label('level', t('Level'))?>
                <div class="ccm-search-field-content">
                <?=$form->selectMultiple('level', $levels, array_keys($levels))?>
                </div>
            </div>
        </div>

        </form>

    </div>

    <div data-search-element="results">

        <table class="ccm-search-results-table">
            <thead>
                <tr>
                    <th><a href=""><?=t('Date/Time')?></a></th>
                    <th><a href=""><?=t('Level')?></a></th>
                    <th><span><?=t('Channel')?></span></th>
                    <th><span><?=t('User')?></span></th>
                    <th><span><?=t('Message')?></span></th>
                </tr>
            </thead>
            <tbody>
                <? foreach($entries as $ent) { ?>
                <tr>
                    <td valign="top" style="white-space: nowrap" class="active"><?php
                        print $ent->getTimestamp();
                    ?></td>
                    <td valign="top" style="text-align: center"><?=$ent->getLevelIcon()?></td>
                    <td valign="top" style="white-space: nowrap"><?=$ent->getChannelDisplay()?></td>
                    <td valign="top"><strong><?php
                    if($ent->getUserID() == NULL){
                        echo t("Guest");
                    }
                    else{
                        $u = User::getByUserID($ent->getUserID());
                        echo $u->getUserName();
                    }
                    ?></strong></td>
                    <td style="width: 100%"><?=$th->makenice($ent->getMessage())?></td>
                </tr>
                <? } ?>
            </tbody>
        </table>

    </div>

    <!-- END Body Pane -->
    <?=$list->displayPagingV2()?>

</div>


<? } ?>
