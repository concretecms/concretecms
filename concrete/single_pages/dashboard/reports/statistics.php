<?php defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>

<h4><?=t('Recent Page Views')?></h4>

<table class="table" id="ccm-site-statistics-visits" style="display: none">
<thead>
<tr>
	<td></td>
	<? foreach($pageViews as $day => $total) { ?>
		<th><?=$day?></th>
	<? } ?>
</tr>
</thead>
<tbody>
<tr>
	<th><?=t('Page Views')?></th>
	<? foreach($pageViews as $total) { ?>
		<td><?=$total?></td>
	<? } ?>
</tr>
</table>

<h4><?=t('Recent Registrations')?></h4>

<table class="table"  id="ccm-site-statistics-registrations" style="display: none">
<thead>
<tr>
	<td></td>
	<? foreach($userRegistrations as $day => $total) { ?>
		<th><?=$day?></th>
	<? } ?>
</tr>
</thead>
<tbody>
<tr>
	<th><?=t('User Registrations')?></th>
	<? foreach($userRegistrations as $total) { ?>
		<td><?=$total?></td>
	<? } ?>
</tr>
</table>

<h4><?=t('Pages Created')?></h4>

<table class="table"  id="ccm-site-statistics-new-pages" style="display: none">
<thead>
<tr>
	<td></td>
	<? foreach($newPages as $day => $total) { ?>
		<th><?=$day?></th>
	<? } ?>
</tr>
</thead>
<tbody>
<tr>
	<th><?=t('Pages Created')?></th>
	<? foreach($newPages as $total) { ?>
		<td><?=$total?></td>
	<? } ?>
</tr>
</table>


<p><?php echo t('Total page versions')?>: <strong><?php echo $totalVersions?></strong></p>
<p><?php echo t('Total pages in edit mode')?>: <strong><?php echo $totalEditMode?></strong></p>

<br/><br/>

<h4><?=t('Five Most Recent Downloads')?></h4>

<table class="table"  id="ccm-site-statistics-downloads">
<thead>
<tr>
	<th><?=t('File')?></th>
	<th><?=t('User')?></th>
	<th><?=t('Downloaded On')?></th>
</tr>
</thead>
<tbody>
<? if (count($downloads) == 0) { ?>
	<tr>
		<td colspan="3" style="text-align: center"><?=t('No files have been downloaded.')?></td>
	</tr>
<? } else { ?>
<?
	foreach($downloads as $download) {
		$f = File::getByID($download['fID']);
		if (!is_object($f)) {
			continue;
		}
		?>
	<tr>
		<td class='ccm-site-statistics-downloads-title'><a href="<?=$f->getDownloadURL()?>" title="<?=$f->getTitle();?>"><?php
		$title = $f->getTitle();
		$maxlen = 20;
		if (strlen($title) > ($maxlen-4)) {
			$ext = substr($title,strrpos($title, '.'));
			if (substr($ext,0,1) != '.') { $ext = ''; }
			$title = substr($title,0,$maxlen-4-strlen($ext)).'[..]'.$ext;
		}
		echo $title;
		?></a></td>
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
		<td><?=$dh->formatDateTime($download['timestamp'])?></td>
	</tr>
	<? } ?>
<? } ?>
</table>

<script type="text/javascript">
$(function() {
	$("#ccm-site-statistics-visits").visualize({
		'type': 'line',
		'appendKey': false,
		'colors': ['#C6DCF1'],
		'width': '500'
	});
	$("#ccm-site-statistics-registrations").visualize({
		'type': 'line',
		'appendKey': false,
		'colors': ['#B2E4BA'],
		'width': '500'
	});
	$("#ccm-site-statistics-new-pages").visualize({
		'type': 'line',
		'appendKey': false,
		'colors': ['#B2E4BA'],
		'width': '500'
	});

});
</script>