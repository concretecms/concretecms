
<ul id="ccm-autonav-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-autonav-tab-add" href="javascript:void(0);">Add</a></li>
	<li class=""><a id="ccm-autonav-tab-preview"  href="javascript:void(0);">Preview</a></li>
</ul>

<div style="padding: 10px">


<div class="ccm-autonavPane ccm-preview-pane" id="ccm-autonavPane-preview" style="display: none">

<center>Auto-Nav Preview</center>

</div>
<div class="ccm-autonavPane" id="ccm-autonavPane-add">



<input type="hidden" name="autonavCurrentCID" value="<?php echo $c->getCollectionID()?>" />
<input type="hidden" name="autonavPreviewPane" value="<?php echo REL_DIR_FILES_TOOLS_BLOCKS?>/<?php echo $bt->getBlockTypeHandle()?>/preview_pane.php" />

<strong>Nav elements should appear</strong><br>
<select name="orderBy" onchange="reloadPreview(this.form)">
	<option value="display_asc" selected>in their sitemap order</option>
	<option value="chrono_desc">with the most recent first</option>
    <option value="chrono_asc">with the earliest first.</option>
    <option value="alpha_asc">in alphabetical order.</option>
    <option value="alpha_desc">in reverse alphabetical order.</option>
    <?php  /* <option value="display_desc">Display Order (Desc)</option> */ ?>
</select>

<br><br>
<strong>Viewing Permissions</strong><br/>
<input type="checkbox" name="displayUnavailablePages" onClick="reloadPreview(document.blockForm)" value="1" style="vertical-align: middle" />
Display pages to users even when those users cannot access those pages.
<br/><br/>
<strong>Display Pages</strong><br>
<select name="displayPages" onchange="toggleCustomPage(this.value); reloadPreview(this.form);">
	<option value="top" selected>At the top level</option>
	<option value="second_level">At the second level</option>
	<option value="third_level">At the third level</option>
	<option value="above">At the level above</option>
	<option value="current">At the current level</option>
	<option value="below">At the level below</option>
	<!--<option value="custom">Beneath a particular page</option>//-->
</select>

<!--
<div id="divInclude" style="display: none">
<br><br>
	Select Page:<br>
	<div id="navigationItems" class="selectOne"></div>
	<input type="button" id="searchButton" name="search" value="search" onclick="ccmOpenWindow('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_collection.php',640,500); return false">
	<input type="hidden" name="displayPagesCID" id="cValueField" value="">
	<br><br>
	<input type="checkbox" name="displayPagesIncludeSelf" onclick="reloadPreview(this.form);" value="1" style="vertical-align: middle">
	Include selected page as top node in list.
</div>
<br>//--><br><br>

<strong>Sub-Pages</strong><br>
<select name="displaySubPages" onchange="toggleSubPageLevels(this.value); reloadPreview(this.form);">
	<option value="none" selected>No sub-pages</option>
	<option value="relevant">Relevant sub-pages</option>
	<option value="relevant_breadcrumb">Display Breadcrumb trail</option>
	<option value="all">Display all sub-pages</option>
</select>
<br><br>

<strong>Sub-Page Levels</strong><br>
<select id="displaySubPageLevels" name="displaySubPageLevels" disabled onchange="toggleSubPageLevelsNum(this.value); reloadPreview(this.form);">
	<option value="enough">Display sub-pages to current</option>
	<option value="enough_plus1">Display sub-pages to current +1</option>
	<option value="all">Display all</option>
	<option value="custom">Display custom amount</option>
</select>
<div id="divSubPageLevelsNum" style="display: none">
	<br>
	<input type="text" name="displaySubPageLevelsNum" onchange="reloadPreview(this.form)" value="1" style="width: 30px; vertical-align: middle">
	&nbsp;Levels to traverse.
</div>
</div>
</div>