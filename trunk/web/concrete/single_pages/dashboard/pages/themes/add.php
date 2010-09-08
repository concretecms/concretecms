<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<h1><span><?=t('Add a Template')?></span></h1>
<div class="ccm-dashboard-inner">

<br/>
<?=t('Upload a template bundle by using the form below. Files must be zipped and contain only HTML files and sub directories. All items referenced in HTML must be done so with relative links.')?>

<br/><br/>
<form action="<?=$addurl?>" method="post" enctype="multipart/form-data">

<input type="file" name="archive" /> <input type="submit" name="install" value="<?=t('Add Theme')?>" />

</form>
<br/>

<a href="<?=$this->url('/dashboard/themes')?>">&lt; <?=t('Return to Template List')?></a>

</div>