<?php
namespace Concrete\Core\Health\Grade\Formatter;

use Concrete\Core\Health\Grade\PassFailGrade;
use HtmlObject\Element as HtmlElement;
use Concrete\Core\Filesystem\Element;

class PassFailFormatter implements FormatterInterface
{

    /**
     * @var PassFailGrade
     */
    protected $grade;

    public function __construct(PassFailGrade $grade)
    {
        $this->grade = $grade;
    }

    public function getBannerElement(): Element
    {
        return new Element('dashboard/health/grade/pass_fail', ['grade' => $this->grade]);
    }

    public function getResultsListIcon(): HtmlElement
    {
        if ($this->grade->hasPassed()) {
            return new HtmlElement('i', '', ['class' => 'fa fa-thumbs-up text-success']);
        } else {
            return new HtmlElement('i', '', ['class' => 'fa fa-thumbs-down text-danger']);
        }
    }

}
