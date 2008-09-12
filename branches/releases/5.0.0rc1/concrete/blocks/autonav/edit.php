<?php 
$info = $controller->getContent();
?>

<ul id="ccm-autonav-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-autonav-tab-add" href="javascript:void(0);">Edit</a></li>
	<li class=""><a id="ccm-autonav-tab-preview"  href="javascript:void(0);">Preview</a></li>
</ul>

<div style="padding: 10px">


<div class="ccm-autonavPane ccm-preview-pane" id="ccm-autonavPane-preview" style="display: none">

<center>Auto-Nav Preview</center>

</div>
<div class="ccm-autonavPane" id="ccm-autonavPane-add">



<input type="hidden" name="autonavCurrentCID" value="<?php echo $c->getCollectionID()?>" />
<input type="hidden" name="autonavPreviewPane" value="<?php echo REL_DIR_FILES_TOOLS_BLOCKS?>/<?php echo $b->getBlockTypeHandle()?>/preview_pane.php" />

<strong>Nav elements should appear</strong><br>
<select name="orderBy" onchange="reloadPreview(this.form)">
	<option value="display_asc" <?php  if ($info['orderBy'] == 'display_asc') { ?> selected<?php  } ?>>in their sitemap order</option>
	<option value="chrono_desc" <?php  if ($info['orderBy'] == 'chrono_desc') { ?> selected<?php  } ?>>with the most recent first</option>
    <option value="chrono_asc" <?php  if ($info['orderBy'] == 'chrono_asc') { ?> selected<?php  } ?>>with the earliest first.</option>
    <option value="alpha_asc" <?php  if ($info['orderBy'] == 'alpha_asc') { ?> selected<?php  } ?>>in alphabetical order.</option>
    <option value="alpha_desc" <?php  if ($info['orderBy'] == 'alpha_desc') { ?> selected<?php  } ?>>in reverse alphabetical order.</option>
    <?php  /* <option value="display_desc">Display Order (Desc)</option> */ ?>
</select>
<br><br>
<strong>Viewing Permissions</strong><br/>
<input type="checkbox" name="displayUnavailablePages" onclick="reloadPreview(this.form)" value="1" <?php  if ($info['displayUnavailablePages'] == 1) { ?> checked <?php  } ?> style="vertical-align: middle" />
Display pages to users even when those users cannot access those pages.
<br/><br/>
<strong>Display Pages</strong><br>
<select name="displayPages" onchange="toggleCustomPage(this.value); reloadPreview(this.form);">
	<option value="top"<?php  if ($info['displayPages'] == 'top') { ?> selected<?php  } ?>>At the top level</option>
	<option value="second_level"<?php  if ($info['displayPages'] == 'second_level') { ?> selected<?php  } ?>>At the second level</option>
	<option value="third_level"<?php  if ($info['displayPages'] == 'third_level') { ?> selected<?php  } ?>>At the third level</option>
	<option value="above"<?php  if ($info['displayPages'] == 'above') { ?> selected<?php  } ?>>At the level above</option>
	<option value="current"<?php  if ($info['displayPages'] == 'current') { ?> selected<?php  } ?>>At the current level</option>
	<option value="below"<?php  if ($info['displayPages'] == 'below') { ?> selected<?php  } ?>>At the level below</option>
	<!--<option value="custom"<?php  if ($info['displayPages'] == 'custom') { ?> selected<?php  } ?>>Beneath a particular page</option>//-->
</select>

<!--
<div id="divInclude"<?php  if ($info['displayPages'] != 'custom') { ?> style="display: none"<?php  } ?>>
<br><br>
	Select Page:<br>
	<?php  if ($info['displayPagesCID']) {
		$dpc = Collection::getByID($info['displayPagesCID'], 'ACTIVE');
		$niTitle = $dpc->getCollectionName();
	} ?>
	<div id="navigationItems" class="selectOne"><?php echo $niTitle?></div>
	<input type="button" id="searchButton" name="search" value="search" onclick="ccmOpenWindow('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_collection.php',640,500); return false">
	<input type="hidden" name="displayPagesCID" id="cValueField" value="<?php echo $info['displayPagesCID']?>">
	<br><br>
	<input type="checkbox" name="displayPagesIncludeSelf" onclick="reloadPreview(this.form);" value="1"<?php  if ($info['displayPagesIncludeSelf']) { ?> checked<?php  } ?> style="vertical-align: middle">
	Include selected page as top node in list.
</div>
//-->
<br><br>

<strong>Sub-Pages</strong><br>
<select name="displaySubPages" onchange="toggleSubPageLevels(this.value); reloadPreview(this.form);">
	<option value="none"<?php  if ($info['displaySubPages'] == 'none') { ?> selected<?php  } ?>>No sub-pages</option>
	<option value="relevant"<?php  if ($info['displaySubPages'] == 'relevant') { ?> selected<?php  } ?>>Relevant sub-pages</option>
	<option value="relevant_breadcrumb"<?php  if ($info['displaySubPages'] == 'relevant_breadcrumb') { ?> selected<?php  } ?>>Display Breadcrumb trail</option>
	<option value="all"<?php  if ($info['displaySubPages'] == 'all') { ?> selected<?php  } ?>>Display all sub-pages</option>
</select>
<br><br>

<strong>Sub-Page Levels</strong><br>
<select id="displaySubPageLevels" name="displaySubPageLevels" <?php  if ($info['displaySubPages'] == 'none') { ?> disabled <?php  } ?> onchange="toggleSubPageLevelsNum(this.value); reloadPreview(this.form);">
	<option value="enough"<?php  if ($info['displaySubPageLevels'] == 'enough') { ?> selected<?php  } ?>>Display sub-pages to current</option>
	<option value="enough_plus1"<?php  if ($info['displaySubPageLevels'] == 'enough_plus1') { ?> selected<?php  } ?>>Display sub-pages to current +1</option>
	<option value="all"<?php  if ($info['displaySubPageLevels'] == 'all') { ?> selected<?php  } ?>>Display all</option>
	<option value="custom"<?php  if ($info['displaySubPageLevels'] == 'custom') { ?> selected<?php  } ?>>Display custom amount</option>
</select>
<div id="divSubPageLevelsNum"<?php  if ($info['displaySubPageLevels'] != 'custom') { ?> style="display: none"<?php  } ?>>
	<br>
	<input type="text" name="displaySubPageLevelsNum" onchange="reloadPreview(this.form)" value="<?php echo $info['displaySubPageLevelsNum']?>" style="width: 30px; vertical-align: middle">
	&nbsp;Levels to traverse.
</div>
</div>
</div>