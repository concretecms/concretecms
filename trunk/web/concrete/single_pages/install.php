<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 

<? if (isset($message)) { ?>

<h1><?=t('Install Concrete')?></h1>

<?=$message?>
<br/><br/>
<a href="<?=DIR_REL?>/"><?=t('Continue to your site')?> &gt;</a>

<? } else { ?>

<h1><?=t('Install Concrete')?></h1>
	
<p>
<?=t('Concrete could not locate <strong>site.php</strong>, within the config directory. ')?>
<?=t('To install Concrete, please fill out the form below.')?>
</p>

<div class="ccm-form">

<form action="<?=$this->url('/install', 'configure')?>" method="post">

	
	<label for="SITE"><?=t('Name Your Site')?>:</label><br/>
	<?=$form->text('SITE'); ?>
	<br/><br/>
	
	<label for="BASE_URL"><?=t('Base URL')?>:</label><br/>
	<?=$form->text('BASE_URL', BASE_URL); ?>
	<div class="ccm-form-sub"><?=t('e.g. <strong>http://www.mysite.com</strong> - no trailing slash')?></div>
	<br/>
	
	
	<label for="DIR_REL"><?=t('Subdirectory for site')?>:</label><br/>
	<?=$form->text('DIR_REL', DIR_REL); ?>
	<div class="ccm-form-sub"><?=t('e.g. http://www.mysite.com<strong>/concrete</strong>/')?></div>	
	<br/>
	
	<h2><?=t('Personal Information')?></h2>
	
	<label for="uEmail"><?=t('Your Email Address')?></label><br/>
	<?=$form->text('uEmail'); ?>
	
	<br/><br/>
	
	<h2><?=t('Database Information')?></h2>
	
	<label for="DB_SERVER"><?=t('Server')?></label><br/>
	<?=$form->text('DB_SERVER'); ?>
	<br/><br/>
	
	<label for="DB_USERNAME"><?=t('MySQL Username')?></label><br/>
	<?=$form->text('DB_USERNAME'); ?>
	<br/><br/>
	
	<label for="DB_PASSWORD"><?=t('MySQL Password')?></label><br/>
	<?=$form->text('DB_PASSWORD'); ?>
	<br/><br/>
	
	<label for="DB_DATABASE"><?=t('Database Name')?></label><br/>
	<?=$form->text('DB_DATABASE'); ?>
	<br/>
	
	<div class="ccm-button">
	<?=$form->submit('submit', t('Install Concrete').' &gt;')?>
	</div>
	

</form>
</div>
<? } ?>
