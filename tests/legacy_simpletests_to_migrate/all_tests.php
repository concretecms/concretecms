<?php

define('SIMPLE_TEST', '../../simpletest/');
require_once SIMPLE_TEST . 'unit_tester.php';
require_once SIMPLE_TEST . 'web_tester.php';
require_once '../../phpQuery/phpQuery.php';

define('C5_ENVIRONMENT_ONLY', true);
define('DIR_BASE', dirname(__FILE__) . '/..');
require '../concrete/dispatcher.php';
require 'testing_base.php';

$c = Page::getByID(1);
$cp = new Permissions($c);
$a = Area::get($c, 'Main');
$ap = new Permissions($a);

class ShowPasses extends HtmlReporter
{
    public function ShowPasses()
    {
        $this->HtmlReporter();
    }

    public function paintPass($message)
    {
        parent::paintPass($message);
        echo "<span class=\"pass\">Pass</span>: ";
        echo " $message<br />\n";
    }
}

$t = new TestSuite('All Tests');
$t->addFile($_SERVER['DOCUMENT_ROOT'] . '/web/tests/template_tests.php');
$t->addFile($_SERVER['DOCUMENT_ROOT'] . '/web/tests/block_override_tests.php');
$t->run(new ShowPasses());
