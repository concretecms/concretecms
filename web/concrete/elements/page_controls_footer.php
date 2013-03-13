<?
defined('C5_EXECUTE') or die("Access Denied.");
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();

if (isset($cp) && $cp->canViewToolbar() && (!$dh->inDashboard())) { ?>

	<div id="ccm-page-controls-wrapper" class="ccm-ui">
		<div id="ccm-toolbar">
			<ul>
				<li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></span></li>
				<li class="ccm-toolbar-page-edit pull-left"><a href="<? if (!$c->isEditMode()) { ?><?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?><? } else { ?>javascript:void(0);<? } ?>"><i class="glyphicon glyphicon-pencil"></i></a></li>
				<li class="ccm-toolbar-page-settings pull-left"><a href=""><i class="glyphicon glyphicon-cog"></i></a></li>

				<li class="ccm-toolbar-account pull-right"><a href="#" data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="#ccm-toolbar-menu-user"><i class="glyphicon glyphicon-user"></i></a>
				
				<ul id="ccm-toolbar-menu-user" class="ccm-toolbar-hover-menu dropdown-menu">
				  <li><a href="<?=$this->url('/account')?>"><?=t('Account')?></a></li>
				  <li><a href="<?=$this->url('/account/messages/inbox')?>"><?=t('Inbox')?></a></li>
				  <li><a href="<?=$this->url('/login', 'logout')?>">Sign Out</a></li>
				</ul>

				</li>
				<? if ($dh->canRead()) { ?>
					<li class="ccm-toolbar-dashboard pull-right"><a href="<?=$this->url('/dashboard')?>" data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="#ccm-toolbar-menu-dashboard"><i class="glyphicon glyphicon-briefcase"></i></a></li>
				<? } ?>
				<li class="ccm-toolbar-search pull-right"><i class="glyphicon glyphicon-search"></i> <input type="text" id="ccm-nav-intelligent-search" tabindex="1" /></li>
				<? if ($c->isEditMode() && $cp->canEditPageContents()) { ?>
					<li class="ccm-toolbar-add pull-right"><a class="dialog-launch" title="<?=t('Add Block')?>" dialog-width="660" dialog-height="280" dialog-modal="false" dialog-title="<?=t('Add Block')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/add_block?cID=<?=$c->getCollectionID()?>"><i class="glyphicon glyphicon-plus"></i></a></li>
				<? } ?>

			</ul>

		</div>
	</div>


<? }