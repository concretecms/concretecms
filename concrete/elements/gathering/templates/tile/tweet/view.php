<?php defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-gathering-twitter">
	<img class="twitter-logo" src="https://abs.twimg.com/a/1373252541/images/resources/twitter-bird-light-bgs.png" />
	<div class="tweet">
		<span class="tweet-body"><?=$description?></span>
		<div style="clear: both;"></div>
		<p class="tweet-info"><span class="elapsed"><?=date('m/d/y', strtotime($date_time))?></span><span class="who-from"><a href="http://www.twitter.com/<?php echo $author ?>"><?php echo $author ?></a></span></p>
	</div>
</div>
