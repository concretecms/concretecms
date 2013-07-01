<?php   
defined('C5_EXECUTE') or die("Access Denied.");  
?> 


 	
  <fieldset>
    <legend style="margin-bottom: 0px"><?=t('Titling')?></legend>

    
	<div class="control-group">
    <div class="controls">
      <label class="radio"><input name="linkStyle" type="radio" value="next_previous" <?php echo ($controller->linkStyle!='page_name')?'checked="checked"':'' ?>  /> <span><?php echo t('Next & Previous Labels')?></span></label>
      <label class="radio"><input name="linkStyle" class="radio" type="radio" value="page_name" <?php echo ($controller->linkStyle=='page_name')?'checked="checked"':'' ?>  /> <span><?php echo t('Page Titles') ?></span></label>
    </div>
	</div>    

  </fieldset>
  
  <fieldset id="ccm_edit_pane_nextPreviousWrap" style="display:<?php echo ($controller->linkStyle!='page_name')?'block':'none' ?>">
    <legend style="margin-bottom: 0px"><?=t("Labels")?></legend>

  <div class="control-group">
    <label class="control-label"><?php  echo t('Next Label')?></label>
    <div class="controls">
      <input name="nextLabel" type="text" value="<?php echo htmlentities($controller->nextLabel, ENT_QUOTES, APP_CHARSET) ?>" placeholder="<?php echo t('leave blank to hide'); ?>" />
    </div>
  </div>    

  <div class="control-group">
    <label class="control-label"><?php  echo t('Previous Label')?></label>
    <div class="controls">
      <input name="previousLabel" type="text" value="<?php echo htmlentities($controller->previousLabel, ENT_QUOTES, APP_CHARSET) ?>" placeholder="<?php echo t('leave blank to hide'); ?>" />
    </div>
  </div>    

  <div class="control-group">
    <label class="control-label"><?php  echo t('Up Label')?></label>
    <div class="controls">
          <input name="parentLabel" type="text" value="<?php echo htmlentities($controller->parentLabel, ENT_QUOTES, APP_CHARSET) ?>" placeholder="<?php echo t('leave blank to hide'); ?>" />
    </div>
  </div>    
 
 </fieldset>

  <fieldset>
    <legend style="margin-bottom: 0px"><?=t('Navigation')?></legend>

  <div class="control-group">
    <label class="control-label"><?php echo t('Arrows')?></label>
    <div class="controls">
      <label class="checkbox"><input name="showArrows" type="checkbox" value="1" <?php echo intval($controller->showArrows)?'checked="checked"':'' ?> /> <span><?=t('Include &laquo; and &raquo;.')?></span></label>
    </div>
  </div>   

  <div class="control-group">
    <label class="control-label"><?php echo t('Loop')?></label>
    <div class="controls">
      <label class="checkbox">
        <input name="loopSequence" type="checkbox" value="1" <?php echo intval($controller->loopSequence)?'checked="checked"':'' ?> /> <span><?php echo t('Return to start/end of page sequence.') ?></span>
      </label>
    </div>
  </div>     


  <div class="control-group">
    <label class="control-label"><?php echo t('System Pages')?></label>
    <div class="controls">
      <label class="checkbox">
        <input name="excludeSystemPages" type="checkbox" value="1" <?php echo intval($controller->excludeSystemPages)?'checked="checked"':'' ?> /> <span><?php echo t('Exclude system pages.') ?></span>
      </label>
    </div>
  </div>     

  </fieldset>

  <fieldset>
    <legend style="margin-bottom: 0px"><?=t('Ordering')?></legend>
    <div class="control-group">
      <label class="control-label"><?php echo(t('Order pages by'))?></label>
      <div class="controls">
      <select name="orderBy">
          <option value="display_asc" <?php echo ($controller->orderBy=='display_asc') ? 'selected="selected"' : '' ?>><?=t('Sitemap')?></option>
          <option value="chrono_desc" <?php echo ($controller->orderBy=='chrono_desc') ? 'selected="selected"' : '' ?>><?=t('Chronological')?></option>
      </select>
      </div>
    </div>    

  </fieldset>

