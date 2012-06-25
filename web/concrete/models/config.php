<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Contains the config class.
 * @package Utilities 
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * The config object holds global site-wide values for specific settings, allowing them to easily be changed
 * without having to visit a PHP configuration file.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class ConfigValue extends Concrete5_Model_ConfigValue {}
class Config extends Concrete5_Model_Config {}
class ConfigStore extends Concrete5_Model_ConfigStore {}