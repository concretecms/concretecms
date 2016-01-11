<?php   
defined('C5_EXECUTE') or die("Access Denied.");  
?> 

<fieldset id="ccm_edit_pane_nextPreviousWrap">
    <div class="form-group row">
        <div class="col-xs-6">
            <label class="control-label"><?php  echo t('Next Label')?></label>
            <input name="nextLabel" class="form-control" type="text" value="<?php echo htmlentities($controller->nextLabel, ENT_QUOTES, APP_CHARSET) ?>" placeholder="<?php echo t('leave blank to hide'); ?>" />
        </div>
      </div>
      <div class="form-group row">
        <div class="col-xs-6">
            <label class="control-label"><?php  echo t('Previous Label')?></label>
            <input name="previousLabel" class="form-control" type="text" value="<?php echo htmlentities($controller->previousLabel, ENT_QUOTES, APP_CHARSET) ?>" placeholder="<?php echo t('leave blank to hide'); ?>" />
        </div>
      </div>
      <div class="form-group row">
        <div class="col-xs-6">
            <label class="control-label"><?php  echo t('Up Label')?></label>
            <input name="parentLabel" class="form-control" type="text" value="<?php echo htmlentities($controller->parentLabel, ENT_QUOTES, APP_CHARSET) ?>" placeholder="<?php echo t('leave blank to hide'); ?>" />
        </div>
      </div>

 </fieldset>

  <fieldset>

  <div class="form-group">
      <div class="checkbox">
          <label><input name="loopSequence" type="checkbox" value="1" <?php echo intval($controller->loopSequence)?'checked="checked"':'' ?> /> <?php echo t('Loop Navigation') ?></label>
      </div>
  </div>


  <div class="form-group">
      <div class="checkbox">
        <label><input name="excludeSystemPages" type="checkbox" value="1" <?php echo intval($controller->excludeSystemPages)?'checked="checked"':'' ?> /> <?php echo t('Exclude system pages.') ?></label>
      </div>
  </div>

  </fieldset>

  <fieldset>
    <div class="form-group">
      <label class="control-label"><?php echo(t('Order Pages'))?></label>
      <select name="orderBy" class="form-control">
          <option value="display_asc" <?php echo ($controller->orderBy=='display_asc') ? 'selected="selected"' : '' ?>><?=t('Sitemap')?></option>
          <option value="chrono_desc" <?php echo ($controller->orderBy=='chrono_desc') ? 'selected="selected"' : '' ?>><?=t('Chronological')?></option>
          <option value="display_desc" <?php echo ($controller->orderBy=='display_desc') ? 'selected="selected"' : '' ?>><?=t('Reverse Sitemap')?></option>
          <option value="chrono_asc" <?php echo ($controller->orderBy=='chrono_asc') ? 'selected="selected"' : '' ?>><?=t('Reverse Chronological')?></option>
      </select>
    </div>

  </fieldset>

