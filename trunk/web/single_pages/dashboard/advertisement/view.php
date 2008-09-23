<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('single_page');
$ih = Loader::helper('concrete/interface');

Loader::model('search/advertisement');
Loader::model('/advertisement/advertisement_group');
Loader::model('/advertisement/advertisement_details');

?>
<h1><span>Ad Listings</span></h1>
<div class="ccm-dashboard-inner">
<p>The following ads are currently available in your website. 
<input type="button" value="Create New Ad" style="vertical-align: middle" onclick="location.href='<?=$this->url('/dashboard/advertisement/details','load_details')?>'" />
</p>

<p>

<?
$s = new AdvertisementSearch($_REQUEST);
$res = $s->getResult($_GET['sort'], $_GET['start'], $_GET['order']);

$variables['agID'] = false;
$qs = Search::qsReplace($variables);
$qs = str_replace("&agID=","",$qs);
?>
Display: <select style="vertical-align: middle" ame="agID" onchange="location.href='<?=$this->url('/dashboard/advertisement/')?><?=$qs?>agID='+this.value">
	<option value="">** All Ads</option>
	<?
	$ag = new AdvertisementGroup();
	$groups = $ag->Find("TRUE ORDER BY agName");
	foreach($groups as $group) { ?>
		<option value="<?=$group->agID?>" <? if ($_REQUEST['agID'] == $group->agID) { ?> selected <? } ?>>In "<?=$group->agName?>"</option>
	<? } ?>
</select>
</p>

<? if($res->numRows()) { ?>
    
    <table border="0" cellspacing="1" cellpadding="0" class="grid-list">
    <tr>
        <?=$s->printHeader('Ad Name','name',1)?>
        <?=$s->printHeader('Target Impressions','targetImpressions',1)?>
        <?=$s->printHeader('Target CTs','targetClickThrus',1)?>
        <?=$s->printHeader('Impressions','impressions',1)?>
        <?=$s->printHeader('CTs','clickThrus',1)?>
         <?=$s->printHeader('')?>
    </tr><?
    
    while ($row = $res->fetchRow() ) {  ?>
        <tr>
            <?=$s->printRow($row['name'], 'name', $this->url('/dashboard/advertisement/details/',"load_details",$row['aID']));?>
            <?=$s->printRow($row['targetImpressions'], 'targetImpressions')?>
            <?=$s->printRow($row['targetClickThrus'], 'targetClickThrus')?>
            <?=$s->printRow($row['impressions'], 'impressions')?>
            <?=$s->printRow($row['clickThrus'], 'clickThrus')?>
            <?
            $del = '<a href="'.$this->url('/dashboard/advertisement/','delete_advertisement',$row['aID']).'" onclick="return confirm(\'Are you sure you would like to delete this ad?\')">Delete</a>';
			echo $s->printRow($del,"delete")?>
        </tr>
    <? } ?>
    </table>
    <? if ($pOptions['needPaging']) { ?>
        <br><br>
        <? include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>			
    <? } ?>
	
<? } else { ?>	
		<strong>No Ads found.</strong>
<? } ?> 
</div>