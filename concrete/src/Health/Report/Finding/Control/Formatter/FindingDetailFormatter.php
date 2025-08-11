<?php

namespace Concrete\Core\Health\Report\Finding\Control\Formatter;

use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Health\Report\Finding\Control\ButtonControl;
use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use HtmlObject\Element;

class FindingDetailFormatter implements FormatterInterface
{

    /**
     * @param ButtonControl $controls
     * @return Element
     */
    public function getFindingsListElement(ControlInterface $control, Finding $finding): Element
    {
        $token = app('token')->generate('view_finding_details');
        $url = app('url/manager')->resolve([
            '/dashboard/reports/health/details',
            'view_finding_details',
            $finding->getId(),
            $token
        ]);
        $options = json_encode(['title' => t('View Content')]);

        $link = new Element('a', t('View Content'), ['href' => '#',
            'data-launch-modal' => $url,
            'data-modal-options' => $options,
            'class' => 'dropdown-item',
        ]);
        return $link;
    }

}
