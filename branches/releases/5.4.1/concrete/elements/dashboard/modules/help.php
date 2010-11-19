<?php 
defined('C5_EXECUTE') or die("Access Denied.");
?>
<h2><?php echo t('Search Documentation')?></h2>
<form method="get" action="http://www.concrete5.org/search/">
<input type="text" name="query" style="width: 130px" />
<input type="hidden" name="do" value="search" />
<input type="submit" value="<?php echo t('Search')?>" />
</form>
<br/>

<h2><?php echo t('Full Documentation')?></h2>
<div><?php echo t('Full documentation is available <a href="%s">at Concrete5.org</a>', 'http://www.concrete5.org/docs/')?>.</div><br/>
