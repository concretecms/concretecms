<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

$searchFields = $controller->getSearchFields();

$flr = new \Concrete\Core\Search\StickyRequest('files');
$req = $flr->getSearchRequest();

?>

<script type="text/template" data-template="search-form">
<form role="form" data-search-form="files" action="<?php echo URL::to('/ccm/system/search/files/submit')?>" class="ccm-search-fields">
    <div class="ccm-search-fields-row form-inline">
        <div class="form-group">
            <select data-bulk-action="files" disabled class="ccm-search-bulk-action form-control">
                <option value=""><?php echo t('Items Selected')?></option>
                <option value="choose"><?php echo t('Choose')?></option>
                <option value="download"><?php echo t('Download')?></option>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Edit Properties')?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/file/bulk/properties')?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Edit Properties')?></option>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Sets')?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/file/bulk/sets')?>" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?php echo t('Sets')?></option>
                <option data-bulk-action-type="ajax" data-bulk-action-url="<?php echo URL::to('/ccm/system/file/rescan')?>"><?php echo t('Rescan')?></option>
                <?php /*
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Duplicate')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/duplicate" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Copy')?></option>
 */ ?>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Delete')?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/file/bulk/delete')?>" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?php echo t('Delete')?></option>
            </select>
        </div>
        <div class="form-group">
            <div class="ccm-search-main-lookup-field">
                <i class="fa fa-search"></i>
                <?php echo $form->search('fKeywords', $req['fKeywords'], array('placeholder' => t('Keywords')))?>
                <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?php echo t('Search')?></button>
            </div>
        </div>
        <ul class="ccm-search-form-advanced list-inline">
            <li><a href="#" data-search-toggle="advanced"><?php echo t('Advanced Search')?></a>
            <li><a href="#" data-search-toggle="customize" data-search-column-customize-url="<?php echo URL::to('/ccm/system/dialogs/file/search/customize')?>"><?php echo t('Customize Results')?></a>
            <?php
            $fp = FilePermissions::getGlobal();
            if ($fp->canAddFile()) {
            $imageresize = Config::get('concrete.file_manager.restrict_uploaded_image_sizes');
            if ($imageresize) {
                $datastring = ' data-image-max-width="'. (int)Config::get('concrete.file_manager.restrict_max_width') . '" ';
                $datastring .=' data-image-max-height="'. (int) Config::get('concrete.file_manager.restrict_max_height'). '" ';
                $datastring .= ' data-image-quality="'. (int)Config::get('concrete.file_manager.restrict_resize_quality'). '" ';
            }  ?>
                <li class="ccm-file-manager-show-dialog ccm-file-manager-upload" <?=$datastring?>><a href="javascript:void"><?php echo t('Upload Files')?><input type="file" name="files[]" multiple="multiple" /></a></li>
                <li class="ccm-file-manager-show-dialog">        <a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/import"
                               class="dialog-launch"
                               dialog-width="500"
                               dialog-height="500"
                               dialog-modal="true"
                               dialog-title="<?=t('Add Files')?>"><?=t('More Options')?></a>


                </li>
            <?php } ?>

        </ul>
    </div>
    <?php
    $s1 = FileSet::getMySets();
    if (count($s1) > 0) { ?>
    <div class="ccm-search-fields-row">
        <div class="form-group form-group-full">
        <?php echo $form->label('fsID', t('File Set'))?>
			<div class="ccm-search-field-content ccm-search-field-content-select2">
        <select multiple name="fsID[]" class="select2-select" style="width: 360px">
            <optgroup label="<?php echo t('Sets')?>">
            <?php foreach ($s1 as $s) { ?>
                <option value="<?php echo $s->getFileSetID()?>"  <?php if (is_array($req['fsID']) && in_array($s->getFileSetID(), $req['fsID'])) { ?> selected="selected" <?php } ?>><?php echo $s->getFileSetDisplayName()?></option>
            <?php } ?>
            </optgroup>
            <optgroup label="<?php echo t('Other')?>">
                <option value="-1" <?php if (is_array($req['fsID']) && in_array(-1, $req['fsID'])) { ?> selected="selected" <?php } ?>><?php echo t('Files in no sets.')?></option>
            </optgroup>
        </select>
        </div>
        </div>
    </div>
    <?php } ?>
    <div class="ccm-search-fields-advanced"></div>
    <div class="ccm-search-fields-row ccm-search-fields-submit">
        <div class="form-group form-group-full">
            <label class="control-label"><?=t('Per Page')?></label>
            <div class="ccm-search-field-content ccm-search-field-content-select2">
                <?=$form->select('numResults', array(10 => t('10'), 20 => t('20'), 50 => t('50'), 100 => t('100'), 250 => t('250'), 500 => t('500'), 1000 => t('1000'))); ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary pull-right"><?=t('Search')?></button>
    </div>
</form>
</script>

<script type="text/template" data-template="search-field-row">
<div class="ccm-search-fields-row">
    <select name="field[]" class="ccm-search-choose-field form-control" data-search-field="files">
        <option value=""><?php echo t('Choose Field')?></option>
        <?php foreach ($searchFields as $key => $value) { ?>
            <option value="<?php echo $key?>" <% if (typeof(field) != 'undefined' && field.field == '<?php echo $key?>') { %>selected<% } %> data-search-field-url="<?php echo URL::to('/ccm/system/search/files/field', $key)?>"><?php echo $value?></option>
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
    <td class="ccm-file-manager-search-results-star <% if (file.isStarred) { %>ccm-file-manager-search-results-star-active<% } %>"><a href="#" data-search-toggle="star" data-search-toggle-url="<?php echo URL::to('/ccm/system/file/star')?>" data-search-toggle-file-id="<%=file.fID%>"><i class="fa fa-star"></i></a></td>
    <td class="ccm-file-manager-search-results-thumbnail"><%=file.resultsThumbnailImg%></td>
    <% for (i = 0; i < file.columns.length; i++) {
        var column = file.columns[i]; %>
        <td><%-column.value%></td>
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
    <th><span><?php echo t('Thumbnail')?></th>
    <%
    for (i = 0; i < columns.length; i++) {
        var column = columns[i];
        if (column.isColumnSortable) { %>
            <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
        <% } else { %>
            <th><span><%-column.title%></span></th>
        <% } %>
    <% } %>
</tr>
</script>
