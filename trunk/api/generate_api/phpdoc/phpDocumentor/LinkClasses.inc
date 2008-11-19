<?php
/**
 * Linking to element documentation is performed by the classes in this file.
 *
 * abstractLink descendants contain enough information to differentiate every
 * documentable element, and so can be converted to a link string by
 * {@link Converter::returnSee()}
 * 
 * phpDocumentor :: automatic documentation generator
 * 
 * PHP versions 4 and 5
 *
 * Copyright (c) 2002-2008 Gregory Beaver
 * 
 * LICENSE:
 * 
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    CVS: $Id: LinkClasses.inc,v 1.5 2008/02/24 02:35:44 ashnazg Exp $
 * @filesource
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2.0
 * @todo       CS cleanup - change package to PhpDocumentor
 */

/**
 * linking classes parent
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class abstractLink
{
    /**#@+
     * @var string
     */
    var $path;
    /**
     * phpdoc alias _phpdoc_inc for phpdoc.inc
     */
    var $fileAlias = '';
    /**
     * element type linked to.
     * can only be a documentable element
     */
    var $type = '';
    var $name = '';
    var $category = '';
    var $package = '';
    var $subpackage = '';
    /**#@-*/

    /**
     * sets up the link
     *
     * @param string $path       full path to file containing element
     * @param string $fileAlias  page name, as configured by {@link Parser::parse}
     * @param string $name       element name
     * @param string $package    package element is in
     * @param string $subpackage subpackage element is in
     * @param string $category   optional category that documentation is in
     *
     * @return void
     */
    function addLink($path, $fileAlias, $name, $package, $subpackage,
        $category = false)
    {
        $this->path       = $path;
        $this->fileAlias  = $fileAlias;
        $this->name       = $name;
        $this->category   = $category;
        $this->package    = $package;
        $this->subpackage = $subpackage;
    }
}

/**
 * procedural page link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class pageLink extends abstractLink
{
    /**
     * @var string
     */
    var $type = 'page';
}

/**
 * function link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class functionLink extends abstractLink
{
    /** 
     * @var string
     */
    var $type = 'function';
}

/**
 * define link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class defineLink extends abstractLink
{
    /** 
     * @var string
     */
    var $type = 'define';
}

/**
 * global variable link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class globalLink extends abstractLink
{
    /**
     * @var string
     */
    var $type = 'global';
}

/**
 * class link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class classLink extends abstractLink
{
    /**
     * @var string
     */
    var $type = 'class';
}

/**
 * method link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class methodLink extends abstractLink
{
    /**
     * @var string
     */
    var $type = 'method';
    /**
     * @var string
     */
    var $class = '';
    
    /**
     * sets up the link
     *
     * @param string $class      class name
     * @param string $path       full path to file containing element
     * @param string $fileAlias  page name, as configured by {@link Parser::parse}
     * @param string $name       element name
     * @param string $package    package element is in
     * @param string $subpackage subpackage element is in
     * @param string $category   optional category that documentation is in
     *
     * @return void
     */
    function addLink($class, $path , $fileAlias, $name, $package, $subpackage, 
        $category = false)
    {
        $this->class = $class;
        abstractLink::addLink($path, $fileAlias, $name, $package, $subpackage, 
            $category);
    }
}

/**
 * class variable link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class varLink extends methodLink
{
    /**
     * @var string
     */
    var $type = 'var';
}

/**
 * class constant link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class constLink extends methodLink
{
    /**
     * @var string
     */
    var $type = 'const';
}

/**
 * tutorial link
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage Links
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2002-2008 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - change classname to PhpDocumentor_*
 */
class tutorialLink extends abstractLink
{
    /**#@+
     * @var string
     */
    var $type    = 'tutorial';
    var $section = '';
    var $title   = false;
    /**#@-*/
    
    /**
     * sets up the link
     *
     * @param string $section    section/subsection name
     * @param string $path       full path to file containing element
     * @param string $name       page name, as configured by {@link Parser::parse}
     * @param string $package    package element is in
     * @param string $subpackage subpackage element is in
     * @param string $title      title of tutorial
     * @param string $category   optional category that documentation is in
     *
     * @return void
     */
    function addLink($section, $path, $name, $package, $subpackage, $title = false, 
        $category = false)
    {
        $this->section = $section;
        $this->title   = $title;
        parent::addLink($path, '', $name, $package, $subpackage, $category);
    }
}
?>
