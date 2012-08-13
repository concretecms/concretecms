<? 
defined('C5_EXECUTE') or die("Access Denied.");

// HELPERS
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');


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
    	<form method="post" id="ccm-log-search"  action="<?=$pageBase?>">
        	<div class="row">
                <div class="span5">
                    <label for="keywords"><?=t('Keywords')?></label>
                    <div class="input">
                        <?=$form->text('keywords', $keywords, array('style'=>'width:180px;'))?>
                    </div>
                </div>
                <div class="span6">
                    <label for="logType"><?=t('Type')?></label>
                    <div class="input">
                        <?=$form->select('logType', $logTypes, array('style'=>'width:180px;'))?>
                    <?=$form->submit('search',t('Search') )?>
                    </div>
                </div>
            </div>
        </form>
    </div>
        
	<div class="ccm-pane-body <? if(!$paginator || !strlen($paginator->getPages())>0) { ?>ccm-pane-body-footer <? } ?>">

        <table class="table table-bordered">
        	<thead>
                <tr>
                    <th class="subheaderActive"><?=t('Date/Time')?></th>
                    <th class="subheader"><?=t('Type')?></th>
                    <th class="subheader"><?=t('User')?></th>
                    <th class="subheader"><input style="float: right" class="btn error btn-mini" type="button" onclick="if (confirm('<?=t("Are you sure you want to clear this log?")?>')) { location.href='<?=$this->url('/dashboard/reports/logs', 'clear', $valt->generate(), $_POST['logType'])?>'}" value="<?=t('Clear Log')?>" /></th>
					<?=t('Text')?></th>
                </tr>
			</thead>
            <tbody>
				<? foreach($entries as $ent) { ?>
                <tr>
                    <td valign="top" style="white-space: nowrap" class="active"><?=date(DATE_APP_GENERIC_TS, strtotime($ent->getTimestamp('user')))?><? if (date('m-d-y') != date('m-d-y', strtotime($ent->getTimestamp('user')))) { ?>
                        <?=t(' at ')?><?=date(DATE_APP_GENERIC_MDY, strtotime($ent->getTimestamp('user')))?>
                    <? } ?></td>
                    <td valign="top"><strong><?=$ent->getType()?></strong></td>
                    <td valign="top"><strong><?php
                    if($ent->getUserID() == NULL){
                        echo t("Guest");
                    }
                    else{
                        $u = User::getByUserID($ent->getUserID());
                        echo $u->getUserName();
                    }
                    ?></strong></td>
                    <td style="width: 100%"><?=$th->makenice($ent->getText())?></td>
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