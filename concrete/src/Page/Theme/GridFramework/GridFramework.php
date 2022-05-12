<?php
namespace Concrete\Core\Page\Theme\GridFramework;

use Concrete\Core\Page\Page;
use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet;

abstract class GridFramework
{
    const DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL = 10;
    const DEVICE_CLASSES_HIDE_ON_SMALL = 20;
    const DEVICE_CLASSES_HIDE_ON_MEDIUM = 30;
    const DEVICE_CLASSES_HIDE_ON_LARGE = 40;

    public function getPageThemeGridFrameworkHideOnExtraSmallDeviceClass()
    {
        return null;
    }

    public function getPageThemeGridFrameworkHideOnSmallDeviceClass()
    {
        return null;
    }

    public function getPageThemeGridFrameworkHideOnMediumDeviceClass()
    {
        return null;
    }

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

    abstract public function getPageThemeGridFrameworkColumnAdditionalClasses();

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

    public function getPageThemeGridFrameworkColumnClassesForSpan($span)
    {
        $classes = $this->getPageThemeGridFrameworkColumnClassForSpan($span);

        if ($this->getPageThemeGridFrameworkColumnAdditionalClasses()) {
            $classes .= ' '.$this->getPageThemeGridFrameworkColumnAdditionalClasses();
        }

        return $classes;
    }

    public function getPageThemeGridFrameworkColumnClassesForOffset($offset)
    {
        $classes = $this->getPageThemeGridFrameworkColumnClassForOffset($offset);

        if ($this->getPageThemeGridFrameworkColumnOffsetAdditionalClasses()) {
            $classes .= ' '.$this->getPageThemeGridFrameworkColumnOffsetAdditionalClasses();
        }

        return $classes;
    }

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

    public function getDeviceHideClassIconClass($class)
    {
        switch ($class) {
            case self::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL:
                return 'fas fa-mobile-alt';
            case self::DEVICE_CLASSES_HIDE_ON_SMALL:
                return 'fas fa-tablet-alt';
            case self::DEVICE_CLASSES_HIDE_ON_MEDIUM:
                return 'fas fa-laptop';
            case self::DEVICE_CLASSES_HIDE_ON_LARGE:
                return 'fas fa-desktop';
        }
    }

    /**
     * @param int $class A const representing a device size
     * @return string
     */
    public function getDeviceHideClasses(int $class): string
    {
        switch ($class) {
            case self::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL:
                return $this->getPageThemeGridFrameworkHideOnExtraSmallDeviceClass();
            case self::DEVICE_CLASSES_HIDE_ON_SMALL:
                return $this->getPageThemeGridFrameworkHideOnSmallDeviceClass();
            case self::DEVICE_CLASSES_HIDE_ON_MEDIUM:
                return $this->getPageThemeGridFrameworkHideOnMediumDeviceClass();
            case self::DEVICE_CLASSES_HIDE_ON_LARGE:
                return $this->getPageThemeGridFrameworkHideOnLargeDeviceClass();
        }
        return '';
    }

    public function supportsNesting()
    {
        return false;
    }
}
