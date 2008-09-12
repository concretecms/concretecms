<?php  if (isset($message)) { ?>

<h1>Install Concrete</h1>

<?php echo $message?>
<br/><br/>
<a href="<?php echo DIR_REL?>/">Continue to your site &gt;</a>

<?php  } else { ?>

<h1>Install Concrete</h1>
	
<p>Concrete could not locate <strong>site.php</strong>, within the config directory. To install Concrete, 
please fill out the form below.
</p>

<div class="ccm-form">

<form action="<?php echo $this->url('/install', 'configure')?>" method="post">

	
	<label for="SITE">Name Your Site:</label><br/>
	<?php echo $form->text('SITE'); ?>
	<br/><br/>
	
	<label for="BASE_URL">Base URL</label><br/>
	<?php echo $form->text('BASE_URL', BASE_URL); ?>
	<div class="ccm-form-sub">e.g. <strong>http://www.mysite.com</strong> - no trailing slash</div>
	<br/>
	
	
	<label for="DIR_REL">Subdirectory for site:</label><br/>
	<?php echo $form->text('DIR_REL', DIR_REL); ?>
	<div class="ccm-form-sub">e.g. http://www.mysite.com<strong>/concrete</strong>/</div>
	
	<br/>
	
	<h2>Personal Information</h2>
	
	<label for="uEmail">Your Email Address</label><br/>
	<?php echo $form->text('uEmail'); ?>
	
	<br/><br/>
	
	<h2>Database Information</h2>
	
	<label for="DB_SERVER">Server</label><br/>
	<?php echo $form->text('DB_SERVER'); ?>
	<br/><br/>
	
	<label for="DB_USERNAME">MySQL Username</label><br/>
	<?php echo $form->text('DB_USERNAME'); ?>
	<br/><br/>
	
	<label for="DB_PASSWORD">MySQL Password</label><br/>
	<?php echo $form->text('DB_PASSWORD'); ?>
	<br/><br/>
	
	<label for="DB_DATABASE">Database Name</label><br/>
	<?php echo $form->text('DB_DATABASE'); ?>
	<br/>
	
	<div class="ccm-button">
	<?php echo $form->submit('submit', 'Install Concrete &gt;')?>
	</div>
	

</form>
</div>
<?php  } ?>
