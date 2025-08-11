<?php
namespace Concrete\Core\Health\Report\Test\Test\Search;

use Concrete\Core\Block\Block;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;
use Concrete\Core\Health\Search\Traits\SearchContentTrait;

class GalleryBlockTest implements TestInterface
{
    use SearchContentTrait;

    public function run(Runner $runner): void
    {
        // Check survey options
        $this->auditDbal(
            $runner,
            'btGalleryEntryDisplayChoices',
            'btGalleryEntryDisplayChoices.bID as bID',
            ['btGalleryEntryDisplayChoices.value', 'btGalleryEntryDisplayChoices.dcKey'],
            [],
            function ($result, $report) { $this->createFinding($result, $report); }
        );
    }

    protected function createFinding(array $result, Runner $report)
    {
        ['bID' => $id, 'content' => $content] = $result;
        $block = Block::getByID($id);

        if ($block instanceof Block && $block->isActive()) {
            $this->addBlockWarning($report, $block, $content);
        }

        return null;
    }



}
