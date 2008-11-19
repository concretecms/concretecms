<?php
/**
 * All abstract representations of html tags in DocBlocks are handled by the
 * classes in this file
 *
 * Before version 1.2, phpDocumentor simply passed html to converters, without
 * much thought, except the {@link adv_htmlentities()} function was provided
 * along with a list of allowed html.  That list is no longer used, in favor
 * of these classes.
 *
 * The PDF Converter output looked wretched in version 1.1.0 because line breaks
 * in DocBlocks were honored.  This meant that output often had just a few words
 * on every other line!  To fix this problem, DocBlock descriptions are now
 * parsed using the {@link ParserDescParser}, and split into paragraphs.  In
 * addition, html in DocBlocks are parsed into these objects to allow for easy
 * conversion in destination converters.  This design also allows different
 * conversion for different templates within a converter, which separates
 * design from logic almost 100%
 *
 * phpDocumentor :: automatic documentation generator
 * 
 * PHP versions 4 and 5
 *
 * Copyright (c) 2002-2007 Gregory Beaver
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
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    CVS: $Id: DescHTML.inc,v 1.5 2007/11/17 03:07:00 ashnazg Exp $
 * @filesource
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @see        parserDocBlock, parserInclude, parserPage, parserClass
 * @see        parserDefine, parserFunction, parserMethod, parserVar
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 */
/**
 * Used for <<code>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserCode
 */
class parserCode extends parserStringWithInlineTags
{
    /**
     * performs the conversion of code tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted code block
     * @uses Converter::ProgramExample()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        if (!isset($this->value[0])) {
            return '';
        }
        if (is_string($this->value[0]) && $this->value[0]{0} == "\n") {
            $this->value[0] = substr($this->value[0], 1);
        }
        $linktags = array();
        foreach ($this->value as $val) {
            if (phpDocumentor_get_class($val) == 'parserlinkinlinetag'
                || phpDocumentor_get_class($val) == 'parsertutorialinlinetag'
            ) {
                $linktags[] = array(
                    $c->postProcess($val->Convert($c, false, false)), $val);
            }
        }
        $a = $c->ProgramExample(rtrim(ltrim(parent::Convert($c,
            false, false), "\n\r")));
        foreach ($linktags as $tag) {
            $a = str_replace($tag[0], $tag[1]->Convert($c, false, false), $a);
        }
        return $a;
    }
}

/**
 * Used for <<pre>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserPre
 */
class parserPre extends parserStringWithInlineTags
{
    /**
     * performs the conversion of code tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::PreserveWhiteSpace()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        return $c->PreserveWhiteSpace(rtrim(ltrim(parent::Convert($c,
            false, false), "\n\r")));
    }
}

/**
 * Used for <<b>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserB
 */
class parserB extends parserStringWithInlineTags
{
    /**
     * performs the conversion of bold tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::Bolden()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        return $c->Bolden(parent::Convert($c));
    }
}

/**
 * Used for <<i>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserI
 */
class parserI extends parserStringWithInlineTags
{
    /**
     * performs the conversion of italic tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::Italicize()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        return $c->Italicize(parent::Convert($c));
    }
}

/**
 * Used for <<var>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserDescVar
 */
class parserDescVar extends parserStringWithInlineTags
{
    /**
     * performs the conversion of variable tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::Varize()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        return $c->Varize(parent::Convert($c));
    }
}

/**
 * Used for <<samp>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserSamp
 */
class parserSamp extends parserStringWithInlineTags
{
    /**
     * performs the conversion of sample tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::Sampize()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        return $c->Sampize(parent::Convert($c));
    }
}

/**
 * Used for <<kbd>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserKbd
 */
class parserKbd extends parserStringWithInlineTags
{
    /**
     * performs the conversion of keyboard tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::Kbdize()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        return $c->Kbdize(parent::Convert($c));
    }
}

/**
 * Used for <<br>> in a description
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserBr
 */
class parserBr extends parserStringWithInlineTags
{
    /**
     * performs the conversion of linebreak tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::Br()
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        return $c->Br($this->getString());
    }
}

/**
 * Used for lists <<ol>> and <<ul>>
 *
 * @category   ToolsAndUtilities
 * @package    phpDocumentor
 * @subpackage DescHTML
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2002-2007 Gregory Beaver
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    Release: 1.4.2
 * @link       http://www.phpdoc.org
 * @link       http://pear.php.net/PhpDocumentor
 * @since      1.2
 * @todo       CS cleanup - change package to PhpDocumentor
 * @todo       CS cleanup - rename class to ParserList
 */
class parserList extends parserStringWithInlineTags
{
    /**
     * @var boolean
     */
    var $numbered;
    /**
     * @var integer
     */
    var $items = 0;
    /**
     * Constructor - create a new list
     *
     * @param integer $numbered a reference number for the new list
     */
    function parserList($numbered)
    {
        $this->numbered = $numbered;
    }
    
    /**
     * add an item to a list
     *
     * @param parserStringWithInlineTags $item the item to add
     *
     * @return void
     */
    function addItem($item)
    {
        $this->value[$this->items++] = $item;
    }
    
    /**
     * add a list
     *
     * @param parserList $list the list to add
     *
     * @return void
     */
    function addList($list)
    {
        $this->value[$this->items++] = $list;
    }
    
    /**
     * performs the conversion of list tags
     *
     * @param Converter &$c the converter object
     *
     * @return string the converted pre block
     * @uses Converter::ListItem() enclose each item of the list
     * @uses Converter::EncloseList() enclose the list
     * @todo CS cleanup - rename method to convert()
     */
    function Convert(&$c)
    {
        $list = '';
        foreach ($this->value as $item) {
            $list .= $c->ListItem(trim($item->Convert($c)));
        }
        return $c->EncloseList($list, $this->numbered);
    }
}

?>
