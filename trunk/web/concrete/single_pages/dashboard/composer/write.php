<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if (isset($entry)) { ?>

	<form method="post" enctype="multipart/form-data" action="<?=$this->action('save')?>" id="ccm-dashboard-composer-form">
	
	<h1><span><?=ucfirst($action)?> <?=$ct->getCollectionTypeName()?></span></h1>
	<div class="ccm-dashboard-inner" id="ccm-dashboard-composer">
	<div id="composer-save-status"></div>
	<h2><?=t("Basic Information")?></h2>
	<ol>
		<li>
		<strong><?=$form->label('cName', t('Name'))?></strong><br/>
		<?=$form->text('cName', $name)?>		
		</li>
		<li>
		<strong><?=$form->label('cDescription', t('Short Description'))?></strong><br/>
		<?=$form->textarea('cDescription', $description)?>		
		</li>
	</ol>
	
	<?
	if (count($attribs) > 0) { ?>
	
	<h2><?=t("Attributes")?></h2>
	<ol>
	<? foreach($attribs as $ak) { 
		if (is_object($entry)) {
			$value = $entry->getAttributeValueObject($ak);
		}
		?>
		<li><strong><?=$ak->render('label');?></strong><br/>
		<?=$ak->render('form', $value, true)?>	
		</li>
	<? } ?>
	</ol>
	
	<? } 
	
	if (count($blocks) > 0) { 
	?>
	
		<h2><?=t('Content')?></h2>
	<ol>
	<? foreach($blocks as $b) { ?>
		<li><? if ($b->getBlockName() != '') { ?>
			<h3><?=$b->getBlockName()?></h3>
		<? } else {
			$btName = $b->getBlockTypeName();
		?>
		
			<h3><?=$btName?></h3>
		
		<? } ?>
		
		<?
		$bv = new BlockView();
		$bv->render($b, 'composer');
		?>
	
	<? } ?>
	
	</ol>
	
	
	<? } ?>

		<?=Loader::helper('concrete/interface')->submit(t('Save Draft'), 'save', 'left')?>
		<?=Loader::helper('concrete/interface')->submit(t('Publish Page'), 'publish')?>
		<?=$form->hidden('entryID', $entry->getCollectionID())?>
		<?=$form->hidden('autosave', 0)?>
		<?=Loader::helper('validation/token')->output('composer')?>
		<div class="ccm-spacer">&nbsp;</div>
		
	</div>
	</form>

	<script type="text/javascript">
	var ccm_composerAutoSaveStarted = false;
	var ccm_composerAutoSaveInterval = false;
	var ccm_composerDoAutoSave = false;
	var ccm_composerMaxSecondsSinceSave = 10;
	var ccm_composerLastSaveTime = new Date("<?=date('m/d/Y g:i:s a')?>");
	
	ccm_composerCheckAutoSave = function() {
		if (ccm_composerDoAutoSave) {
			$('input[name=autosave]').val('1');
			$('#ccm-dashboard-composer-form').ajaxSubmit({
				'dataType': 'json',
				'success': function(r) {
					ccm_composerLastSaveTime = new Date();
					$("#composer-save-status").html('<?=t("Page saved at ")?>' + r.time)
				}
			});
			$('input[name=autosave]').val('0');
			ccm_composerDoAutoSave = false;
		} else {
			
			// first check - has it been longer than X seconds since last save
			var ms = ccm_composerMaxSecondsSinceSave * 1000;
			var secondsSinceLastSave = (new Date() - ccm_composerLastSaveTime);
			if (ms < secondsSinceLastSave) {
				ccm_composerDoAutoSave = true;
			}
		}
	}
		
	$(function() {
		var ccm_composerAutoSaveIntervalTimeout = 5000;
		
		// don't start the auto-save until something changes
		$("input, textarea, select").change(function() {
			if (!ccm_composerAutoSaveStarted) {
				ccm_composerAutoSaveInterval = setInterval(function() {
					ccm_composerCheckAutoSave();
				}, ccm_composerAutoSaveIntervalTimeout);
			}
			ccm_composerAutoSaveStarted = true;
		});
	});
	</script>
	
	
<? } else { ?>

	<h1><span><?=t('Composer')?></span></h1>
	<div class="ccm-dashboard-inner" id="ccm-dashboard-composer">


	<? if (count($ctArray) > 0) { ?>
	<h2><?=t('What type of page would you like to write?')?></h2>
	<ul>
	<? foreach($ctArray as $ct) { ?>
		<li><a href="<?=$this->url('/dashboard/composer/write', $ct->getCollectionTypeID())?>"><?=$ct->getCollectionTypeName()?></a></li>
	<? } ?>
	</ul>
	<? } else { ?>
		<p><?=t('You have not setup any page types for Composer.')?></p>
	<? } ?>

	</div>
	
<? } ?>

