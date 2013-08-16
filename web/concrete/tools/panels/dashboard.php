<?
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard');
if ($dh->canRead()) {
	$c = Page::getByPath('/dashboard');
	$children = $c->getCollectionChildrenArray(true);

?>

<section>
	<header><?=t('Dashboard')?>
		<aside><a href=""><?=t('Logout')?></a></aside>
	</header>
	<menu>
		<? foreach($children as $cID) {
			$cc = Page::getByID($cID, 'ACTIVE');
			$cp = new Permissions($cc);
			if ($cp->canViewPage() && $cc->getAttribute('exclude_nav') != true) { ?>
				<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($cc)?>"><?=$cc->getCollectionName()?></a></li>
			<? }
		} ?>

	</menu>
</section>


<? } ?>