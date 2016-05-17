<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Logging\Query\Logger;
use Concrete\Core\Logging\Query\LogList;
use Concrete\Core\Page\Controller\DashboardPageController;

class QueryLog extends DashboardPageController
{
    public function view()
    {
        $l = new LogList();
        $pagination = $l->getPagination();
        $pagination->setMaxPerPage(10);
        $entries = $pagination->getCurrentPageResults();

        $this->set('list', $l);
        $this->set('entries', $entries);
        $this->set('total', Logger::getTotalLogged());
        $this->set('pagination', $pagination);
    }

    public function inspect($query)
    {
        $this->set('query', h($query));
        $this->set('parameters', Logger::getParametersForQuery($query));
        $this->setThemeViewTemplate('dialog.php');
    }

    public function clear()
    {
        if (Loader::helper('validation/token')->validate('clear')) {
            $l = new Logger();
            $l->clearQueryLog();
            $this->redirect('/dashboard/system/optimization/query_log', 'cleared');
        }
    }

    public function cleared()
    {
        $this->set('message', t('Database query log cleared.'));
        $this->view();
    }

    public function csv()
    {
        if (!Loader::helper('validation/token')->validate('csv')) {
            $this->redirect('/dashboard/system/optimization/query_log');
        } else {
            $l = new LogList();
            $entries = $l->get(0);
            $fileName = "Database Query Log Results";

            header("Content-Type: text/csv");
            header("Cache-control: private");
            header("Pragma: public");
            $date = date('Ymd');
            header("Content-Disposition: attachment; filename=" . $fileName . "_form_data_{$date}.csv");

            $fp = fopen('php://output', 'w');

            // write the columns
            $row = array(
                t('Times Run'),
                t('Query'),
            );

            fputcsv($fp, $row);

            foreach ($entries as $ent) {
                fputcsv($fp, array(
                    $ent['queryTotal'],
                    $ent['query'],
                ));
            }

            fclose($fp);
            die;
        }
    }
}
