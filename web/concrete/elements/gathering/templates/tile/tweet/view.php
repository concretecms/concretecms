<? defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-gathering-twitter">
	<img class="twitter-logo" src="https://abs.twimg.com/a/1373252541/images/resources/twitter-bird-light-bgs.png" />
	<div class="tweet">
		<p><?=$tweet?></p>
		<p><span class="elapsed"><?=date('m/d/y', strtotime($date_time))?></span><span class="who-from"></span></p>
	</div>
</div>
