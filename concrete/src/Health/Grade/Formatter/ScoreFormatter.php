<?php
namespace Concrete\Core\Health\Grade\Formatter;

use Concrete\Core\Health\Grade\ScoreGrade;
use HtmlObject\Element as HtmlElement;
use Concrete\Core\Filesystem\Element;

class ScoreFormatter implements FormatterInterface
{

    /**
     * @var ScoreGrade
     */
    protected $grade;

    public function __construct(ScoreGrade $grade)
    {
        $this->grade = $grade;
    }

    public function getBannerElement(): Element
    {
        return new Element('dashboard/health/grade/score', ['grade' => $this->grade]);
    }

    public function getResultsListIcon(): HtmlElement
    {
        if ($this->grade->getScore() >= 80) {
            return new HtmlElement('i', '', ['class' => 'fa fa-thumbs-up text-success']);
        } else if ($this->grade->getScore() >= 60) {
            return new HtmlElement('i', '', ['class' => 'fa fa-exclamation-circle text-warning']);
        } else {
            return new HtmlElement('i', '', ['class' => 'fa fa-thumbs-down text-danger']);
        }
    }

}
