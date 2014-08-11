<? defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Drafts'))?>

<? 
if (count($drafts) > 0) { ?>

<table class="table table-striped">
<tr>
	<th width="60%"><?=t('Page Name')?></th>
	<th width="10%"><?=t('Author')?></th>
	<th width="10%"><?=t('Page Type')?></th>
	<th width="10%"><?=t('Last Modified')?></th>
</tr>
<?
$num = 0;
foreach($drafts as $dr) { 
	$pdr = new Permissions($dr);
	if ($pdr->canEditPage()) { 
		$num++;
		$pageName = ($dr->getCollectionName()) ? $dr->getCollectionName() : t('(Untitled Page)');
		?>
	<tr>
		<td><a href="<?=$view->url('/dashboard/composer/write', 'draft', $dr->getCollectionID())?>"><?=$pageName?></a></td>
		<td><?
		$ui = UserInfo::getByID($dr->getCollectionUserID());
		if (is_object($ui)) {
			print $ui->getUserDisplayName();
		} else {
			print t('(Unknown author)');
		}
		?>
		</td>
		<td><?=$dr->getPageTypeName()?></td>
		<td><?=$dh->formatPrettyDateTime($dr->getCollectionDateLastModified());?></td>
	<? } ?>

<? } ?>
</table>

<? } 

if ($num == 0) { ?>
	
	<p><?=t('There are no drafts. <a href="%s">Visit Composer &gt;</a>', $view->url('/dashboard/composer/write'))?></p>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
