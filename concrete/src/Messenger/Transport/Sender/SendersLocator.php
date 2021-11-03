<?php
namespace Concrete\Core\Messenger\Transport\Sender;

use Concrete\Core\Messenger\Stamp\SkipSendersStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator as SymfonySendersLocator;

class SendersLocator extends SymfonySendersLocator
{

    public function getSenders(Envelope $envelope): iterable
    {
        $skipSenders = $envelope->last(SkipSendersStamp::class);
        if ($skipSenders instanceof SkipSendersStamp) {
            return [];
        }
        return parent::getSenders($envelope);
    }


}