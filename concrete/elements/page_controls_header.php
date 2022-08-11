<?php

use Concrete\Core\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Concrete\Core\Notification\Events\MercureService;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$dh = Loader::helper('concrete/dashboard');

if (isset($cp)) {
    if ($cp->canViewToolbar()) {
        ?>

        <script type="text/javascript">
            <?php
            $valt = Loader::helper('validation/token');
            echo "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';\n";

            $mercureService = app(MercureService::class);
            if ($mercureService->isEnabled()) {
                echo "var CCM_SERVER_EVENTS_URL = '" . $mercureService->getPublisherUrl() . "';\n";
            }
            ?>
        </script>

        <?php
        $dh = Loader::helper('concrete/dashboard');
        $v = View::getInstance();
        $request = \Request::getInstance();

        $v->requireAsset('core/cms');

        $editMode = $c->isEditMode();
        $htmlTagClasses = 'ccm-toolbar-visible';

        if ($c->isEditMode()) {
            $startEditMode = 'window.concreteEditMode = new Concrete.EditMode();';
            $htmlTagClasses .= ' ccm-edit-mode';
        } else {
            $startEditMode = '';
        }

        if (!$dh->inDashboard()) {
            $launchPageComposer = '';
            if ($cp->canEditPageContents() && $request->query->get('ccmCheckoutFirst') === '1') {
                $pagetype = $c->getPageTypeObject();
                if (is_object($pagetype) && $pagetype->doesPageTypeLaunchInComposer()) {
                    $launchPageComposer = "$('a[data-launch-panel=page]').toggleClass('ccm-launch-panel-active'); ConcretePanelManager.getByIdentifier('page').show();";
                }
            }
            $panelDashboard = URL::to('/ccm/system/panels/dashboard');
            $panelPage = URL::to('/ccm/system/panels/page');
            $panelSitemap = URL::to('/ccm/system/panels/sitemap');
            $panelHelp = URL::to('/ccm/system/panels/help');
            $panelAdd = URL::to('/ccm/system/panels/add');
            $panelCheckIn = URL::to('/ccm/system/panels/page/check_in');
            $panelRelations = URL::to('/ccm/system/panels/page/relations');

            $js = <<<EOL
<script type="text/javascript">$(function() {
	$('html').addClass('$htmlTagClasses');
	ConcretePanelManager.register({'identifier': 'dashboard', 'position': 'right', url: '{$panelDashboard}'});
	ConcretePanelManager.register({'identifier': 'page', url: '{$panelPage}'});
	ConcretePanelManager.register({'identifier': 'sitemap', 'position': 'right', url: '{$panelSitemap}'});
    ConcretePanelManager.register({'identifier': 'help', 'position': 'right', url: '{$panelHelp}'});
	ConcretePanelManager.register({'identifier': 'page_relations', 'position': 'right', url: '{$panelRelations}'});
	ConcretePanelManager.register({'identifier': 'add-block', 'translucent': false, 'position': 'left', url: '{$panelAdd}', pinable: true});
	ConcretePanelManager.register({'identifier': 'check-in', 'position': 'left', url: '{$panelCheckIn}'});
	ConcreteToolbar.start();
	{$startEditMode}
	{$launchPageComposer}
});
</script>

EOL;
        } else {
            $js = <<<EOL
<script type="text/javascript">$(function() {
	$('html').addClass('ccm-toolbar-visible');
	{$startEditMode}
});
</script>

EOL;
        }
        $v->addFooterItem($js);
        $cih = Loader::helper('concrete/ui');
        if (Localization::activeLanguage() != 'en') {
            $v->addFooterItem(
                '<script type="text/javascript">$(function() { jQuery.datepicker.setDefaults({dateFormat: \'yy-mm-dd\'}); });</script>'
            );
        }
        if (Config::get('concrete.messenger.consume.method') === 'app') {
            $transportManager = Core::make(\Concrete\Core\Messenger\Transport\TransportManager::class);
            $transport = $transportManager->getReceivers()->get(TransportInterface::DEFAULT_ASYNC);
            if ($transport instanceof MessageCountAwareInterface && $transport->getMessageCount() > 0) {
                $v->addFooterItem(
                    '<script type="text/javascript">$(function() { ConcreteQueueConsumer.consume(\'' . $valt->generate('consume_messages') . '\') });</script>'
                );
            }
        }
    }
}
