<?php
namespace Concrete\Core\Application\Service;

use Concrete\Core\Http\Response;
use HtmlObject\Traits\Tag;
use PermissionKey;
use User as ConcreteUser;
use Loader;
use Core;
use Page;
use Config;
use Session;
use Concrete\Core\View\ErrorView;
use stdClass;

/**
 * Useful functions for generating elements on the Concrete interface.
 *
 * @subpackage Concrete
 * \@package Helpers
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class UserInterface
{
    public static $menuItems = [];

    /**
     * Generates a submit button in the Concrete style.
     *
     * @param string $text The text of the button
     * @param bool|string $formID The form this button will submit
     * @param string $buttonAlign
     * @param string $innerClass
     * @param array $args Extra args passed to the link
     *
     * @return string
     */
    public function submit($text, $formID = false, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        if ('right' == $buttonAlign) {
            $innerClass .= ' pull-right';
        } elseif ('left' == $buttonAlign) {
            $innerClass .= ' pull-left';
        }

        if (!$formID) {
            $formID = 'button';
        }
        $argsstr = '';
        foreach ($args as $k => $v) {
            $argsstr .= $k . '="' . $v . '" ';
        }

        return '<input type="submit" class="btn ' . $innerClass . '" value="' . $text . '" id="ccm-submit-' . $formID . '" name="ccm-submit-' . $formID . '" ' . $argsstr . ' />';
    }

    /**
     * Generates a simple link button in the Concrete style.
     *
     * @param string $text The text of the button
     * @param string $href
     * @param string $buttonAlign
     * @param string $innerClass
     * @param array $args Extra args passed to the link
     *
     * @return string
     */
    public function button($text, $href, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        if ('right' == $buttonAlign) {
            $innerClass .= ' pull-right';
        } elseif ('left' == $buttonAlign) {
            $innerClass .= ' pull-left';
        }
        $argsstr = '';
        foreach ($args as $k => $v) {
            $argsstr .= $k . '="' . $v . '" ';
        }

        return '<a href="' . $href . '" class="btn btn-default ' . $innerClass . '" ' . $argsstr . '>' . $text . '</a>';
    }

    /**
     * Generates a JavaScript function button in the Concrete style.
     *
     * @param string $text The text of the button
     * @param string $onclick
     * @param string $buttonAlign
     * @param string $innerClass - no longer used
     * @param array $args Extra args passed to the link
     *
     * @return string
     */
    public function buttonJs($text, $onclick, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        if ('right' == $buttonAlign) {
            $innerClass .= ' pull-right';
        } elseif ('left' == $buttonAlign) {
            $innerClass .= ' pull-left';
        }
        $argsstr = '';
        foreach ($args as $k => $v) {
            $argsstr .= $k . '="' . $v . '" ';
        }

        return '<input type="button" class="btn btn-default ' . $innerClass . '" value="' . $text . '" onclick="' . $onclick . '" ' . $buttonAlign . ' ' . $argsstr . ' />';
    }

    /**
     * @deprecated
     */
    public function button_js($text, $onclick, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        return self::buttonJs($text, $onclick, $buttonAlign, $innerClass, $args);
    }

    /**
     * Outputs button text passed as arguments with a special Concrete wrapper for positioning
     * <code>
     *    $bh->buttons($myButton1, $myButton2, $myButton3);
     * </code>.
     *
     * @param string $buttons
     *
     * @return string
     */
    public function buttons($buttons = null)
    {
        if (!is_array($buttons)) {
            $buttons = func_get_args();
        }
        $html = '<div class="ccm-buttons well">';
        foreach ($buttons as $_html) {
            $html .= $_html . ' ';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * @param \Concrete\Core\Page\Page $c
     *
     * @return string
     */
    public function getQuickNavigationLinkHTML($c)
    {
        $cnt = Loader::controller($c);
        if (method_exists($cnt, 'getQuickNavigationLinkHTML')) {
            return $cnt->getQuickNavigationLinkHTML();
        } else {
            return '<a href="' . Core::make('helper/navigation')->getLinkToCollection($c) . '">' . $c->getCollectionName() . '</a>';
        }
    }

    /**
     * @return bool
     */
    public function showWhiteLabelMessage()
    {
        return Config::get('concrete.white_label.logo') || file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/logo_menu.png');
    }

    /**
     * @return string
     */
    public function getToolbarLogoSRC()
    {
        $alt = false;
        $src = false;
        if (Config::get('concrete.white_label.name')) {
            $alt = Config::get('concrete.white_label.name');
        }
        if (!$alt) {
            $alt = 'concrete5';
        }
        if (Config::get('concrete.white_label.logo')) {
            $src = Config::get('concrete.white_label.logo');
        }
        if (!$src) {
            $filename = 'logo';
            if (file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.svg')) {
                $src = REL_DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.svg';
            } elseif (file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.png')) {
                $src = REL_DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.png';
            } else {
                $src = ASSETS_URL_IMAGES . '/' . $filename . '.svg';
            }
        }

        return '<img id="ccm-logo" src="' . $src . '" alt="' . $alt . '" title="' . $alt . '">';
    }

    /**
     * @deprecated The Newsflow Overlay feature has been removed
     *
     * @return bool
     */
    public function showNewsflowOverlay()
    {
        return false;
    }

    /**
     * Shall we show the introductive help overlay?
     *
     * @return bool
     */
    public function showHelpOverlay()
    {
        $result = false;
        if (Config::get('concrete.misc.help_overlay')) {
            $u = new ConcreteUser();
            $timestamp = $u->config('MAIN_HELP_LAST_VIEWED');
            if (!$timestamp) {
                $result = true;
            }
        }

        return $result;
    }

    public function trackHelpOverlayDisplayed()
    {
        $u = new ConcreteUser();
        $u->saveConfig('MAIN_HELP_LAST_VIEWED', time());
    }

    /**
     * Clears the Interface Items Cache (clears the session).
     */
    public function clearInterfaceItemsCache()
    {
        $u = new ConcreteUser();
        if ($u->isRegistered()) {
            Session::remove('dashboardMenus');
        }
    }

    /**
     * Cache the interface items.
     */
    public function cacheInterfaceItems()
    {
        $u = new ConcreteUser();
        if ($u->isRegistered()) {
            Core::make('helper/concrete/dashboard')->getIntelligentSearchMenu();
        }
    }

    /**
     * @param \Concrete\Core\Page\Page[] $tabs
     *
     * @return string
     */
    public function pagetabs($tabs)
    {
        $tcn = rand(0, getrandmax());

        $html = '<ul class="nav-tabs nav" id="ccm-tabs-' . $tcn . '">';
        $c = Page::getCurrentPage();
        foreach ($tabs as $t) {
            if (is_array($t)) {
                $name = $t[1];
                $_c = $t[0];
            } else {
                $_c = $t;
                $name = $t->getCollectionName();
            }

            $href = Core::make('helper/navigation')->getLinkToCollection($_c);
            $active = false;
            if (is_object($c) && $c->getCollectionID() == $_c->getCollectionID()) {
                $active = true;
            }
            $html .= '<li class="' . (($active) ? 'active' : '') . '"><a href="' . $href . '">' . $name . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * @param \Concrete\Core\Page\Page[] $tabs
     * @param bool $jstabs
     * @param string $callback
     *
     * @return string
     */
    public function tabs($tabs, $jstabs = true, $callback = 'ccm_activateTabBar')
    {
        $tcn = rand(0, getrandmax());

        $html = '<ul class="nav-tabs nav" id="ccm-tabs-' . $tcn . '">';
        foreach ($tabs as $t) {
            $dt = $t[0];
            $href = '#';
            if (!$jstabs) {
                $dt = '';
                $href = $t[0];
            }
            $html .= '<li class="' . ((isset($t[2]) && true == $t[2]) ? 'active' : '') . '"><a href="' . $href . '" data-tab="' . $dt . '">' . $t[1] . '</a></li>';
        }
        $html .= '</ul>';
        if ($jstabs) {
            $html .= '<script type="text/javascript">$(function() { ' . $callback . '($(\'#ccm-tabs-' . $tcn . '\'));});</script>';
        }

        return $html;
    }

    /**
     * @param string $title
     * @param string $error
     * @param bool|\Exception $exception
     */
    public function renderError($title, $error, $exception = false)
    {
        Response::closeOutputBuffers(1, false);
        $this->buildErrorResponse($title, $error, $exception)->send();
    }

    /**
     * @param string $title
     * @param string $error
     * @param bool|\Exception $exception
     *
     * @return Response;
     */
    public function buildErrorResponse($title, $error, $exception = false)
    {
        $o = new stdClass();
        $o->title = $title;
        $o->content = $error;
        if ($exception) {
            $o->content .= $exception->getTraceAsString();
        }

        $ve = new ErrorView($o);
        $contents = $ve->render($o);

        return Response::create($contents, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param array $arguments
     *
     * @return string
     */
    public function notify($arguments)
    {
        $defaults = [
            'type' => 'success',
            'icon' => 'fa fa-check-mark',
            'title' => false,
            'text' => false,
            'form' => false,
            'hide' => false,
            'addclass' => 'ccm-notification-page-alert',
            'buttons' => [],
        ];

        // overwrite all the defaults with the arguments
        $arguments = array_merge($defaults, $arguments);

        $arguments['addclass'] .= ' ccm-ui';

        $text = '';

        if ($arguments['form']) {
            $text .= $arguments['form'];
        }

        $text .= $arguments['text'];

        if (count($arguments['buttons']) > 0) {
            $text .= '<div class="ccm-notification-inner-buttons">';
            if (1 == count($arguments['buttons'])) {
                $singleButton = $arguments['buttons'][0];
                if ($singleButton instanceof Tag) {
                    $singleButton->addClass('btn btn-xs btn-default');
                }
                $text .= '<div>' . $singleButton . '</div>';
            } else {
                $text .= '<div class="btn-group"><button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">';
                foreach ($arguments['buttons'] as $button) {
                    $text .= '<li>' . $button . '</li>';
                }
                $text .= '</ul></div>';
            }
            $text .= '</div>';
        }

        if ($arguments['form']) {
            $text .= '</form>';
        }

        $arguments['text'] = $text;

        unset($arguments['buttons']);
        $string = json_encode($arguments);

        $content = '<script type="text/javascript">$(function() {';
        $content .= 'new PNotify(' . $string . ');';
        $content .= '});</script>';

        return $content;
    }
}
