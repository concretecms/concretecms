<div class="discussion">

<?
$nav = Loader::helper('navigation');
foreach($posts as $p) { 
	
	$title = $p->getSubject();
	
	?>

	<h3><a href="<?=$nav->getLinkToCollection($p)?>"><?=$title?></a></h3>
	<div class="discussion-info">
		by
		<span class="discussion-author"><?=$p->getUserName()?></span>
	</div>
	
		
<? } ?>
</div>

<br/><br/>

<a href="<?=$this->action('add')?>">Add Discussion</a>