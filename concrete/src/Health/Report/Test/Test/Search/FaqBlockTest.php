<?php
namespace Concrete\Core\Health\Report\Test\Test\Search;

use Concrete\Core\Block\Block;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;
use Concrete\Core\Health\Search\Traits\SearchContentTrait;

class FaqBlockTest implements TestInterface
{
    use SearchContentTrait;

    public function run(Runner $report): void
    {
        $this->auditDbal(
            $report,
            'btFaq',
            'btFaq.bID as bID',
            ['btFaq.blockTitle', 'e.linkTitle', 'e.title', 'e.description'],
            [['btFaqEntries', 'btFaq', 'e', 'btFaq.bID=e.bID']],
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
    }


}
