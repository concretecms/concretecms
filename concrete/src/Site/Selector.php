<?php
namespace Concrete\Core\Site;

use Concrete\Core\Entity\Site\Site;
use Symfony\Component\HttpFoundation\Session\Session;

class Selector
{

    protected $session;

    public function __construct(
    Session $session
    ) {
        $this->session = $session;
    }

    public function saveSiteToSession(Site $site)
    {
        $this->session->set('active_site', $site->getSiteID());
    }

    public function getSiteIDFromSession()
    {
        return $this->session->get('active_site');
    }

}
