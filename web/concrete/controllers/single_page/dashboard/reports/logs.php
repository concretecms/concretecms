<?

namespace Concrete\Controller\SinglePage\Dashboard\Reports;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Logging\LogList;
use Log;
use Core;
use Request;

class Logs extends DashboardPageController {

    public function clear($token = '', $channel = false) {
        $valt = Core::make('helper/validation/token');
        if ($valt->validate('', $token)) {
            if (!$channel) {
                Log::clearAll();
            } else {
                Log::clearByChannel($channel);
            }
            $this->redirect('/dashboard/reports/logs');
        } else {
            $this->redirect('/dashboard/reports/logs');
        }
    }

	public function view($page = 0) {
		$list = new LogList();

        $levels = array();
        foreach(Log::getLevels() as $level) {
            $levels[$level] = ucfirst(strtolower(Log::getLevelName($level)));
        }
        $this->set('levels', $levels);
        $channels = array('' => t('All Channels'));
        foreach(Log::getChannels() as $channel) {
            $channels[$channel] = Core::make('helper/text')->unhandle($channel);
        }
        $r = Request::getInstance();
        if ($r->query->has('channel') && $r->query->get('channel') != '') {
            $list->filterByChannel($r->query->get('channel'));
        }
        if ($r->query->has('keywords') && $r->query->get('keywords') != '') {
            $list->filterByKeywords($r->query->get('keywords'));
        }

        $entries = $list->getPage();
        $this->set('list', $list);
        $this->set('entries', $entries);

        $this->set('levels', $levels);
        $this->set('channels', $channels);

       }
	
}