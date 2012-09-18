<? 
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
if (defined('ENABLE_USER_PROFILES') && ENABLE_USER_PROFILES && $u->isRegistered()) { ?>

<div style="display: none">
<div class="btn-group" id="ccm-account-menu">
  <button class="btn" data-toggle="dropdown"><?=t('My Account')?></button>
  <button class="btn dropdown-toggle" data-toggle="dropdown">
	<span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
  <?
	$account = Page::getByPath('/account');
	$children = $account->getCollectionChildrenArray(true);
	foreach($children as $cID) {
		$nc = Page::getByID($cID, 'ACTIVE');
		$ncp = new Permissions($nc);
		if ($ncp->canRead() && (!$nc->getAttribute('exclude_nav'))) {
			$categories[] = $nc;	
		}
	}
	
	foreach($categories as $cc) { ?>
		<li class="nav-header"><?=$cc->getCollectionName()?></li>
<?
		$subchildren = $cc->getCollectionChildrenArray(true);
		foreach($subchildren as $cID) {
			$nc = Page::getByID($cID, 'ACTIVE');
			$ncp = new Permissions($nc);
			if ($ncp->canRead() && (!$nc->getAttribute('exclude_nav'))) { ?>
				
				<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($nc)?>"><?=$nc->getCollectionName()?></a></li>
			
			<?

			}
		}
	}
	?>
	
	<li class="divider"></li>
	<li><a href="<?=DIR_REL?>/"><i class="icon-home"></i> <?=t("Home")?></a></li>
	<li><a href="<?=$this->url('/login', 'logout')?>"><i class="icon-remove"></i> <?=t("Sign Out")?></a></li>
 </ul>
</div>
</div>

<?
}
