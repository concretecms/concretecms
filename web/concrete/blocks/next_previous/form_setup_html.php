<?php   
defined('C5_EXECUTE') or die("Access Denied.");  
?> 

<div id="ccm-next-previous-pane" class="ccm-next-previous-pane">  
 
 	
    
	<div class="ccm-block-field-group">
	  <h2><?php  echo t('Next Label')?></h2>   
      <input name="linkStyle" type="radio" value="next_previous" <?php echo ($controller->linkStyle!='page_name')?'checked="checked"':'' ?>  /><?php echo t('Next & Previous Labels')?>  &nbsp;
      <input name="linkStyle" type="radio" value="page_name" <?php echo ($controller->linkStyle=='page_name')?'checked="checked"':'' ?>  /><?php echo t('Page Titles') ?>
	</div>    
 
 	<div id="ccm_edit_pane_nextPreviousWrap" style="display:<?php echo ($controller->linkStyle!='page_name')?'block':'none' ?>" >
        <div class="ccm-block-field-group">
          <h2><?php  echo t('Next Label')?></h2>  
          <input name="nextLabel" type="text" value="<?php echo htmlentities($controller->nextLabel, ENT_QUOTES, APP_CHARSET) ?>" />
        </div>
        
        <div class="ccm-block-field-group">
          <h2><?php  echo t('Previous Label')?></h2>  
          <input name="previousLabel" type="text" value="<?php echo htmlentities($controller->previousLabel, ENT_QUOTES, APP_CHARSET) ?>" />
        </div>
        
        <div class="ccm-block-field-group">
          <h2><?php  echo t('Up Label')?></h2>  
          <input name="parentLabel" type="text" value="<?php echo htmlentities($controller->parentLabel, ENT_QUOTES, APP_CHARSET) ?>" />
        </div>
    </div>
    
	<div class="ccm-block-field-group">
	  <h2><?php  echo t('Show Arrows')?></h2>  
      <input name="showArrows" type="checkbox" value="1" <?php echo intval($controller->showArrows)?'checked="checked"':'' ?> /> &laquo; &raquo; 
	</div>      
    
	<div class="ccm-block-field-group">
	  <h2><?php  echo t('Loop')?></h2>  
      <input name="loopSequence" type="checkbox" value="1" <?php echo intval($controller->loopSequence)?'checked="checked"':'' ?> /> <?php echo t('Return to start/end of page sequence') ?> 
	</div> 
    
	<div class="ccm-block-field-group">
	  <h2><?php  echo t('Page Order')?></h2>  
      <select name="orderBy">
          <option value="display_asc" <?php echo ($controller->orderBy=='display_asc') ? 'selected="selected"' : '' ?>><?=t('Sitemap')?></option>
          <option value="chrono_desc" <?php echo ($controller->orderBy=='chrono_desc') ? 'selected="selected"' : '' ?>><?=t('Chronological')?></option>
      </select>
	</div> 
    	
</div> 
