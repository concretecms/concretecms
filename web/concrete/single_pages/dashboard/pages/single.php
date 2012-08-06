<?
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper('concrete/interface');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Single Pages'), false);?>
	<div class="clearfix">
		<h3><?php echo t('Add Single Page')?></h3>
		<?php if(URL_REWRITING == true || URL_REWRITING_ALL == true) {
			$base = BASE_URL.DIR_REL;
		} else { 
			$base = BASE_URL.DIR_REL.'/'.DISPATCHER_FILENAME;
		}?>
		<form class="form-stacked" method="post" id="add_static_page_form" action="<?php echo $this->url('/dashboard/pages/single')?>">
			<?php echo $this->controller->token->output('add_single_page')?>
			<div class="control-group">
			<label for="pageURL" class="control-label"><?php echo t('The page you want to add is available at:')?></label>
			<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><?php echo $base?>/</span><input type="text" name="pageURL" value="<?php echo $this->post('pageURL')?>" class="span4" />
			</div>
				<button class="btn" type="submit"?><?=t('Add')?></button>
			</div>
			</div>

		</form>
		<h3><?php echo t('Already Installed')?></h3>
		<table border="0" cellspacing="1" cellpadding="0" class="table table-striped">
			<thead>
				<tr>
					<th class="subheader" width="100%"><?php echo t('Name')?></th>
					<th class="subheader"><?php echo t('Path')?></th>
					<th class="subheader"><?php echo t('Package')?></th>
					<th class="subheader"><div style="width: 90px"></div></th>
				</tr>
			</thead>
			<?php if (count($generated) == 0) { ?>
				<tr>
					<td colspan="4">
						<?php echo t('No pages found.')?>
					</td>
				</tr>
			<?php } else { ?>
		
				<?php foreach ($generated as $p) { 
					$cp = new Permissions($p);
					if ($p->getPackageID() > 0) {
						$package = Package::getByID($p->getPackageID());
						if(is_object($package)) {
							$packageHandle = $package->getPackageHandle();
							$packageName = $package->getPackageName();
						}
					} else {
						$packageName = t('None');
					} ?>
					<tr>
						<td><a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $p->getCollectionID()?>"><?php echo $p->getCollectionName()?></a></td>
						<td><?php echo $p->getCollectionPath()?></td>
						<td><?php print $packageName; ?></td>
						<td>
							<?php if($cp->canAdmin()) { print $ih->button(t('Refresh'),$this->action('refresh', $p->getCollectionID(), $this->controller->token->generate('refresh')), 'left', false, array('title'=>t('Refreshes the page, rebuilding its permissions and its name.'))); }?>
						</td>
					</tr>
				<?php }

			} ?>
		</table>
		
	</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
