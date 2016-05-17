<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class PanelManager extends AbstractManager
{
    public function __construct()
    {
        $this->registerMessages(array(
            '/page/composer' => t('Use the form below to create your page. You can also preview your page in edit mode at any time.'),
            '/page/attributes' => t('Manage the page attributes. To associate an attribute to the page click it in the left panel.'),
            '/page/caching' => t('Full page caching can dramatically improve page speed for pages that don\'t need to have absolutely up-to-the-minute content.'),
        ));

        $m = new Message();
        $m->setMessageContent(t('Define where this page lives on your website. View and delegate what other pages are redirecting to this page.'));
        $m->setIdentifier('/page/location');
        $m->addGuide('location-panel');
        $this->messages['/page/location'] = $m;
    }
}
