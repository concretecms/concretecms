<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="row">
<div class="span14 offset1 columns">
<div class="ccm-dashboard-pane">


	<div class="ccm-dashboard-pane-header"><h3><?=$c->getCollectionName()?></h3></div>
	<div class="ccm-dashboard-pane-body clearfix" id="ccm-stack-container">
	<? $a = new Area(STACKS_AREA_NAME); $a->display($c); ?>
	</div>

</div>
</div>
</div>