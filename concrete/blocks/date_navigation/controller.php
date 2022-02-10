<?php

namespace Concrete\Block\DateNavigation;

defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 400;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 450;

    /**
     * @var string[]
     */
    protected $btExportPageColumns = ['cParentID', 'cTargetID'];

    /**
     * @var string[]
     */
    protected $btExportPageTypeColumns = ['ptID'];

    /**
     * @var string
     */
    protected $btTable = 'btDateNavigation';

    /**
     * @var int|null
     */
    protected $cTargetID;

    /**
     * @var int|null
     */
    protected $ptID;

    /**
     * @var string|int|null
     */
    protected $selectedYear;

    /**
     * @var string|int|null
     */
    protected $selectedMonth;

    /**
     * @var int|null
     */
    protected $cParentID;

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::NAVIGATION,
        ];
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays a list of months to filter a page list by.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Date Navigation');
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->edit();
        $this->set('maxResults', 3);
        $this->set('title', t('Archives'));
        $this->set('titleFormat', 'h5');
    }

    /**
     * @return void
     */
    public function edit()
    {
        $types = Type::getList();
        $this->set('pagetypes', $types);
    }

    /**
     * @param array<string,mixed>$dateArray
     *
     * @return string
     */
    public function getDateLink($dateArray = null)
    {
        if ($this->cTargetID) {
            $c = Page::getByID($this->cTargetID);
        } else {
            $c = Page::getCurrentPage();
        }

        if ($dateArray) {
            $resolve = [$c, $dateArray['year'], $dateArray['month']];
        } else {
            $resolve = [$c];
        }

        return  $this->app->make(ResolverManagerInterface::class)->resolve($resolve);
    }

    /**
     * @param array<string,mixed> $dateArray
     *
     * @throws \Punic\Exception
     * @throws \Punic\Exception\BadArgumentType
     * @throws \Punic\Exception\ValueNotInList
     *
     * @return string
     */
    public function getDateLabel($dateArray)
    {
        return \Punic\Calendar::getMonthName($dateArray['month'], 'wide', '', true) . ' ' . $dateArray['year'];
    }

    /**
     * @param array<int,mixed> $parameters
     *
     * @return mixed[]
     */
    public function getPassThruActionAndParameters($parameters)
    {
        if (app('helper/validation/numbers')->integer($parameters[0])) {
            // then we're going to treat this as a year.
            $method = 'action_filter_by_date';
            $parameters[0] = (int) $parameters[0];
            if (isset($parameters[1])) {
                $parameters[1] = (int) $parameters[1];
            }
        } else {
            $parameters = $method = null;
        }

        return [$method, $parameters];
    }

    /**
     * @param int|bool|null $year
     * @param int|bool|null $month
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public function action_filter_by_date($year = false, $month = false)
    {
        $this->selectedYear = $year;
        $this->selectedMonth = $month;
        $this->view();
    }

    /**
     * @param array<string, mixed> $dateArray
     *
     * @return bool
     */
    public function isSelectedDate($dateArray)
    {
        if (isset($this->selectedYear, $this->selectedMonth)) {
            return $dateArray['year'] == $this->selectedYear && $dateArray['month'] == $this->selectedMonth;
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public function view()
    {
        $pl = new PageList();
        if ($this->ptID) {
            $pl->filterByPageTypeID($this->ptID);
        }
        if ($this->cParentID) {
            $pl->filterByParentID($this->cParentID);
        }
        $query = $pl->deliverQueryObject();
        $query->select('date_format(cv.cvDatePublic, "%Y") as navYear, date_format(cv.cvDatePublic, "%m") as navMonth');
        $query->groupBy('navYear, navMonth');
        $query->orderBy('navYear', 'desc')->addOrderBy('navMonth', 'desc');
        $r = $query->execute();
        $dates = [];
        $results = $r->fetchAllAssociative();
        foreach ($results as $row) {
            $dates[] = ['year' => $row['navYear'], 'month' => $row['navMonth']];
        }
        $this->set('dates', $dates);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return void
     */
    public function save($data)
    {
        $data += [
            'redirectToResults' => 0,
            'cTargetID' => 0,
            'filterByParent' => 0,
            'cParentID' => 0,
            'ptID' => 0,
        ];
        if ($data['redirectToResults']) {
            $data['cTargetID'] = (int) ($data['cTargetID']);
        } else {
            $data['cTargetID'] = 0;
        }
        if ($data['filterByParent']) {
            $data['cParentID'] = (int) ($data['cParentID']);
        } else {
            $data['cParentID'] = 0;
        }
        $data['ptID'] = (int) ($data['ptID']);
        parent::save($data);
    }
}
