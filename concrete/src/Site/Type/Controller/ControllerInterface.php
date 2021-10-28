<?php

namespace Concrete\Core\Site\Type\Controller;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Site\Type\Formatter\FormatterInterface;
use Symfony\Component\HttpFoundation\Request;

interface ControllerInterface
{
    public function add(Site $site, Request $request): Site;

    public function update(Site $site, Request $request): Site;

    public function delete(Site $site): bool;

    public function addType(Type $type): Type;

    public function getFormatter(Type $type): FormatterInterface;
}
