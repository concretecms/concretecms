<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

$searchFields = array(
    'size' => t('Size'),
    'type' => t('Type'),
    'extension' => t('Extension'),
    'date_added' => t('Added Between'),
    'added_to' => t('Added to Page')
);

$searchFieldAttributes = FileAttributeKey::getSearchableList();
foreach ($searchFieldAttributes as $ak) {
    $searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
}

$flr = new \Concrete\Core\Search\StickyRequest('files');
$req = $flr->getSearchRequest();

?>

<script type="text/template" data-template="search-form">
<form role="form" data-search-form="files" action="<?=URL::to('/ccm/system/search/files/submit')?>" class="form-inline ccm-search-fields">
    <div class="ccm-search-fields-row">
        <div class="form-group">
            <select data-bulk-action="files" disabled class="ccm-search-bulk-action form-control">
                <option value=""><?=t('Items Selected')?></option>
                <option value="download"><?=t('Download')?></option>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Edit Properties')?>" data-bulk-action-url="<?=URL::to('/ccm/system/dialogs/file/bulk/properties')?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?=t('Edit Properties')?></option>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Sets')?>" data-bulk-action-url="<?=Loader::helper('concrete/urls')->getToolsURL('files/add_to')?>" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Sets')?></option>
                <option data-bulk-action-type="ajax" data-bulk-action-url="<?=URL::to('/ccm/system/file/rescan')?>"><?=t('Rescan')?></option>
                <? /*
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Duplicate')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/duplicate" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Copy')?></option>
 */ ?>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Delete')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Delete')?></option>
            </select>
        </div>
        <div class="form-group">
            <div class="ccm-search-main-lookup-field">
                <i class="fa fa-search"></i>
                <?=$form->search('fKeywords', $req['fKeywords'], array('placeholder' => t('Keywords')))?>
                <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
            </div>
        </div>
        <ul class="ccm-search-form-advanced list-inline">
            <li><a href="#" data-search-toggle="advanced"><?=t('Advanced Search')?></a>
            <li><a href="#" data-search-toggle="customize" data-search-column-customize-url="<?=URL::to('/ccm/system/dialogs/file/search/customize')?>"><?=t('Customize Results')?></a>
            <?php
            $fp = FilePermissions::getGlobal();
            if ($fp->canAddFile()) { ?>
                <li id="ccm-file-manager-upload"><a href="#"><?=t('Upload Files')?><input type="file" name="files[]" multiple="multiple" /></a></li>
            <?php } ?>

        </ul>
    </div>
    <?php
    $s1 = FileSet::getMySets();
    if (count($s1) > 0) { ?>
    <div class="ccm-search-fields-row">
        <div class="form-group form-group-full">
        <?=$form->label('fsID', t('File Set'))?>
        <div class="ccm-search-field-content">
        <select multiple name="fsID[]" class="select2-select" style="width: 100%">
            <optgroup label="<?=t('Sets')?>">
            <?php foreach ($s1 as $s) { ?>
                <option value="<?=$s->getFileSetID()?>"  <?php if (is_array($req['fsID']) && in_array($s->getFileSetID(), $req['fsID'])) { ?> selected="selected" <?php } ?>><?=wordwrap($s->getFileSetName(), '23', '&shy;', true)?></option>
            <?php } ?>
            </optgroup>
            <optgroup label="<?=t('Other')?>">
                <option value="-1" <?php if (is_array($req['fsID']) && in_array(-1, $req['fsID'])) { ?> selected="selected" <?php } ?>><?=t('Files in no sets.')?></option>
            </optgroup>
        </select>
        </div>
        </div>
    </div>
    <?php } ?>
    <div class="ccm-search-fields-advanced"></div>
</form>
</script>

<script type="text/template" data-template="search-field-row">
<div class="ccm-search-fields-row">
    <select name="field[]" class="ccm-search-choose-field form-control" data-search-field="files">
        <option value=""><?=t('Choose Field')?></option>
        <?php foreach ($searchFields as $key => $value) { ?>
            <option value="<?=$key?>" <% if (typeof(field) != 'undefined' && field.field == '<?=$key?>') { %>selected<% } %> data-search-field-url="<?=URL::to('/ccm/system/search/files/field', $key)?>"><?=$value?></option>
        <?php } ?>
    </select>
    <div class="ccm-search-field-content"><% if (typeof(field) != 'undefined') { %><%=field.html%><% } %></div>
    <a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="fa fa-minus-circle"></i></a>
</div>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (file) {%>
<tr data-launch-search-menu="<%=file.fID%>" data-file-manager-file="<%=file.fID%>">
    <td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="individual" value="<%=file.fID%>" /></span></td>
    <td class="ccm-file-manager-search-results-star <% if (file.isStarred) { %>ccm-file-manager-search-results-star-active<% } %>"><a href="#" data-search-toggle="star" data-search-toggle-url="<?=URL::to('/ccm/system/file/star')?>" data-search-toggle-file-id="<%=file.fID%>"><i class="fa fa-star"></i></a></td>
    <td class="ccm-file-manager-search-results-thumbnail"><%=file.resultsThumbnailImg%></td>
    <% for (i = 0; i < file.columns.length; i++) {
        var column = file.columns[i]; %>
        <td><%=column.value%></td>
    <% } %>
</tr>
<% }); %>
</script>

<div data-search-element="wrapper"></div>

<div data-search-element="results">
    <div class="table-responsive">
        <table class="ccm-search-results-table">
        <thead>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div>
    <div class="ccm-search-results-pagination"></div>
</div>

<script type="text/template" data-template="search-results-pagination">
<%=paginationTemplate%>
</script>

<script type="text/template" data-template="search-results-table-head">
<tr>
    <th><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="select-all" /></span></th>
    <th class="ccm-file-manager-search-results-star"><span><i class="fa fa-star"></i></span></th>
    <th><span><?=t('Thumbnail')?></th>
    <%
    for (i = 0; i < columns.length; i++) {
        var column = columns[i];
        if (column.isColumnSortable) { %>
            <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%=column.title%></a></th>
        <% } else { %>
            <th><span><%=column.title%></span></th>
        <% } %>
    <% } %>
</tr>
</script>
