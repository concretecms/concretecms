<? defined('C5_EXECUTE') or die("Access Denied.");?>

<h1><span><?=t('Multilingual Setup')?></span></h1>
<div class="ccm-dashboard-inner">
<h2><?=t('Interface')?></h2>
<? 

if (count($languages) == 0) { ?>
	<?=t("You don't have any interface languages installed. You must run concrete5 in English.");?>
<? } else { ?>
	
	<form method="post" action="<?=$this->action('save_interface_language')?>">
	<div><?=$form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN)?> <?=$form->label('LANGUAGE_CHOOSE_ON_LOGIN', t('Offer choice of language on login.'))?></div>
	<? if (defined('LOCALE')) { ?>
	<br/>
		<strong><?=t('Default Language: ')?></strong>
		<div><? foreach($interfacelocales as $sl => $v) {
			if ($sl == LOCALE) {
				print $v;
			}
		} ?> <?=t('This has been set in config/site.php')?></div>
	<? } else { ?>
		<div><?=$form->label('SITE_LOCALE', t('Default Language'))?> <?=$form->select('SITE_LOCALE', $interfacelocales, SITE_LOCALE);?></div>
	<? } ?>
	
	<br/>
	<?=Loader::helper('validation/token')->output('save_interface_language')?>
	<?= Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'left')?>
	</form>
	
<? } ?>

</div>


<h1><span><?=t("Multilingual Content")?></span></h1>
<div class="ccm-dashboard-inner">
<? if ($LANGUAGE_MULTILINGUAL_CONTENT_ENABLED) { ?>

<h2><?=t('Content Sections')?></h2>
<? 
$nav = Loader::helper('navigation');
if (count($pages) > 0) { ?>
	<table class="ccm-results-list" style="width: auto">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th style="width: 200px"><?=t("Name")?></td>
		<th style="width: 200px"><?=t('Path')?></th>
		<th>&nbsp;</th>
	</tr>
	<? foreach($pages as $pc) { ?>
		<tr>
			<td><img src="<?=$pc->getCollectionIcon()?>" /></td>
			<td><a href="<?=$nav->getLinkToCollection($pc)?>"><?=$pc->getCollectionName()?></a></td>
			<td><?=$pc->getCollectionPath()?></td>
			<td><a href=""><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" /></td>
		</tr>
	<? } ?>
	</table>
	<br/><br/>
<? } else { ?>
	<p><?=t('You have not created any multilingual content sections yet.')?></p>
<? } ?>
<form method="post" action="<?=$this->action('add_content_section')?>">
	<h2><?=t('Add a Language')?></h2>
	
	<h3><?=$form->label('lsLanguage', t('Choose Language'))?></h3>
	<div><?=$form->select('lsLanguage', $locales);?></div>
	
	<br/>
	
	<h3><?=t('Language Icon')?></h3>
	<div id="ccm-multilingual-language-icon">
	<?=t('Choose a Language')?>
	</div>
	
	<br/>
	<h3><?=t('Choose a Parent Page')?></h3>
	<?=Loader::helper('form/page_selector')->selectPage('pageID', '')?>
	<br/>
	<?=Loader::helper('validation/token')->output('add_content_section')?>
	<?=Loader::helper('concrete/interface')->submit(t('Add Content Section'), 'add', 'left')?>
</form>

<style type="text/css">
ul.ccm-multilingual-choose-flag {list-style-type: none;}

</style>

<script type="text/javascript">
$(function() {
	$("select[name=lsLanguage]").change(function() {
		ccm_multilingualPopulateIcons($(this).val(), '');
	});
	ccm_multilingualPopulateIcons($("select[name=lsLanguage]").val(), '<?=$_POST["languageIcon"]?>');
});

ccm_multilingualPopulateIcons = function(lang, icon) {
	if (lang != '') {
		$("#ccm-multilingual-language-icon").load('<?=$this->action("load_icons")?>', {'lsLanguage': lang, 'selectedLanguageIcon': icon});
	}
}

</script>

<? } else { ?>

<form method="post" action="<?=$this->action('enable_multilingual_content')?>">
<p><?=t('Click below to enable multilingual content on your site. You will want to do this if you are building a site that contains content in multiple languages.')?></p>
<div style="text-align: center">
<br/>
<?=Loader::helper('concrete/interface')->submit(t('Enable Multilingual Content'), 'enable', 'left')?>
</div>
	<?=Loader::helper('validation/token')->output('enable_multilingual_content')?>
</form>
<? } ?>
</div>