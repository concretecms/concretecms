<?php
namespace Concrete\Core\Page\Theme\GridFramework;

use Concrete\Core\Page\Page;
use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet;

abstract class GridFramework
{
    /**
     * @since 5.7.5
     */
    const DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL = 10;
    /**
     * @since 5.7.5
     */
    const DEVICE_CLASSES_HIDE_ON_SMALL = 20;
    /**
     * @since 5.7.5
     */
    const DEVICE_CLASSES_HIDE_ON_MEDIUM = 30;
    /**
     * @since 5.7.5
     */
    const DEVICE_CLASSES_HIDE_ON_LARGE = 40;

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnExtraSmallDeviceClass()
    {
        return null;
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnSmallDeviceClass()
    {
        return null;
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnMediumDeviceClass()
    {
        return null;
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkHideOnLargeDeviceClass()
    {
        return null;
    }

    abstract public function getPageThemeGridFrameworkName();

    abstract public function getPageThemeGridFrameworkRowStartHTML();

    abstract public function getPageThemeGridFrameworkRowEndHTML();

    abstract public function getPageThemeGridFrameworkContainerStartHTML();

    abstract public function getPageThemeGridFrameworkContainerEndHTML();

    public function getPageThemeGridFrameworkNumColumns()
    {
        $classes = $this->getPageThemeGridFrameworkColumnClasses();

        return count($classes);
    }

    public function hasPageThemeGridFrameworkOffsetClasses()
    {
        $classes = $this->getPageThemeGridFrameworkColumnOffsetClasses();

        return count($classes) > 0;
    }

    abstract public function getPageThemeGridFrameworkColumnClasses();

    abstract public function getPageThemeGridFrameworkColumnOffsetClasses();

    /**
     * @since 5.7.2.1
     */
    abstract public function getPageThemeGridFrameworkColumnAdditionalClasses();

    /**
     * @since 5.7.2.1
     */
    abstract public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses();

    public function getPageThemeGridFrameworkColumnClassForSpan($span)
    {
        $span = $span - 1;
        $classes = $this->getPageThemeGridFrameworkColumnClasses();

        return $classes[$span];
    }

    public function getPageThemeGridFrameworkColumnClassForOffset($offset)
    {
        $offset = $offset - 1;
        $classes = $this->getPageThemeGridFrameworkColumnOffsetClasses();

        return $classes[$offset];
    }

    /**
     * @since 5.7.4
     */
    public function getPageThemeGridFrameworkColumnClassesForSpan($span)
    {
        $classes = $this->getPageThemeGridFrameworkColumnClassForSpan($span);

        if ($this->getPageThemeGridFrameworkColumnAdditionalClasses()) {
            $classes .= ' '.$this->getPageThemeGridFrameworkColumnAdditionalClasses();
        }

        return $classes;
    }

    /**
     * @since 5.7.4
     */
    public function getPageThemeGridFrameworkColumnClassesForOffset($offset)
    {
        $classes = $this->getPageThemeGridFrameworkColumnClassForOffset($offset);

        if ($this->getPageThemeGridFrameworkColumnOffsetAdditionalClasses()) {
            $classes .= ' '.$this->getPageThemeGridFrameworkColumnOffsetAdditionalClasses();
        }

        return $classes;
    }

    /**
     * @since 5.7.5.3
     */
    public function getPageThemeGridFrameworkSelectedDeviceHideClassesForDisplay(StyleSet $set, Page $page)
    {
        $classes = array();
        if (!$page->isEditMode()) {
            if ($set->getHideOnExtraSmallDevice()) {
                $classes[] = $this->getPageThemeGridFrameworkHideOnExtraSmallDeviceClass();
            }
            if ($set->getHideOnSmallDevice()) {
                $classes[] = $this->getPageThemeGridFrameworkHideOnSmallDeviceClass();
            }
            if ($set->getHideOnMediumDevice()) {
                $classes[] = $this->getPageThemeGridFrameworkHideOnMediumDeviceClass();
            }
            if ($set->getHideOnLargeDevice()) {
                $classes[] = $this->getPageThemeGridFrameworkHideOnLargeDeviceClass();
            }
        }

        return $classes;
    }

    /**
     * @since 5.7.5
     */
    public function getPageThemeGridFrameworkDeviceHideClasses()
    {
        $classes = array();
        if ($this->getPageThemeGridFrameworkHideOnExtraSmallDeviceClass()) {
            $classes[] = self::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL;
        }
        if ($this->getPageThemeGridFrameworkHideOnSmallDeviceClass()) {
            $classes[] = self::DEVICE_CLASSES_HIDE_ON_SMALL;
        }
        if ($this->getPageThemeGridFrameworkHideOnMediumDeviceClass()) {
            $classes[] = self::DEVICE_CLASSES_HIDE_ON_MEDIUM;
        }
        if ($this->getPageThemeGridFrameworkHideOnLargeDeviceClass()) {
            $classes[] = self::DEVICE_CLASSES_HIDE_ON_LARGE;
        }

        return $classes;
    }

    /**
     * @since 5.7.5
     */
    public function getDeviceHideClassIconClass($class)
    {
        switch ($class) {
            case self::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL:
                return 'fa fa-mobile-phone';
            case self::DEVICE_CLASSES_HIDE_ON_SMALL:
                return 'fa fa-tablet';
            case self::DEVICE_CLASSES_HIDE_ON_MEDIUM:
                return 'fa fa-laptop';
            case self::DEVICE_CLASSES_HIDE_ON_LARGE:
                return 'fa fa-desktop';
        }
    }

    /**
     * @since 5.7.5
     */
    public function supportsNesting()
    {
        return false;
    }
}
