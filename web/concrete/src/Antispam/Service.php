<?php
namespace Concrete\Core\Antispam;

use Config;
use Core;
use Group;
use Loader;
use Log;
use Page;
use User;
use UserInfo;

class Service
{

    /**
     * @var bool|mixed
     */
    protected $controller = false;

    public function __construct()
    {

        $library = Library::getActive();
        if (is_object($library)) {
            $this->controller = $library->getController();
        }
    }

    /**
     * @return Group|null
     */
    public function getWhitelistGroup()
    {
        return Group::getByID(Config::get('concrete.spam.whitelist_group'));
    }

    /**
     * Report some content with the poster's information to the AntiSpam service
     * @param string $content
     * @param UserInfo $ui
     * @param string $ip
     * @param string $ua
     * @param array $additionalArgs
     */
    public function report($content, $author, $email, $ip, $ua, $additionalArgs = array())
    {
        $args['content'] = $content;
        $args['author'] = $author;
        $args['author_email'] = $email;
        $args['ip_address'] = $ip;
        $args['user_agent'] = $ua;

        foreach ($additionalArgs as $key => $value) {
            $args[$key] = $value;
        }
        if (method_exists($this->controller, 'report')) {
            $this->controller->report($args);
        }

        $u = new User();
        \Log::info(t('Content %s (author %s, %s) flagged as spam by user %s',
            $content,
            $author,
            $email,
            $u->getUserName()
        ));
    }

    /**
     * @param string $content
     * @param string $type
     * @param array $additionalArgs
     * @param bool $user
     * @return bool
     * @throws \Exception
     */
    public function check($content, $type, $additionalArgs = array(), $user = false)
    {
        if ($this->controller) {
            if (!$user) {
                $user = new User;
            }
            $wlg = $this->getWhitelistGroup();
            if ($wlg instanceof Group && $user->inGroup($wlg)) {
                // Never spam if user is in the whitelist
                return true;
            }

            /** @var \Concrete\Core\Permission\IPService $iph */
            $iph = Core::make('helper/validation/ip');
            $ip = $iph->getRequestIP();
            $args['ip_address'] = ($ip === false)?(''):($ip->getIp($ip::FORMAT_IP_STRING));
            $args['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $args['content'] = $content;
            foreach ($additionalArgs as $key => $value) {
                $args[$key] = $value;
            }
            if (isset($args['user']) && is_object($args['user'])) {
                $u = $args['user'];
            } else {
                $u = new User();
            }
            if (!isset($args['email']) && $u->isRegistered()) {
                $ui = UserInfo::getByID($u->getUserID());
                $args['email'] = $ui->getUserEmail();
            }
            $r = $this->controller->check($args);
            if ($r) {
                return true;
            } else {
                $logText = '';
                $c = Page::getCurrentPage();
                if (is_object($c)) {
                    $logText .= t('URL: %s', Loader::helper('navigation')->getLinkToCollection($c, true));
                    $logText .= "\n";
                }
                if ($u->isRegistered()) {
                    $logText .= t('User: %s (ID %s)', $u->getUserName(), $u->getUserID());
                    $logText .= "\n";
                }
                $logText .= t('Type: %s', Loader::helper('text')->unhandle($type));
                $logText .= "\n";
                foreach ($args as $key => $value) {
                    $logText .= Loader::helper('text')->unhandle($key) . ': ' . $value . "\n";
                }

                if (Config::get('concrete.log.spam')) {
                    Log::addEntry($logText, t('spam'));
                }
                if (Config::get('concrete.spam.notify_email') != '') {
                    $mh = Loader::helper('mail');
                    $mh->to(Config::get('concrete.spam.notify_email'));
                    $mh->addParameter('content', $logText);
                    $mh->addParameter('siteName', tc('SiteName', Config::get('concrete.site')));
                    $mh->load('spam_detected');
                    $mh->sendMail();
                }
                return false;
            }
        } else {
            return true; // return true if it passes the test
        }
    }

    /**
     * @param $nm
     * @param $args
     * @return mixed
     */
    public function __call($nm, $args)
    {
        if (method_exists($this->controller, $nm)) {
            return call_user_func_array(array($this->controller, $nm), $args);
        }
    }

}
