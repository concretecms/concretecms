<?php
namespace Concrete\Core\Site\Type\Controller;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Site\Type\Formatter\FormatterInterface;
use Symfony\Component\HttpFoundation\Request;

interface ControllerInterface
{
    /**
     * @param Site $site
     * @param Request $request
     * @return Site
     */

    public function add(Site $site, Request $request);
    public function update(Site $site, Request $request);
    public function delete(Site $site);
    public function addType(Type $type);

    /**
     * @return FormatterInterface
     */
    public function getFormatter(Type $type);

}
