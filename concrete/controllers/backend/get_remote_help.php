<?php
namespace Concrete\Controller\Backend;

class GetRemoteHelp extends UserInterface
{
    protected function canAccess()
    {
        return true;
    }

    public function view()
    {
        session_write_close();

        $config = \Core::make('config');
        $r = json_encode(array());
        if ($config->get('concrete.external.intelligent_search_help')) {
            if ($_REQUEST['q']) {
                $url = \Config::get('concrete.urls.help.remote_search');
                $r = \Core::make("helper/file")->getContents($url . '?q=' . $_REQUEST['q']);
                if (!$r) {
                    $r = json_encode(array());
                }
            }
        }

        echo $r;

        \Core::shutdown(array('jobs' => true));
    }
}
