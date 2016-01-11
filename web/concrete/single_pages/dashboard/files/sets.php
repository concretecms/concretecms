<?php defined('C5_EXECUTE') or die("Access Denied.");

$ih = Core::make('helper/concrete/ui');
$dh = Core::make('helper/date');

?>
<?php if ($this->controller->getTask() == 'view_detail') {
    ?>

	<script type="text/javascript">
		deleteFileSet = function() {
			if (confirm('<?=t('Are you sure you want to permanently remove this file set?')?>')) { 
				location.href = "<?=$view->url('/dashboard/files/sets', 'delete', $fs->getFileSetID(), Core::make('helper/validation/token')->generate('delete_file_set'))?>";
			}
		}
	</script>

	<?php
    $fsp = new Permissions($fs);
    if ($fsp->canDeleteFileSet()) {
        ?>
	<div class="ccm-dashboard-header-buttons">
		<button class="btn btn-danger" onclick="deleteFileSet()"><?=t('Delete Set')?></button>
	</div>
	<?php 
    }
    ?>

	<form method="post" class="form-horizontal" id="file_sets_edit" action="<?=$view->url('/dashboard/files/sets', 'file_sets_edit')?>">
		<?=$validation_token->output('file_sets_edit');
    ?>

		<?= $ih->tabs(array(
            array('details', t('Details'), true),
            array('files', t('Files in Set')),
        ));
    ?>

		<div id="ccm-tab-content-details" class="ccm-tab-content">

			<div class="form-group">
                <?=$form->label('file_set_name', t('Name'))?>
                <?=$form->text('file_set_name', $fs->fsName, array('class' => 'span5'));
    ?>
			</div>

            <?php if (Config::get('concrete.permissions.model') != 'simple' && $fsp->canEditFileSetPermissions()) {
    ?>
			
                <div class="form-group">
                    <div class="checkbox">
                        <label><?=$form->checkbox('fsOverrideGlobalPermissions', 1, $fs->overrideGlobalPermissions())?> <?=t('Enable custom permissions for this file set.')?></label>
                    </div>
                </div>

                <div id="ccm-permission-list-form" <?= !$fs->overrideGlobalPermissions() ? 'style="display: none"' : ''?> >
                    <?php Loader::element('permission/lists/file_set', array("fs" => $fs));
    ?>
                </div>
            <?php 
}
    ?>
			
			<?= $form->hidden('fsID', $fs->getFileSetID());
    ?>
			
		</div>

		<div class="ccm-tab-content" id="ccm-tab-content-files">
		<?php
        $fl = new FileList();
    $fl->filterBySet($fs);
    $fl->sortByFileSetDisplayOrder();
    $files = $fl->get();
    if (count($files) > 0) {
        ?>

            <span class="help-block"><?=t('Click and drag to reorder the files in this set. New files added to this set will automatically be appended to the end.')?></span>
            <div class="ccm-spacer">&nbsp;</div>

            <table class="ccm-search-results-table">
                <thead>
                    <tr>
                        <th></th>
                        <th><span><?=t('Thumbnail')?></span></th>
                        <th><a href="javascript:void(0)" class="sort-link" data-sort="type"    ><?=t('Type')?></a></th>
                        <th><a href="javascript:void(0)" class="sort-link" data-sort="title"   ><?=t('Title')?></a></th>
                        <th><a href="javascript:void(0)" class="sort-link" data-sort="filename"><?=t('File name')?></a></th>
                        <th><a href="javascript:void(0)" class="sort-link" data-sort="added"   ><?=t('Added')?></a></th>
                    </tr>
                </thead>

                <tbody class="ccm-file-set-file-list">

                    <?php foreach ($files as $f) {
    ?>
                        <tr id="fID_<?=$f->getFileID()?>" class="">
                            <td><i class="fa fa-arrows-v"></i></td>
                            <td class="ccm-file-manager-search-results-thumbnail"><?=$f->getListingThumbnailImage()?><input type="hidden" name="fsDisplayOrder[]" value="<?=$f->getFileID()?>" /></td>
                            <td data-key="type" ><?=$f->getGenericTypetext()?>/<?=$f->getType()?></td>
                            <td data-key="title"><?=$f->getTitle()?></td>
                            <td data-key="filename"><?=$f->getFileName()?></td>
                            <td data-key="added" data-sort="<?=$f->getDateAdded()->getTimestamp()?>" ><?=$dh->formatDateTime($f->getDateAdded()->getTimestamp())?></td>
                        </tr>
                    <?php 
}
        ?>
                </tbody>
            </table>
		<?php 
    } else {
        ?>
			<div class="alert alert-info"><?=t('There are no files in this set.')?></div>
		<?php 
    }
    ?>
		</div>
		<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<a href="<?=View::url('/dashboard/files/sets')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
			<?=Core::make("helper/form")->submit('save', t('Save'), array('class' => 'btn btn-primary pull-right'))?>
		</div>
		</div>
	</form>
	
	
	<script type="text/javascript">

	$(function() {
        var baseClass="ccm-results-list-active-sort-"; // asc desc

        function ccmFileSetResetSortIcons()
        {
            $(".ccm-search-results-table thead tr th").removeClass(baseClass + 'asc');
            $(".ccm-search-results-table thead tr th").removeClass(baseClass + 'desc');
            $(".ccm-search-results-table thead tr th a").css("color", "#93bfd5");
        }

        function ccmFileSetDoSort()
        {
            var $this = $(this);
            var $parent = $(this).parent();
            var asc = $parent.hasClass( baseClass + 'asc' );
            var key = $this.attr('data-sort');

            ccmFileSetResetSortIcons();
            var sortableList = $('.ccm-file-set-file-list');
            var listItems = $('tr', sortableList);

            if ( asc ) $parent.addClass( baseClass + 'desc' );
            else $parent.addClass( baseClass + 'asc' );

            listItems.sort( function( a, b ) {
                var aTD = $('td[data-key=' + key + ']', $(a) );
                var bTD = $('td[data-key=' + key + ']', $(b) );

                var aVal = typeof( aTD.attr('data-sort') ) == 'undefined' ? aTD.text().toUpperCase() : parseInt(aTD.attr('data-sort'));
                var bVal = typeof( bTD.attr('data-sort') ) == 'undefined' ? bTD.text().toUpperCase() : parseInt(bTD.attr('data-sort'));

                if (asc) {
                    return aVal < bVal ? -1 : 1;
                } else {
                    return bVal < aVal ? -1 : 1;
                }
            });
            sortableList.append(listItems);
        }

        $('.ccm-search-results-table thead th a.sort-link').click(ccmFileSetDoSort);

		$(".ccm-file-set-file-list").sortable({
			cursor: 'move',
            opacity: 0.5,
            axis: 'y',
            helper: function( evt, elem ) {
                var ret = $(elem).clone();
                var i;
                // copy the actual width of the elements

                ret.width( elem.outerWidth() );
                retChilds = $(ret.children());
                elemChilds = $(elem.children());
                
                for ( i = 0; i < elemChilds.length; i++ ) 
                    $(retChilds[i]).width( $(elemChilds[i]).outerWidth() );

                return ret; 
            },
            placeholder: "ccm-file-set-file-placeholder",
            stop: function(e,ui) {
                ccmFileSetResetSortIcons();
            }
		});


	});
	
	</script>
	
	<style type="text/css">
	    .ccm-file-set-file-list:hover {cursor: move}
        .ccm-file-set-file-placeholder { background-color: #ffd !important;  }
        .ccm-file-set-file-placeholder td { background:transparent !important; }
        .ccm-file-set-file-list td.ccm-file-manager-search-results-thumbnail img {max-height: 60px}
	</style>

<?php 
} else {
    ?>

<div class="ccm-dashboard-content-full">
    <div data-search-element="wrapper">
        <form role="form" id="ccm-file-set-search" method="get" action="<?=$view->url('/dashboard/files/sets')?>" class="form-inline ccm-search-fields">
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('keywords', t('Search'))?>
                    <div class="ccm-search-field-content">
                        <div class="ccm-search-main-lookup-field">
                            <i class="fa fa-search"></i>
				            <?=$form->search('fsKeywords', \Core::make('helper/text')->entities($_REQUEST['fsKeywords']), array('placeholder' => t('File Set Name')))?>
                            <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ccm-search-fields-row">
                <div class="form-group">
                    <?=$form->label('fsType', t('Type'))?>
                    <div class="ccm-search-field-content">
                        <select id="fsType" class="form-control" name="fsType" style="width: 200px; float: right">
                        <option value="<?=FileSet::TYPE_PUBLIC?>" <?php if ($fsType != FileSet::TYPE_PRIVATE) {
    ?> selected <?php 
}
    ?>><?=t('Public Sets')?></option>
                        <option value="<?=FileSet::TYPE_PRIVATE?>" <?php if ($fsType == FileSet::TYPE_PRIVATE) {
    ?> selected <?php 
}
    ?>><?=t('My Sets')?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="ccm-search-fields-submit">
                <button type="submit" class="btn btn-primary pull-right"><?=t('Search')?></button>
            </div>

        </form>

    </div>

  	<div class="ccm-dashboard-header-buttons">
		<a href="<?=View::url('/dashboard/files/add_set')?>" class="btn btn-default"><?=t('Add File Set')?></a>
	</div>
	<style type="text/css">
		form#ccm-file-set-search {
			margin-left: 0px !important;
		}
	</style>

    <section style="margin-right: 20px">
	<?php if (count($fileSets) > 0) {
    ?>
		

		<?php foreach ($fileSets as $fs) {
    ?>
		
			<div class="ccm-group">
				<a class="ccm-group-inner" href="<?=$view->url('/dashboard/files/sets/', 'view_detail', $fs->getFileSetID())?>"><i class="fa fa-cubes"></i> <?=$fs->getFileSetDisplayName()?></a>
			</div>
		
		<?php 
}
} else {
    ?>
	
		<p><?=t('No file sets found.')?></p>
	
	<?php 
}
    ?>


	<?php if ($fsl->requiresPaging()) {
    ?>
		<?php $fsl->displayPagingV2();
    ?>
	<?php 
}
    ?>

        </section>

	</div>
<?php 
} ?>	
