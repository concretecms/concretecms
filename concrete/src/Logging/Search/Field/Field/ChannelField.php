<?php

namespace Concrete\Core\Logging\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class ChannelField extends AbstractField
{
    protected $requestVariables = [
        'channel'
    ];

    public function getKey()
    {
        return 'channel';
    }

    public function getDisplayName()
    {
        return t('Channel');
    }

    /**
     * @param LogList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByChannel($this->getData('channel'));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        $channels = ['' => t('All Channels')];
        foreach (Channels::getChannels() as $channel) {
            $channels[$channel] = Channels::getChannelDisplayName($channel);
        }
        return $form->select('channel', $channels, $this->getData('channel'));
    }
}
