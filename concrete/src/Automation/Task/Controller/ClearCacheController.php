<?php
namespace Concrete\Core\Automation\Task\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

class ClearCacheController extends AbstractController
{

    public function getName(): string
    {
        return t('Clear Cache');
    }

    public function getDescription(): string
    {
        return t('Clears all caches.');
    }
}
