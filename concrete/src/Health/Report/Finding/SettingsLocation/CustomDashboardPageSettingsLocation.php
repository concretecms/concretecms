<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation;

class CustomDashboardPageSettingsLocation extends DashboardPageSettingsLocation
{

    /**
     * @var string
     */
    protected $pagePath;

    /**
     * @var string
     */
    protected $pageName;

    /**
     * @param string $pagePath
     * @param string $pageName
     */
    public function __construct(string $pagePath, string $pageName)
    {
        $this->pagePath = $pagePath;
        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getPagePath(): string
    {
        return $this->pagePath;
    }

    /**
     * @return string
     */
    public function getPageName(): string
    {
        return $this->pageName;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => static::class,
            'pageName' => $this->getPageName(),
            'pagePath' => $this->getPagePath(),
        ];
    }


}
