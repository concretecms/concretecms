<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">

<div id="ccm-file-properties-response"></div>

<?
$tabs = array(array('details', t('Details'), true));
$tabs[] = array('versions', t('Versions'));
$tabs[] = array('statistics', t('Statistics'));

if (!$previewMode) {
	print Loader::helper('concrete/ui')->tabs($tabs);
}
?>

<? if (!$previewMode) { ?>
<div class="ccm-tab-content container" id="ccm-tab-content-details" data-container="editable-fields">
<? } else { ?>
<div class="container">
<? } ?>

<section>

<? if (!$previewMode && $fp->canEditFileContents()) { ?>
	<a href="#" class="btn pull-right btn-default btn-xs" data-action="rescan"><?=t('Rescan')?></a>
<? } ?>

<h4><?=t('Basic Properties')?></h4>
<div class="row">
	<div class="col-md-3"><p><?=t('ID')?></p></div>
	<div class="col-md-9"><p><?=$fv->getFileID()?> <span style="color: #afafaf">(<?=t('Version')?> <?=$fv->getFileVersionID()?>)</p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Filename')?></p></div>
	<div class="col-md-9"><p><?=$fv->getFileName()?></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('URL to File')?></p></div>
	<div class="col-md-9"><p><?=$fv->getRelativePath(true)?></p></div>
</div>
<?
$oc = $f->getOriginalPageObject();
if (is_object($oc)) { 
	$fileManager = Page::getByPath('/dashboard/files/search'); 
	$ocName = $oc->getCollectionName();
	if (is_object($fileManager) && !$fileManager->isError()) {
		if ($fileManager->getCollectionID() == $oc->getCollectionID()) {
			$ocName = t('Dashboard File Manager');
		}
	}
	?>
	<div class="row">
		<div class="col-md-3"><p><?=t('Page Added To')?></p></div>
		<div class="col-md-9"><p><a href="<?=Loader::helper('navigation')->getLinkToCollection($oc)?>" target="_blank"><?=$ocName?></a></p></div>
	</div>
<? } ?>

<div class="row">
	<div class="col-md-3"><p><?=t('Type')?></p></div>
	<div class="col-md-9"><p><?=$fv->getType()?></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Size')?></p></div>
	<div class="col-md-9"><p><?=$fv->getSize()?> (<?=t2(/*i18n: %s is a number */ '%s byte', '%s bytes', $fv->getFullSize(), Loader::helper('number')->format($fv->getFullSize()))?>)</p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Date Added')?></p></div>
	<div class="col-md-9"><p><?=t('Added by <strong>%s</strong> on %s', $fv->getAuthorName(), $dateHelper->date(DATE_APP_FILE_PROPERTIES, strtotime($f->getDateAdded())))?></p></div>
</div>
<?

$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
if (is_object($fsl)) {
	if ($f->getStorageLocationID() > 0) {
		$sli = $fsl->getName() . ' <span style="color: #afafaf">(' . $fsl->getDirectory() . ')</span>';;
	}
}
if (!isset($sli)) {
	$sli = t('Default Location') . ' <span style="color: #afafaf">(' . DIR_FILES_UPLOADED . ')</span>';
}
?>
<div class="row">
	<div class="col-md-3"><p><?=t('Location')?></p></div>
	<div class="col-md-9"><p><?=$sli?></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Title')?></p></div>
	<div class="col-md-9"><p><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="text" data-name="fvTitle"<? } ?>><?=$fv->getTitle()?></span></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Description')?></p></div>
	<div class="col-md-9"><p><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="textarea" data-name="fvDescription"<? } ?>><?=$fv->getDescription()?></span></p></div>
</div>
<div class="row">
	<div class="col-md-3"><p><?=t('Tags')?></p></div>
	<div class="col-md-9"><p><span <? if ($fp->canEditFileProperties()) { ?>data-editable-field-type="xeditable" data-type="textarea" data-name="fvTags"<? } ?>><?=$fv->getTags()?></span></p></div>
</div>
</section>

<?
$attribs = FileAttributeKey::getImporterList($fv);
$ft = $fv->getType();

if (count($attribs) > 0) { ?>

<section>
<h4><?=t('%s File Properties', $ft)?></h4>

<?

Loader::element('attribute/editable_list', array(
	'attributes' => $attribs, 
	'object' => $f,
	'saveAction' => $controller->action('update_attribute'),
	'clearAction' => $controller->action('clear_attribute'),
	'permissionsArguments' => $fp->canEditFileProperties(),
	'permissionsCallback' => function($ak, $permissionsArguments) {
		return $permissionsArguments;
	}
));?>

<? } ?>
</section>

<? 
$attribs = FileAttributeKey::getUserAddedList();

if (count($attribs) > 0) { ?>

<section>

<h4><?=t('Other Properties')?></h4>

<? Loader::element('attribute/editable_list', array(
	'attributes' => $attribs, 
	'object' => $f,
	'saveAction' => $controller->action('update_attribute'),
	'clearAction' => $controller->action('clear_attribute'),
	'permissionsArguments' => $fp->canEditFileProperties(),
	'permissionsCallback' => function($ak, $permissionsArguments) {
		return $permissionsArguments;
	}
));?>

</section>

<? } ?>

<section>

<h4><?=t('File Preview')?></h4>

<div style="text-align: center">
<?=$fv->getThumbnail(2)?>
</div>

</section>

</div>

<? if (!$previewMode) { ?>

<div class="ccm-tab-content" id="ccm-tab-content-versions">

	<h4><?=t('Versions')?></h4>

	<table border="0" cellspacing="0" width="100%" id="ccm-file-versions" class="table" cellpadding="0">
	<tr>
		<th>&nbsp;</th>
		<th><?=t('Filename')?></th>
		<th><?=t('Title')?></th>
		<th><?=t('Comments')?></th>
		<th><?=t('Creator')?></th>
		<th><?=t('Added On')?></th>
		<? if ($fp->canEditFileContents()) { ?>
			<th>&nbsp;</th>
		<? } ?>
	</tr>
	<?
	$versions = $f->getVersionList();
	foreach($versions as $fvv) { ?>
		<tr <? if ($fvv->getFileVersionID() == $fv->getFileVersionID()) { ?> class="success" <? } ?> data-file-version-id="<?=$fvv->getFileVersionID()?>">
			<td style="text-align: center">
				<input type="radio" name="fvID" value="<?=$fvv->getFileVersionID()?>" <? if ($fvv->getFileVersionID() == $fv->getFileVersionID()) { ?>checked<? } ?> />
			</td>
			<td width="100">
				<div style="width: 150px; word-wrap: break-word">
				<a href="<?=URL::to('/system/dialogs/file/properties')?>?fID=<?=$f->getFileID()?>&amp;fvID=<?=$fvv->getFileVersionID()?>" dialog-modal="false" dialog-width="630" dialog-height="450" dialog-title="<?=t('Preview File')?>" class="dialog-launch">
					<?=$fvv->getFilename()?>
				</a>
				</div>
			</td>
			<td> 
				<div style="width: 150px; word-wrap: break-word">
					<?=$fvv->getTitle()?>
				</div>
			</td>
			<td><?
				$comments = $fvv->getVersionLogComments();
				if (count($comments) > 0) {
					print t('Updated ');

					for ($i = 0; $i < count($comments); $i++) {
						print $comments[$i];
						if (count($comments) > ($i + 1)) {
							print ', ';
						}
					}
					
					print '.';
				}
				?>
				</td>
			<td><?=$fvv->getAuthorName()?></td>
			<td><?=$dateHelper->date(DATE_APP_FILE_VERSIONS, strtotime($fvv->getDateAdded()))?></td>
			<? if ($fp->canEditFileContents()) { ?>
				<td><a data-action="delete-version" data-file-version-id="<?=$fvv->getFileVersionID()?>" href="javascript:void(0)"><i class="glyphicon glyphicon-trash"></i></a></td>
			<? } ?>
		</tr>	
	
	<? } ?>
	
	</table>

</div>

<div class="ccm-tab-content" id="ccm-tab-content-statistics">
	
	<?
	$downloadStatistics = $f->getDownloadStatistics();
	?>

<section>
	<h4><?=t('Total Downloads')?></h4>
	<div><?=$f->getTotalDownloads()?></div>
</section>

<section>
	<h4><?=t('Most Recent Downloads')?></h4>
	<table border="0" cellspacing="0" width="100%" class="table" cellpadding="0">
		<tr> 
			<th><?=t('User')?></th>
			<th><?=t('Download Time')?></th>
			<th><?=t('File Version ID')?></th>
		</tr>	
		<?
		
		$downloadStatsCounter=0;
		foreach($downloadStatistics as $download){ 
			$downloadStatsCounter++;
			if($downloadStatsCounter>20) break;
			?>
		<tr>
			<td>
				<? 
				$uID=intval($download['uID']);
				if(!$uID){
					echo t('Anonymous');
				}else{ 
					$downloadUI = UserInfo::getById($uID);
					if($downloadUI instanceof UserInfo) {
						echo $downloadUI->getUserName();
					} else {
						echo t('Deleted User');
					}
				} 
				?>
			</td>
			<td><?=$dateHelper->date(DATE_APP_FILE_DOWNLOAD, strtotime($download['timestamp']))?></td>
			<td><?=intval($download['fvID'])?></td>
		</tr>
		<? } ?>
	</table>
</section>
</div>
<? } ?>

</div>
<style type="text/css">
#ccm-file-properties-response #ccm-notification-hud {
	position: relative;
	padding: 10px 10px 10px 30px;
	margin-bottom: 20px;
	top: 0px;
	left: 0px;
}

#ccm-file-properties-response #ccm-notification-hud i {
	top: 11px;
	left: 8px;
}

tr.success a[data-action=delete-version] {
	display: none;
}

a[data-action=delete-version] {
	color: #333;
}

a[data-action=delete-version]:hover {
	color: #000;
	text-decoration: none;
}

</style>

<script type="text/javascript">

var ConcreteFilePropertiesDialog = function() {
	var my = this;
	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		url: '<?=$controller->action('save')?>'
	});
	my.setupFileVersionsTable();
	my.setupFileRescan();
}

ConcreteFilePropertiesDialog.prototype = {

	handleAjaxResponse: function(r, callback) {
		if (r.error) {
			ConcreteAlert.showResponseNotification(r.message, 'exclamation-sign', '#ccm-file-properties-response');
		} else if (callback) {
			callback(r);
		} else {
			ConcreteAlert.showResponseNotification('<?=t('File recanned successfully.')?>', 'pencil', 'success', '#ccm-file-properties-response');
		}
	},

	setupFileRescan: function() {
		var my = this;
		$('a[data-action=rescan]').on('click', function() {
			$.concreteAjax({
				url: '<?=URL::to('/system/file/rescan')?>',
				data: {'fID': '<?=$f->getFileID()?>'},
				success: function(r) {
					my.handleAjaxResponse(r);
				}
			});
			return false;
		});
	},

	setupFileVersionsTable: function() {
		var my = this;
		$versions = $('#ccm-file-versions');
		$versions.on('click', 'input[name=fvID]', function() {
			var fvID = $(this).val();
			$.concreteAjax({
				url: '<?=URL::to('/system/file/approve_version')?>',
				data: {'fID': '<?=$f->getFileID()?>', 'fvID': fvID},
				success: function(r) {
					my.handleAjaxResponse(r, function() {
						$versions.find('tr[class=success]').removeClass();
						$versions.find('tr[data-file-version-id=' + fvID + ']').addClass('success');
					});
				}
			});
		});
		$versions.on('click', 'a[data-action=delete-version]', function() {
			var fvID = $(this).attr('data-file-version-id');
			$.concreteAjax({
				url: '<?=URL::to('/system/file/delete_version')?>',
				data: {'fID': '<?=$f->getFileID()?>', 'fvID': fvID},
				success: function(r) {
					my.handleAjaxResponse(r, function() {
						var $row = $versions.find('tr[data-file-version-id=' + fvID + ']');
						$row.queue(function() {
							$(this).addClass('animated fadeOutDown');
							$(this).dequeue();
						}).delay(500).queue(function() {
							$(this).remove();
							$(this).dequeue();
						});
					});
				}
			});
		});

	}

}

<? if (!$previewMode) { ?>
$(function() {
	var dialog = new ConcreteFilePropertiesDialog();
});
<? } ?>
</script>