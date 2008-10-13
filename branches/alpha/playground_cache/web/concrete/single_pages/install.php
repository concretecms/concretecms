<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 

<? if (isset($message)) { ?>

<h1>Install Concrete</h1>

<?=$message?>
<br/><br/>
<a href="<?=DIR_REL?>/">Continue to your site &gt;</a>

<? } else { ?>

<h1>Install Concrete</h1>
	
<p>Concrete could not locate <strong>site.php</strong>, within the config directory. To install Concrete, 
please fill out the form below.
</p>

<div class="ccm-form">

<form action="<?=$this->url('/install', 'configure')?>" method="post">

	
	<label for="SITE">Name Your Site:</label><br/>
	<?=$form->text('SITE'); ?>
	<br/><br/>
	
	<label for="BASE_URL">Base URL</label><br/>
	<?=$form->text('BASE_URL', BASE_URL); ?>
	<div class="ccm-form-sub">e.g. <strong>http://www.mysite.com</strong> - no trailing slash</div>
	<br/>
	
	
	<label for="DIR_REL">Subdirectory for site:</label><br/>
	<?=$form->text('DIR_REL', DIR_REL); ?>
	<div class="ccm-form-sub">e.g. http://www.mysite.com<strong>/concrete</strong>/</div>
	
	<br/>
	
	<h2>Personal Information</h2>
	
	<label for="uEmail">Your Email Address</label><br/>
	<?=$form->text('uEmail'); ?>
	
	<br/><br/>
	
	<h2>Database Information</h2>
	
	<label for="DB_SERVER">Server</label><br/>
	<?=$form->text('DB_SERVER'); ?>
	<br/><br/>
	
	<label for="DB_USERNAME">MySQL Username</label><br/>
	<?=$form->text('DB_USERNAME'); ?>
	<br/><br/>
	
	<label for="DB_PASSWORD">MySQL Password</label><br/>
	<?=$form->text('DB_PASSWORD'); ?>
	<br/><br/>
	
	<label for="DB_DATABASE">Database Name</label><br/>
	<?=$form->text('DB_DATABASE'); ?>
	<br/>
	
	<div class="ccm-button">
	<?=$form->submit('submit', 'Install Concrete &gt;')?>
	</div>
	

</form>
</div>
<? } ?>
