<?
defined('C5_EXECUTE') or die("Access Denied.");
//not working? try $_GET['force']=1
$cnt = Loader::controller('/upgrade');
$cnt->on_start();
$cnt->view();
$v = $cnt->getViewObject();
$r = new Response($v->render());
$r->send();