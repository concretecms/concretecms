<?php
/**
 * Intermediate procedural page parsing structure.
 * This structure parses defines, functions, and global variables by file,
 * and then iterates over the elements to document conflicts.
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
 * @category  ToolsAndUtilities
 * @package   phpDocumentor
 * @author    Gregory Beaver <cellog@php.net>
 * @copyright 2002-2008 Gregory Beaver
 * @license   http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version   CVS: $Id: ProceduralPages.inc,v 1.9 2008/02/24 02:35:44 ashnazg Exp $
 * @link      http://www.phpdoc.org
 * @link      http://pear.php.net/PhpDocumentor
 * @since     1.1
 * @todo      CS cleanup - change package to PhpDocumentor
 */

/**
 * Intermediate procedural page parsing structure.
 * This structure parses defines, functions, and global variables by file,
 * and then iterates over the elements to document conflicts.
 *
 * @category  ToolsAndUtilities
 * @package   phpDocumentor
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2002-2008 Gregory Beaver
 * @license   http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version   Release: 1.4.2
 * @link      http://www.phpdoc.org
 * @link      http://pear.php.net/PhpDocumentor
 * @since     1.1
 * @todo      CS cleanup - change package to PhpDocumentor
 */
class ProceduralPages
{
    /**
     * file being parsed, used in every add function 
     * to match up elements with the file that contains them
     *
     * @see addClass(), addMethod(), addVar(), nextFile()
     * @var string
     */
    var $curfile;
    /**
     * array of all procedural pages ordered by name
     * Format:
     * <pre>
     *     array(
     *         name => array(
     *             fullpath => parserPage,
     *             fullpath => parserPage2 [if there are name conflicts],
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $pages = array();
    /**
     * array of all procedural pages ordered by name 
     * that have been ignored via -po or @access private or @ignore
     * Format:
     * <pre>
     *     array(
     *         name => array(
     *             fullpath => parserPage,
     *             fullpath => parserPage2 [if there are name conflicts],
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $ignorepages = array();
    /**
     * array of all procedural page names ordered by full path to the file
     * Format:
     * <pre>
     *     array(
     *         fullpath => name
     *     )
     * </pre>
     *
     * @var array
     */
    var $pathpages = array();
    /**
     * array of parsed includes organized by the full path 
     * of the file that contains the include.
     * Format:
     * <pre>
     *     array(
     *         full path => array(
     *             includename => {@link parserInclude}
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $includesbyfile = array();
    /**
     * array of parsed functions organized by the full path 
     * of the file that contains the function.
     * Format:
     * <pre>
     *     array(
     *         full path => array(
     *             functionname => {@link parserFunction}
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $functionsbyfile = array();
    /**
     * array of parsed defines organized by the full path 
     * of the file that contains the define.
     * Format:
     * <pre>
     *     array(
     *         full path => array(
     *             definename => {@link parserDefine}
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $definesbyfile = array();
    /**
     * array of parsed global variables organized by the full path 
     * of the file that contains the global variable definition.
     * Format:
     * <pre>
     *     array(
     *         full path => array(
     *             globalname => {@link parserGlobal}
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $globalsbyfile = array();
    /**
     * array of file names organized by functions that are in the file.
     *
     * This structure is designed to handle name conflicts.  Two files can contain
     * functions with the same name, and this array will record both filenames to
     * help control namespace errors
     * Format:
     * <pre>
     *     array(
     *         functionname => array(
     *             full path of file containing functionname,
     *             full path of file 2 containing functionname,
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $functionsbynamefile = array();
    /**
     * array of file names organized by defines that are in the file.
     * This structure is designed to handle name conflicts.  Two files 
     * can contain defines with the same name, and this array will
     * record both filenames to help control namespace errors
     * Format:
     * <pre>
     *     array(
     *         definename => array(
     *             full path of file containing definename,
     *             full path of file 2 containing definename,
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $definesbynamefile = array();
    /**
     * array of file names organized by global variables that are in the file.
     *
     * This structure is designed to handle name conflicts.  Two files can 
     * contain global variables with the same name, and this array will
     * record both filenames to help control namespace errors
     * Format:
     * <pre>
     *     array(
     *         global variablename => array(
     *             full path of file containing global variablename,
     *             full path of file 2 containing global variablename,
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $globalsbynamefile = array();
    /**
     * array of packages ordered by full path
     * Format:
     * <pre>
     *     array(
     *         fullpath => array(
     *             packagename,
     *             subpackagename
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $pagepackages = array();
    /**
     * array of packages assigned to classes in a file, ordered by fullpath
     * Format:
     * <pre>
     *     array(
     *         fullpath => array(
     *             packagename => array(
     *                 subpackagename => 1,
     *                 subpackagename => 1,
     *                 ..
     *             ),
     *             packagename2 => array(...
     *             )
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $pageclasspackages = array();
    /**
     * Namespace conflicts within all documented packages of functions
     * Format:
     * <pre>
     *     array(
     *         functionname => array(
     *             full path,
     *             full path,
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $functionconflicts = array();
    /**
     * Namespace conflicts within all documented pages
     * Format:
     * <pre>
     *     array(
     *         pagename => array(
     *             fullpath,
     *             fullpath,
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $pageconflicts = array();
    /**
     * Namespace conflicts within all documented packages of functions
     * Format:
     * <pre>
     *     array(
     *         functionname => array(
     *             full path,
     *             full path,
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $defineconflicts = array();
    /**
     * Namespace conflicts within all documented packages of functions
     * Format:
     * <pre>
     *     array(
     *         functionname => array(
     *             full path,
     *             full path,
     *             ...
     *         )
     *     )
     * </pre>
     *
     * @var array
     */
    var $globalconflicts = array();
    /**
     * @access private
     * @var array
     */
    var $revcpbf = array();
    /**
     * @access private
     * @var boolean
     */
    var $packagesetup = false;

    /**
     * sets up the {@link $pages} array
     *
     * @param parserPage &$element the parser page element
     *
     * @return void
     */
    function addPage(&$element)
    {
        $this->curfile
            = $element->getPath();
        $this->pages[$element->getFile()][$element->getPath()]
            = $element;
        $this->pathpages[$this->curfile]
            = $element->getFile();
        $this->addPagePackage($this->curfile, 
            $element->package, $element->subpackage);
    }
    
    /**
     * moves a page from the {@link $pages} array to the {@link $ignorepages} array
     *
     * @param parserPage &$element the parser page element
     *
     * @return void
     */
    function ignorePage(&$element)
    {
        $this->ignorepages[$element->getFile()][$element->getPath()]
            = $this->pages[$element->getFile()][$element->getPath()];
        unset($this->pages[$element->getFile()][$element->getPath()]);
    }

    /**
     * gathers path-related info about a given element
     *
     * @param string $path path to the element
     * @param mixed  &$c   ???
     *
     * @return array|bool an array of path info,
     *                    or FALSE 
     * @todo figure out what &$c is and update the param tag
     */
    function getPathInfo($path, &$c)
    {
        $path = str_replace('/', SMART_PATH_DELIMITER, $path);
        $info = array();
        if (!isset($this->pathpages[$path])) {
            return false;
        }

        $p = $this->pages[$this->pathpages[$path]][$path];
        // fixes [ 1391432 ] Too many underscores in include links.
        $p->name = $p->origName;
        $p->name = $c->getPageName($p);

        $info['package']    = $p->package;
        $info['subpackage'] = $p->subpackage;
        $info['name']       = $p->getFile();
        $info['source_loc'] = $p->getSourceLocation($c);

        $x = new pageLink;
        $x->addLink($p->path, $p->name, $p->file, $p->package, $p->subpackage);

        $info['docs'] = $c->returnSee($x);
        $p->name      = $p->origName;

        return $info;
    }
    
    /**
     * Change a page's name from its file to alias $name
     *
     * This function is used to handle a @name tag in a page-level DocBlock
     *
     * @param string $name the alias
     *
     * @return void
     */
    function setName($name)
    {
        if ($this->pages[$name][$this->curfile]->file == $name) {
            addWarning(PDERROR_NAME_ALIAS_SAME_AS_TARGET,'');

        } else {
            $this->pages[$name][$this->curfile]
                = $this->pages[$this->pathpages[$this->curfile]][$this->curfile];
            $this->pages[$name][$this->curfile]->file
                = $name;

            unset($this->pages[$this->pathpages[$this->curfile]][$this->curfile]);

            $this->pathpages[$this->curfile] = $name;
        }
    }
    
    /**
     * Changes the package of the page represented by $path
     *
     * changes package in both the {@link $pages} array 
     * and the {@link pagepackages} array
     *
     * @param string $path       full path
     * @param string $package    the package name
     * @param string $subpackage the subpackage name
     *
     * @return void
     */
    function addPagePackage($path, $package, $subpackage)
    {
        $this->pages[$this->pathpages[$path]][$path]->package
            = $package;
        $this->pages[$this->pathpages[$path]][$path]->subpackage
            = $subpackage;
        $this->pagepackages[$path]
            = array($package, $subpackage);

        if (isset($this->includesbyfile[$path])) {
            foreach ($this->includesbyfile[$path] as $i => $el) {
                $el->package                     = $package;
                $el->subpackage                  = $subpackage;
                $this->includesbyfile[$path][$i] = $el;
            }
        }
        if (isset($this->functionsbyfile[$path])) {
            foreach ($this->functionsbyfile[$path] as $i => $el) {
                $el->package                      = $package;
                $el->subpackage                   = $subpackage;
                $this->functionsbyfile[$path][$i] = $el;
            }
        }
        if (isset($this->definesbyfile[$path])) {
            foreach ($this->definesbyfile[$path] as $i => $el) {
                $el->package                    = $package;
                $el->subpackage                 = $subpackage;
                $this->definesbyfile[$path][$i] = $el;
            }
        }
        if (isset($this->globalsbyfile[$path])) {
            foreach ($this->globalsbyfile[$path] as $i => $el) {
                $el->package                    = $package;
                $el->subpackage                 = $subpackage;
                $this->globalsbyfile[$path][$i] = $el;
            }
        }
    }

    /**
     * sets up the {@link $includesbyfile} array using {@link $curfile}
     *
     * @param parserInclude &$element the "include" element object
     *
     * @return void
     */
    function addInclude(&$element)
    {
        $this->includesbyfile[$this->curfile][] = $element;
    }

    /**
     * sets up the {@link $functionsbyfile} array using {@link $curfile}
     *
     * @param parserFunction &$element the "function" object
     *
     * @return void
     */
    function addFunction(&$element)
    {
        if (isset($this->functionsbyfile[$this->curfile])) {
            foreach ($this->functionsbyfile[$this->curfile] as $i => $function) {
                if ($function->getName() == $element->getName()) {
                    addWarning(PDERROR_ELEMENT_IGNORED, 'function',
                        $element->getName(), $this->curfile);
                    return;
                }
            }
        }
        $this->functionsbyfile[$this->curfile][]          = $element;
        $this->functionsbynamefile[$element->getName()][] = $this->curfile;
    }

    /**
     * sets up the {@link $globalsbyfile} array using {@link $curfile}
     *
     * @param parserGlobal &$element the "global" element
     *
     * @return void
     */
    function addGlobal(&$element)
    {
        if (isset($this->globalsbyfile[$this->curfile])) {
            foreach ($this->globalsbyfile[$this->curfile] as $i => $global) {
                if ($global->getName() == $element->getName()) {
                    addWarning(PDERROR_ELEMENT_IGNORED, 'global variable',
                        $element->getName(), $this->curfile);
                    return;
                }
            }
        }
        $this->globalsbyfile[$this->curfile][]          = $element;
        $this->globalsbynamefile[$element->getName()][] = $this->curfile;
    }

    /**
     * sets up the {@link $definesbyfile} array using {@link $curfile}
     *
     * @param parserDefine &$element the "define" element
     *
     * @return void
     */
    function addDefine(&$element)
    {
        if (isset($this->definesbyfile[$this->curfile])) {
            foreach ($this->definesbyfile[$this->curfile] as $i => $define) {
                if ($define->getName() == $element->getName()) {
                    addWarning(PDERROR_ELEMENT_IGNORED, 'define',
                        $element->getName(), $this->curfile);
                    return;
                }
            }
        }
        $this->definesbyfile[$this->curfile][]          = $element;
        $this->definesbynamefile[$element->getName()][] = $this->curfile;
    }
    
    /**
     * Used to align an element with the package of its parent page 
     * prior to Conversion.
     *
     * @param parserElement &$element the element to align
     *
     * @return void
     */
    function replaceElement(&$element)
    {
        if ($element->type == 'define') {
            foreach ($this->definesbyfile[$element->getPath()] as $i => $el) {
                if ($el->getName() == $element->getName()) {
                    $this->definesbyfile[$element->getPath()][$i] = &$element;
                }
            }
        } elseif ($element->type == 'global') {
            foreach ($this->globalsbyfile[$element->getPath()] as $i => $el) {
                if ($el->getName() == $element->getName()) {
                    $this->globalsbyfile[$element->getPath()][$i] = &$element;
                }
            }
        } elseif ($element->type == 'include') {
            foreach ($this->includesbyfile[$element->getPath()] as $i => $el) {
                if ($el->getName() == $element->getName()) {
                    $this->includesbyfile[$element->getPath()][$i] = &$element;
                }
            }
        } elseif ($element->type == 'function') {
            foreach ($this->functionsbyfile[$element->getPath()] as $i => $el) {
                if ($el->getName() == $element->getName()) {
                    $this->functionsbyfile[$element->getPath()][$i] = &$element;
                }
            }
        }
    }

    /**
     * adds a package from a class to the current file
     *
     * @param string $file       full path to the file that contains the class
     * @param string $package    package name
     * @param string $subpackage subpackage name
     *
     * @return void
     */
    function addClassPackageToFile($file, $package, $subpackage)
    {
        if (!isset($this->revcpbf[$file][$package][$subpackage])) {
            $this->pageclasspackages[$file][$package][$subpackage] = 1;
        }
        $this->revcpbf[$file][$package][$subpackage] = 1;
    }
    
    /**
     * if there is one class package in a file, 
     * the parent path inherits the package if its package is default.
     * helps with -po to avoid dumb bugs
     *
     * @return void
     */
    function setupPagePackages()
    {
        if ($this->packagesetup) {
            return;
        }
        foreach ($this->pageclasspackages as $fullpath => $packages) {
            if (isset($this->pagepackages[$fullpath])) {
                if ($this->pagepackages[$fullpath][0] 
                    == $GLOBALS['phpDocumentor_DefaultPackageName']
                ) {
                    if (count($packages) == 1) {
                        list($package, $subpackage) = each($packages);
                        if (count($subpackage) == 1) {
                            list($subpackage,) = each($subpackage);
                        } else {
                            $subpackage = '';
                        }
                        $this->addPagePackage($fullpath, $package, $subpackage);
                    }
                }
            }
        }
        $this->packagesetup = true;
    }
    
    /**
     * extracts function, define, and global variable name conflicts within the 
     * same package and between different packages.  No two elements with the same
     * name are allowed in the same package, to keep automatic linking possible.
     *
     * @param mixed &$render the renderer object
     *
     * @return void
     * @access private
     * @todo functions, defines, and globals are coded,
     *       but pages section is empty... does it need to be coded?
     */
    function setupConflicts(&$render)
    {
        foreach ($this->functionsbynamefile as $function => $paths) {
            if (count($paths) - 1) {
                //conflict
                $package = array();
                foreach ($paths as $path) {
                    // create a list of conflicting functions in each package
                    $package[$this->pagepackages[$path][0]][] = $path;
                }
                foreach ($package as $pathpackages) {
                    // if at least 2 functions exist in the same package, 
                    // delete all but the first one and add warnings
                    if (count($pathpackages) - 1) {
                        for ($i=1; $i < count($pathpackages); $i++) {
                            addWarning(PDERROR_ELEMENT_IGNORED, 'function',
                                $function, $pathpackages[$i]);
                            foreach ($this->functionsbyfile[$pathpackages[$i]] 
                                as $j => $blah
                            ) {
                                if ($this->functionsbyfile[$pathpackages[$i]][$j]
                                    ->getName() == $function
                                ) {
                                    unset($this
                                        ->functionsbyfile[$pathpackages[$i]][$j]);
                                }
                            }
                            $oth = array_flip($paths);
                            unset($paths[$oth[$pathpackages[$i]]]);
                        }
                    }
                }
                $this->functionconflicts[$function] = $paths;
            }
        }

        foreach ($this->definesbynamefile as $define => $paths) {
            if (count($paths) - 1) { 
                //conflict
                $package = array();
                foreach ($paths as $path) {
                    // create a list of conflicting functions in each package
                    $package[$this->pagepackages[$path][0]][] = $path;
                }
                foreach ($package as $pathpackages) {
                    // if at least 2 functions exist in the same package, 
                    // delete all but the first one and add warnings
                    if (count($pathpackages) - 1) {
                        for ($i=1; $i < count($pathpackages); $i++) {
                            addWarning(PDERROR_ELEMENT_IGNORED, 'define',
                                $define, $pathpackages[$i]);
                            foreach ($this->definesbyfile[$pathpackages[$i]]
                                as $j => $blah
                            ) {
                                if ($this->definesbyfile[$pathpackages[$i]][$j]
                                    ->getName() == $define
                                ) {
                                    unset($this
                                        ->definesbyfile[$pathpackages[$i]][$j]);
                                }
                            }
                            $oth = array_flip($paths);
                            unset($paths[$oth[$pathpackages[$i]]]);
                        }
                    }
                }
                $this->defineconflicts[$define] = $paths;
            }
        }

        foreach ($this->globalsbynamefile as $global => $paths) {
            if (count($paths) - 1) { 
                //conflict
                $package = array();
                foreach ($paths as $path) {
                    // create a list of conflicting functions in each package
                    $package[$this->pagepackages[$path][0]][] = $path;
                }
                foreach ($package as $pathpackages) {
                    // if at least 2 functions exist in the same package, 
                    // delete all but the first one and add warnings
                    if (count($pathpackages) - 1) {
                        for ($i=1; $i < count($pathpackages); $i++) {
                            addWarning(PDERROR_ELEMENT_IGNORED, 'global variable',
                                $global, $pathpackages[$i]);
                            foreach ($this->globalsbyfile[$pathpackages[$i]] 
                                as $j => $blah
                            ) {
                                if ($this->globalsbyfile[$pathpackages[$i]][$j]
                                    ->getName() == $global
                                ) {
                                    unset($this
                                        ->globalsbyfile[$pathpackages[$i]][$j]);
                                }
                            }
                            $oth = array_flip($paths);
                            unset($paths[$oth[$pathpackages[$i]]]);
                        }
                    }
                }
                $this->globalconflicts[$global] = $paths;
            }
        }
        
        /*
         * @todo does this section still need to be coded???
         */
        foreach ($this->pages as $name => $pages) {
            if (count($pages) - 1) { 
                // possible conflict
            }
        }
    }
    
    /**
     * called by {@link parserFunction::getConflicts()} to get 
     * inter-package conflicts, should not be called directly
     *
     * @param string $name the function name to check
     *
     * @access private
     * @return array|bool Format: (package => {@link parserFunction} 
     *                    of conflicting function)
     *                    or FALSE if the function is not recorded as a conflict
     */
    function getFuncConflicts($name)
    {
        if (!isset($this->functionconflicts[$name])) {
            return false;
        }
        $a = array();
        foreach ($this->functionconflicts[$name] as $conflict) {
            foreach ($this->functionsbyfile[$conflict] as $i => $func) {
                if ($func->getName() == $name) {
                    $a[$this->functionsbyfile[$conflict][$i]->docblock->package] 
                        = $this->functionsbyfile[$conflict][$i];
                }
            }
        }
        return $a;
    }
    
    /**
     * called by {@link parserGlobal::getConflicts()} 
     * to get inter-package conflicts, should not be called directly
     *
     * @param string $name the global name to check
     *
     * @access private
     * @return array|bool Format: (package => {@link parserGlobal} 
     *                    of conflicting global variable)
     *                    or FALSE if the global is not recorded as a conflict
     */
    function getGlobalConflicts($name)
    {
        if (!isset($this->globalconflicts[$name])) {
            return false;
        }
        $a = array();
        foreach ($this->globalconflicts[$name] as $conflict) {
            foreach ($this->globalsbyfile[$conflict] as $i => $func) {
                if ($func->getName() == $name) {
                    $a[$this->globalsbyfile[$conflict][$i]->docblock->package] 
                        = $this->globalsbyfile[$conflict][$i];
                }
            }
        }
        return $a;
    }
    
    /**
     * called by {@link parserDefine::getConflicts()} 
     * to get inter-package conflicts, should not be called directly
     *
     * @param string $name the define name to check
     *
     * @access private
     * @return array|bool Format: (package => {@link parserDefine} 
     *                    of conflicting define)
     *                    or FALSE if the define is not recorded as a conflict
     */
    function getDefineConflicts($name)
    {
        if (!isset($this->defineconflicts[$name])) {
            return false;
        }
        $a = array();
        foreach ($this->defineconflicts[$name] as $conflict) {
            foreach ($this->definesbyfile[$conflict] as $i => $func) {
                if ($func->getName() == $name) {
                    $a[$this->definesbyfile[$conflict][$i]->docblock->package] 
                        = $this->definesbyfile[$conflict][$i];
                }
            }
        }
        return $a;
    }
    
    /**
     * Adjusts packages of all pages and removes name conflicts within a package
     *
     * Automatic linking requires that each linkable name have exactly one element 
     * associated with it.  In other words, there cannot be two functions named 
     * foo() in the same package.
     *
     * This also adheres to php rules with one exception:
     *
     * <code>
     * if ($test == 3) {
     *    define('whatever', 'this thing');
     * } else {
     *    define('whatever', 'this other thing');
     * }
     * </code>
     *
     * phpDocumentor is not aware of conditional control structures because it 
     * would slow things down considerably.  So, what phpDocumentor does is 
     * automatically ignore the second define and raise a warning.  The warning can
     * be eliminated with an @ignore tag on the second element like so:
     *
     * <code>
     * if ($test == 3) {
     *    define('whatever', 'this thing');
     * } else {
     *    /**
     *     * @ignore
     *     {@*}
     *    define('whatever', 'this other thing');
     * }
     * </code>
     *
     * If there are two files that contain the same procedural elements in the 
     * same package (for example, a common configuration file common.php), they 
     * will also be ignored as if they were in the same file.  The reasoning
     * behind this is simple.  A package is an indivisible set of files and 
     * classes that a user will include in their code.  Name conflicts must be 
     * avoided to allow successful execution.
     *
     * This function also plays the all-important role of calling 
     * {@link phpDocumentor_IntermediateParser::addElementToPage()} in order to add 
     * processed elements to their pages for Conversion.
     *
     * @param phpDocumentor_IntermediateParser &$render the parser
     *
     * @return void
     */
    function setupPages(&$render)
    {
        global $_phpDocumentor_setting;
        phpDocumentor_out("\nProcessing Procedural Page Element Name Conflicts\n\n");
        flush();
        $this->setupPagePackages();
        $this->setupConflicts($render);
        // phpDocumentor_out("\nProcessing Procedural Pages\n\n");
        foreach ($this->pathpages as $path => $name) {
            // phpDocumentor_out("Processing $path\n");
            $a = $this->pagepackages[$path];
            $b = &$this->pages[$name][$path];
            $render->addPage($b, $path);
            $render->addUses($b, $path);
            if (isset($this->includesbyfile[$path])) {
                foreach ($this->includesbyfile[$path] as $include) {
                    $include->docblock->package    = $a[0];
                    $include->docblock->subpackage = $a[1];
                    $render->addElementToPage($include, $path);
                }
            }
    
            if (isset($this->functionsbyfile[$path])) {
                foreach ($this->functionsbyfile[$path] as $function) {
                    $function->docblock->package    = $a[0];
                    $function->docblock->subpackage = $a[1];
                    $render->addElementToPage($function, $path);
                    $render->addUses($function, $path);
                }
            }
    
            if (isset($this->definesbyfile[$path])) {
                foreach ($this->definesbyfile[$path] as $define) {
                    $define->docblock->package    = $a[0];
                    $define->docblock->subpackage = $a[1];
                    $render->addElementToPage($define, $path);
                    $render->addUses($define, $path);
                }
            }
    
            if (isset($this->globalsbyfile[$path])) {
                foreach ($this->globalsbyfile[$path] as $global) {
                    $global->docblock->package    = $a[0];
                    $global->docblock->subpackage = $a[1];
                    $render->addElementToPage($global, $path);
                    $render->addUses($global, $path);
                }
            }
        }
    }
    
    /**
     * sets the parser base
     *
     * @param mixed $pbase the parser base
     *
     * @return void
     */
    function setParseBase($pbase)
    {
        $this->_parsedbase = $pbase;
    }
    
    /**
     * checks to see if the parsed file matches the given path
     *
     * @param string $path   the path to look for
     * @param string $infile the file to check
     *
     * @return parserPage|bool matched parserPage if found,
     *                         or FALSE if not found
     */
    function pathMatchesParsedFile($path, $infile)
    {
        $test = $this->getRealPath($path, $infile);
        if (is_string($test)) {
            if (isset($this->pathpages[$test])) {
                return $this->pages[$this->pathpages[$test]][$test];
            }
            if (PHPDOCUMENTOR_WINDOWS) {
                $test = str_replace('/', '\\', $test);
            }
            if (isset($this->pathpages[$test])) {
                $a = $this->pages[$this->pathpages[$test]][$test];
                if (is_array($a->packageOutput) 
                    && !in_array($a->package, $a->packageOutput)
                ) {
                    return false;
                }
                return $this->pages[$this->pathpages[$test]][$test];
            }
        } else {
            foreach ($test as $file) {
                if (isset($this->pathpages[$file])) {
                    return $this->pages[$this->pathpages[$file]][$file];
                }
                if (PHPDOCUMENTOR_WINDOWS) {
                    $file = str_replace('/', '\\', $file);
                }
                if (isset($this->pathpages[$file])) {
                    $a = $this->pages[$this->pathpages[$file]][$file];
                    if (is_array($a->packageOutput) 
                        && !in_array($a->package, $a->packageOutput)
                    ) {
                        return false;
                    }
                    return $this->pages[$this->pathpages[$file]][$file];
                }
            }
        }
        return false;
    }
    
    /**
     * Ensures the path to the file is an absolute path
     * 
     * @param string $path path to the file
     * @param string $file the file name
     *
     * @return array|string returns an array of possible file locations or
     *                      a string if there is an exact match
     */
    function getRealPath($path, $file)
    {
        $curdir = str_replace('\\', '/', dirname($file));
        $path   = str_replace('\\', '/', $path);
        if (strpos($path, ':') !== false) {
            // windows, and we have a drive letter
            return $path;
        } elseif (strpos($path, '/') === 0) {
            return $path;
        }
        // not an absolute path
        $path = explode('/', $path);
        if ($path[0] == '.') {
            $path[0] = dirname($file);
            return join($path, '/');
        } elseif ($path[0] == '..') {
            $dirfile = explode('/', dirname(str_replace('\\', '/', $file)));
            // remove the current directory
            array_pop($dirfile); 
            if (!count($dirfile)) {
                // we were at a top-level dir!
                return false;
            }
            // replace .. with parent dirname
            $path[0] = join($dirfile, '/');
            return join($path, '/');
        } else {
            $path = join($path, '/');
            return array($curdir . PATH_DELIMITER . $path,
                str_replace('\\', '/', PHPDOCUMENTOR_BASE)
                . PATH_DELIMITER . $path);
        }
    }
}
?>
