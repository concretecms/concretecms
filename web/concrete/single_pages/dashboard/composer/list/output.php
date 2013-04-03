<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('%s Output', $composer->getComposerName()), false, false)?>

<? 
$tabs = array();
$i = 0;
foreach($composer->getComposerPageTypeObjects() as $ct) {
	$tabs[] = array($ct->getCollectionTypeID(), $ct->getCollectionTypeName(), $i == 0);
	$i++;
}
print Loader::helper('concrete/interface')->tabs($tabs);

foreach($composer->getComposerPageTypeObjects() as $ct) { ?>
	
	<div id="ccm-tab-content-<?=$ct->getCollectionTypeID()?>" class="ccm-tab-content" data-composer-control-output-collection-type-id="<?=$ct->getCollectionTypeID()?>">
		<?
		$areas = ComposerOutputControl::getCollectionTypeAreas($ct);
		if (count($areas) > 0) {

			foreach($areas as $area) { ?>

				<div class="ccm-composer-control-output-area" data-composer-control-output-area="<?=$area?>">
					<div class="ccm-composer-control-output-area-handle" ><?=$area?></div>
					<div class="ccm-composer-control-output-area-inner">
						<? $controls = ComposerOutputControl::getList($composer, $ct, $area);
						foreach($controls as $cnt) { ?>
							<? Loader::element('composer/output/control', array('control' => $cnt));?>
						<? } ?>
					</div>
				</div>

			<? } ?>
		<? } else { ?>
			<p><?=t('There are no areas.')?></p>
		<? }
		?>
	</div>

<? } ?>


<script type="text/javascript">
$(function() {

	$('.ccm-composer-control-output-area-inner').sortable({
		handle: 'a[data-command=move-output-control]',
		items: '.ccm-composer-output-control',
		connectWith: '.ccm-composer-control-output-area-inner',
		cursor: 'move',
		axis: 'y', 
		stop: function() {

			$('.ccm-tab-content:visible').each(function() {
				var ctID = $(this).attr('data-composer-control-output-collection-type-id');

				var formData = [{
					'name': 'token',
					'value': '<?=Loader::helper("validation/token")->generate("update_output_control_display_order")?>'
				}, {
					'name': 'cmpID',
					'value': '<?=$composer->getComposerID()?>'
				}, {
					'name': 'ctID',
					'value': ctID
				}];

				$(this).find('div[data-composer-control-output-area]').each(function() {
					var area = $(this).attr('data-composer-control-output-area');
					$(this).find('div[data-composer-output-control-id]').each(function() {
						var controlID = $(this).attr('data-composer-output-control-id');
						formData.push({'name': 'area[' + area + '][]', 'value': controlID});
					});
				});

				$.ajax({
					type: 'post',
					data: formData,
					url: '<?=$this->action("update_output_control_display_order")?>',
					success: function() {}
				});
			});
		}
	});

});
</script>

<style type="text/css">

div.ccm-tab-content {
	padding: 10px;
}

div.ccm-composer-control-output-area {
	margin-bottom: 20px;
}

div.ccm-composer-control-output-area:last-child {
	margin-bottom: 0px;
}

div.ccm-composer-control-output-area-inner {
	border: 1px solid #eee;
}

div.ccm-composer-control-output-area-handle {
	border-left: 1px solid #eee;
	border-right: 1px solid #eee;
	border-top: 1px solid #eee;
	background-color: #f1f1f1;
	padding: 4px 4px 4px 8px;
	color: #888;
	border-top-left-radius: 4px;
	border-top-right-radius: 4px;
}

div.ccm-composer-item-control-bar {
	position: relative;
}

div.ccm-composer-output-control div.ccm-composer-item-control-bar {
	background-color: #fafafa;
	border-bottom: 1px solid #dedede;
	padding: 4px 10px 4px 10px;
}

div.ccm-composer-output-control:last-child div.ccm-composer-item-control-bar {
	border-bottom: 0px;
}


ul.ccm-composer-item-controls {
	position: absolute;
	right: 8px;
	top: 5px;
}

ul.ccm-composer-item-controls a {
	color: #333;
}

ul.ccm-composer-item-controls a i {
	position: relative;
}

ul.ccm-composer-item-controls a:hover {
	text-decoration: none;
}

div.ccm-composer-item-control-bar:hover ul.ccm-composer-item-controls li {
	display: inline-block;
}

ul.ccm-composer-item-controls li {
	list-style-type: none;
	display: none;
}



</style>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>