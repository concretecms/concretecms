<?php
namespace Concrete\Core\Command\Process\Menu\Item;

use Concrete\Core\Application\UserInterface\Menu\Item\Controller;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Concrete\Core\Messenger\Transport\TransportManager;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
use HtmlObject\Element;
use HtmlObject\Link;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

class RunningProcessesController extends Controller
{

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(Repository $config, Token $token, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->token = $token;
        $this->entityManager = $entityManager;
    }

    /**
     * Determine whether item should be displayed
     *
     * @return bool
     */
    public function displayItem()
    {
        $processes = $this->entityManager->getRepository(Process::class)->findRunning();
        if (count($processes) > 0) {
            return true;
        }
        return false;
    }


    public function getMenuItemLinkElement()
    {
        $token = $this->token->generate('view_activity');
        $link = new Link('#', '');
        $link->setAttribute('onclick', "ConcreteEvent.publish('TaskActivityWindowShow', {'token': '$token'})");
        $link->setAttribute('title', t('Active Processes'));
        $show_tooltips = (bool) \Config::get('concrete.accessibility.toolbar_tooltips');
        if ($show_tooltips) {
            $link->setAttribute('class', 'launch-tooltip');
            $link->setAttribute('data-toggle', 'tooltip');
            $link->setAttribute('data-placement', 'bottom');
            $link->setAttribute('data-delay', '{ "show": 500, "hide": 0 }');
        }

        $icon = new Element('i', '');
        $icon->addClass('fas fa-tasks');

        $accessibility = new Element('span', t('Active Processes'));
        $accessibility->addClass('ccm-toolbar-accessibility-title');

        $link->appendChild($icon);
        $link->appendChild($accessibility);

        return $link;
    }
}
