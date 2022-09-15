<?php
namespace Concrete\Core\Health\Report\Test\Test\Search;

use Concrete\Core\Block\Block;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;
use Concrete\Core\Health\Search\Traits\SearchContentTrait;

class SurveyBlockTest implements TestInterface
{
    use SearchContentTrait;

    public function run(Runner $report): void
    {
        $this->auditDbal(
            $report,
            'btSurveyOptions',
            'btSurveyOptions.bID as bID',
            ['btSurveyOptions.optionName', 's.question', 's.customMessage'],
            [['btSurvey', 'btSurveyOptions', 's', 's.bID=btSurveyOptions.bID']],
            function(array $result, Runner $report) {
                ['bID' => $id, 'content' => $content] = $result;
                $block = Block::getByID($id);
                if ($block instanceof Block && $block->isActive()) {
                    $this->addBlockWarning($report, $block, $content);
                }
                return null;
            }
        );
    }



}
