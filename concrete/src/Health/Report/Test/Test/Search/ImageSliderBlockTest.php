<?php
namespace Concrete\Core\Health\Report\Test\Test\Search;

use Concrete\Core\Block\Block;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;
use Concrete\Core\Health\Search\Traits\SearchContentTrait;

class ImageSliderBlockTest implements TestInterface
{
    use SearchContentTrait;

    public function run(Runner $report): void
    {
        $this->auditDbal(
            $report,
            'btImageSlider',
            'btImageSlider.bID as bID',
            ['btImageSliderEntries.linkURL', 'btImageSliderEntries.title', 'btImageSliderEntries.description'],
            [['btImageSliderEntries', 'btImageSlider', 'btImageSliderEntries', 'btImageSlider.bID=btImageSliderEntries.bID']],
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
