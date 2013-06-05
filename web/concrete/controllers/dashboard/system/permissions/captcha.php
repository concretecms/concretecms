<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model("system/captcha/library");
Loader::model("system/captcha/controller");

class DashboardSystemPermissionsCaptchaController extends Concrete5_Controller_Dashboard_System_Permissions_Captcha {}