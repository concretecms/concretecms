<?
defined('C5_EXECUTE') or die("Access Denied.");
session_write_close();

Loader::model('system_notification');
// latest version, including addon updates
Loader::library('update');
$lv = Update::getLatestAvailableVersionNumber();