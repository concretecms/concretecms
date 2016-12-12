<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php', array('enableEditing' => true)); 
?>

<div class="ccm-ui" id="newsflow">
	<? $this->inc('elements/header_newsflow.php'); ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8">
				<div id="ccm-dashboard-welcome-back">
				<? $a = new Area('Primary'); $a->display($c); ?>
				</div>
				<div class="row">
					<div class="col-md-6">
						<? $a = new Area('Secondary 3'); $a->display($c); ?>
					</div>
					<div class="col-md-6">
						<? $a = new Area('Secondary 4'); $a->display($c); ?>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-accented">

				<? $a = new Area('Secondary 1'); $a->display($c); ?>
				<? $a = new Area('Secondary 2'); $a->display($c); ?>
				<? $a = new Area('Secondary 5'); $a->display($c); ?>

			</div>
		</div>
	</div>
</div>

<? $this->inc('elements/footer.php'); ?>