<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal;

use Concrete\Core\View\View;

class IntroductionModal extends AbstractBasicModal
{

    public function getTitle(): string
    {
        return t('Take a few minutes to learn the basics');
    }

    public function getBody(): string
    {
        ob_start();
        View::element('help/introduction');
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
}
