<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>
<ul id="ccm-autonav-tabs" class="nav nav-tabs">
	<li class="active"><a id="ccm-autonav-tab-add" href="javascript:void(0);"><?=t('Edit')?></a></li>
	<li class=""><a id="ccm-autonav-tab-preview"  href="javascript:void(0);"><?=t('Preview')?></a></li>
</ul>

<div style="padding: 10px">


<div style="clear: both" class="ccm-autonavPane ccm-preview-pane" id="ccm-autonavPane-preview" style="display: none">


</div>
<div class="ccm-autonavPane" id="ccm-autonavPane-add">



<input type="hidden" name="autonavCurrentCID" value="<?=$c->getCollectionID()?>" />
<input type="hidden" name="autonavPreviewPane" value="<?=REL_DIR_FILES_TOOLS_BLOCKS?>/<?=$this->getBlockTypeHandle()?>/preview_pane.php" />

<strong><?=t('Pages Should Appear')?></strong><br>
<select name="orderBy">
	<option value="display_asc" <? if ($info['orderBy'] == 'display_asc') { ?> selected<? } ?>><?=t('in their sitemap order.')?></option>
	<option value="chrono_desc" <? if ($info['orderBy'] == 'chrono_desc') { ?> selected<? } ?>><?=t('with the most recent first.')?></option>
    <option value="chrono_asc" <? if ($info['orderBy'] == 'chrono_asc') { ?> selected<? } ?>><?=t('with the earliest first.')?></option>
    <option value="alpha_asc" <? if ($info['orderBy'] == 'alpha_asc') { ?> selected<? } ?>><?=t('in alphabetical order.')?></option>
    <option value="alpha_desc" <? if ($info['orderBy'] == 'alpha_desc') { ?> selected<? } ?>><?=t('in reverse alphabetical order.')?></option>
    <option value="display_desc" <? if ($info['orderBy'] == 'display_desc') { ?> selected<? } ?>><?=t('in reverse sitemap order.')?></option>
</select>
<br><br>
<strong><?=t('Viewing Permissions')?></strong><br/>
<label class="checkbox">
<?=$form->checkbox('displayUnavailablePages', 1, $info['displayUnavailablePages']); ?>
<?=t('Display pages to users even when those users cannot access those pages.')?>
</label>
<br/><br/>
<strong><?=t('Display Pages')?></strong><br>
<select name="displayPages" onchange="toggleCustomPage(this.value);">
	<option value="top"<? if ($info['displayPages'] == 'top') { ?> selected<? } ?>><?=t('at the top level.')?></option>
	<option value="second_level"<? if ($info['displayPages'] == 'second_level') { ?> selected<? } ?>><?=t('at the second level.')?></option>
	<option value="third_level"<? if ($info['displayPages'] == 'third_level') { ?> selected<? } ?>><?=t('at the third level.')?></option>
	<option value="above"<? if ($info['displayPages'] == 'above') { ?> selected<? } ?>><?=t('at the level above.')?></option>
	<option value="current"<? if ($info['displayPages'] == 'current') { ?> selected<? } ?>><?=t('at the current level.')?></option>
	<option value="below"<? if ($info['displayPages'] == 'below') { ?> selected<? } ?>><?=t('At the level below.')?></option>
	<option value="custom"<? if ($info['displayPages'] == 'custom') { ?> selected<? } ?>><?=t('Beneath a particular page')?></option>
</select>

<div id="ccm-autonav-page-selector"<? if ($info['displayPages'] != 'custom') { ?> style="display: none"<? } ?>>
<? $form = Loader::helper('form/page_selector');
print $form->selectPage('displayPagesCID', $info['displayPagesCID']);
?>
</div>

<br><br>

<strong><?=t('Sub Pages to Display')?></strong><br>
<select name="displaySubPages" onchange="toggleSubPageLevels(this.value);">
	<option value="none"<? if ($info['displaySubPages'] == 'none') { ?> selected<? } ?>><?=t('None')?></option>
	<option value="relevant"<? if ($info['displaySubPages'] == 'relevant') { ?> selected<? } ?>><?=t('Relevant sub pages.')?></option>
	<option value="relevant_breadcrumb"<? if ($info['displaySubPages'] == 'relevant_breadcrumb') { ?> selected<? } ?>><?=t('Display breadcrumb trail.')?></option>
	<option value="all"<? if ($info['displaySubPages'] == 'all') { ?> selected<? } ?>><?=t('Display all.')?></option>
</select>
<br><br>

<strong><?=t('Sub-Page Levels')?></strong><br>
<select id="displaySubPageLevels" name="displaySubPageLevels" <? if ($info['displaySubPages'] == 'none') { ?> disabled <? } ?> onchange="toggleSubPageLevelsNum(this.value);">
	<option value="enough"<? if ($info['displaySubPageLevels'] == 'enough') { ?> selected<? } ?>><?=t('Display sub pages to current.')?></option>
	<option value="enough_plus1"<? if ($info['displaySubPageLevels'] == 'enough_plus1') { ?> selected<? } ?>><?=t('Display sub pages to current +1.')?></option>
	<option value="all"<? if ($info['displaySubPageLevels'] == 'all') { ?> selected<? } ?>><?=t('Display all.')?></option>
	<option value="custom"<? if ($info['displaySubPageLevels'] == 'custom') { ?> selected<? } ?>><?=t('Display a custom amount.')?></option>
</select>
<div id="divSubPageLevelsNum"<? if ($info['displaySubPageLevels'] != 'custom') { ?> style="display: none"<? } ?>>
	<br>
	<input type="text" name="displaySubPageLevelsNum" value="<?=$info['displaySubPageLevelsNum']?>" style="width: 30px; vertical-align: middle">
	&nbsp;<?=t('levels')?>
</div>
</div>
</div>