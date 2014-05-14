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

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Logs'), false, false, false);?>
    
    <? if(!$areEntries) { ?>
    
    <div class="ccm-pane-body ccm-pane-body-footer">
    
    	<p><?=t('There are no log entries to show at the moment.')?></p>
    
    </div>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
    
    <? } else { ?>
    
    <div class="ccm-pane-options ccm-pane-options-permanent-search">
    	<form class="form-inline" method="get" id="ccm-log-search"  action="<?=$controller->action('view')?>">
		<div class="control-inline">
			<label for="keywords"><?=t('Keywords')?></label>
			<?=$form->text('keywords', $keywords, array('style'=>'width:180px;'))?>
		</div>
		<div class="control-inline">
			<label for="level"><?=t('Level')?></label>
			<?=$form->select('level', $levels, array('style'=>'width:180px;'))?>
		</div>
        <div class="control-inline">
            <label for="channel"><?=t('Channel')?></label>
            <?=$form->select('channel', $channels, array('style'=>'width:180px;'))?>
            <?=$form->submit('search',t('Search') )?>
        </div>
        </form>
    </div>
        
	<div class="ccm-pane-body <? if(!$paginator || !strlen($paginator->getPages())>0) { ?>ccm-pane-body-footer <? } ?>">

        <table class="table table-bordered">
        	<thead>
                <tr>
                    <th class="subheaderActive"><?=t('Date/Time')?></th>
                    <th class="subheader"><?=t('Level')?></th>
                    <th class="subheader"><?=t('Channel')?></th>
                    <th class="subheader"><?=t('User')?></th>
                    <th class="subheader"><input style="float: right" class="btn error btn-mini" type="button" onclick="if (confirm('<?=t("Are you sure you want to clear this log?")?>')) { location.href='<?=$view->url('/dashboard/reports/logs', 'clear', $valt->generate(), $_REQUEST['channel'])?>'}" value="<?=t('Clear Log')?>" /><?=t('Text')?></th>
                </tr>
			</thead>
            <tbody>
				<? foreach($entries as $ent) { ?>
                <tr>
                    <td valign="top" style="white-space: nowrap" class="active"><?php
                        print $ent->getTimestamp();
                    ?></td>
                    <td valign="top"><strong><?=$ent->getLevel()?></strong></td>
                    <td valign="top"><strong><?=$ent->getChannel()?></strong></td>
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
    
	<? if($paginator && strlen($paginator->getPages())>0){ ?>
    <div class="ccm-pane-footer">
        
        	<div class="pagination">
              <ul>
                  <li class="prev"><?=$paginator->getPrevious()?></li>
                  
                  <? // Call to pagination helper's 'getPages' method with new $wrapper var ?>
                  <?=$paginator->getPages('li')?>
                  
                  <li class="next"><?=$paginator->getNext()?></li>
              </ul>
			</div>


	</div>
        <? } // PAGINATOR ?>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
    
    <? } ?>
