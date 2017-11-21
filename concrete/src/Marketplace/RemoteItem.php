<?php
namespace Concrete\Core\Marketplace;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageArchive;
use Loader;
use Config;
use Concrete\Core\Foundation\ConcreteObject;
use Exception;

class RemoteItem extends ConcreteObject
{
    protected $price = 0.00;
    protected $remoteCID = 0;
    protected $remoteURL = '';
    protected $remoteFileURL = '';
    protected $remoteIconURL = '';
    protected $isLicensedToSite = false;

    public function setPropertiesFromJSONObject($obj)
    {
        foreach ($obj as $prop => $value) {
            $this->{$prop} = $value;
        }
    }

    public function getMarketplaceItemID()
    {
        return $this->mpID;
    }
    public function getMarketplaceItemType()
    {
        return $this->mpType;
    }
    public function getHandle()
    {
        return $this->handle;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getBody()
    {
        return $this->bodyContent;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getSkillLevel()
    {
        return $this->skillLevel;
    }
    public function getExampleURL()
    {
        return $this->exampleURL;
    }
    public function getSkillLevelClassName()
    {
        switch ($this->getSkillLevel()) {
            case 'beginner':
                return 'fa fa-cog ccm-marketplace-skill-level-beginner';
            case 'intermediate':
                return 'fa fa-cog ccm-marketplace-skill-level-intermediate';
            case 'advanced':
                return 'fa fa-cog ccm-marketplace-skill-level-advanced';
            case 'bleeding_edge':
                return 'fa fa-cogs ccm-marketplace-skill-level-bleeding-edge';
        }
    }
    public function getSkillLevelDisplayName()
    {
        switch ($this->getSkillLevel()) {
            case 'beginner':
                return t('Beginner');
            case 'intermediate':
                return t('Intermediate');
            case 'advanced':
                return t('Advanced');
            case 'bleeding_edge':
                return t('Bleeding Edge/Developer');
        }
    }

    public function getLocalURL()
    {
        if ($this->getMarketplaceItemType() == 'theme') {
            return \URL::to('/dashboard/extend/themes/', 'view_detail', $this->getMarketplaceItemID());
        } else {
            return \URL::to('/dashboard/extend/addons/', 'view_detail', $this->getMarketplaceItemID());
        }
    }

    public function getDisplayPrice()
    {
        if ($this->price == '' || $this->price == '0' || $this->price == '0.00') {
            return t('Free');
        } else {
            return sprintf("$%.2f", floatval($this->price));
        }
    }
    public function getScreenshots()
    {
        if (is_array($this->screenshots)) {
            return $this->screenshots;
        } else {
            return array();
        }
    }
    public function getSlideshow()
    {
        if (is_array($this->slideshowImages)) {
            return $this->slideshowImages;
        } else {
            return array();
        }
    }
    public function getMarketplaceItemVersionForThisSite()
    {
        return $this->siteLatestAvailableVersion;
    }

    public function getAverageRating()
    {
        return $this->rating;
    }
    public function getVersionHistory()
    {
        return $this->versionHistory;
    }
    public function getTotalRatings()
    {
        if ($this->totalRatings) {
            return $this->totalRatings;
        } else {
            return 0;
        }
    }
    public function getRemoteReviewsURL()
    {
        return $this->reviewsURL;
    }
    public function getRemoteCollectionID()
    {
        return $this->cID;
    }
    public function getReviewBody()
    {
        return $this->reviewBody;
    }
    public function getLargeThumbnail()
    {
        if ($this->largethumbnail) {
            return $this->largethumbnail;
        } else {
            $screenshots = $this->getScreenshots();

            return $screenshots[0];
        }
    }
    public function getRemoteURL()
    {
        return $this->url;
    }
    public function getRemoteHelpURL()
    {
        return $this->helpURL;
    }
    public function getProductBlockID()
    {
        return $this->productBlockID;
    }
    public function getFivePackProductBlockID()
    {
        return $this->fivePackProductBlockID;
    }
    public function getRemoteFileURL()
    {
        return $this->file;
    }
    public function getRemoteIconURL()
    {
        return $this->icon;
    }
    public function getRemoteListIconURL()
    {
        return $this->listicon;
    }
    public function isLicensedToSite()
    {
        return $this->islicensed;
    }
    public function purchaseRequired()
    {
        if ($this->price == '' || $this->price == '0' || $this->price == '0.00') {
            return false;
        } elseif ($this->isLicensedToSite()) {
            return false;
        } else {
            return true;
        }
    }

    public function getVersion()
    {
        return $this->pkgVersion;
    }

    public function downloadUpdate()
    {
        $pkg = Package::getByHandle($this->getHandle());

        $fileURL = $this->getRemoteFileURL();
        if (empty($fileURL)) {
            return array(Package::E_PACKAGE_NOT_FOUND);
        }

        $file = Marketplace::downloadRemoteFile($this->getRemoteFileURL());
        if (is_object($file)) {
            return $file; // error
        }

        $r = $pkg->backup();
        if (is_object($r) && $r instanceof Error) {
            return $r;
        }

        $pkg = $r;

        try {
            $am = new PackageArchive($this->getHandle());
            $am->install($file, true);
        } catch (Exception $e) {
            $pkg->restore();
            $error = \Core::make('error');
            $error->add($e);
            return $error;
        }
    }

    public function download()
    {
        $file = Marketplace::downloadRemoteFile($this->getRemoteFileURL());
        if ($file instanceof ErrorList) {
            return $file;
        } else {
            try {
                $am = new PackageArchive($this->getHandle());
                $am->install($file, true);
            } catch (Exception $e) {
                $error = \Core::make('error');
                $error->add($e);
                return $e;
            }
        }
    }

    public function enableFreeLicense()
    {
        $fh = Loader::helper('file');
        $dbConfig = \Core::make('config/database');
        $csToken = $dbConfig->get('concrete.marketplace.token');
        $csiURL = urlencode(\Core::getApplicationURL());
        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.item_free_license');
        $url .= "?mpID=" . $this->mpID . "&csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
        $fh->getContents($url);
    }

    protected static function getRemotePackageObject($method, $identifier)
    {
        $fh = Loader::helper('file');

        // Retrieve the URL contents
        $dbConfig = \Core::make('config/database');
        $csToken = $dbConfig->get('concrete.marketplace.token');
        $csiURL = urlencode(\Core::getApplicationURL());

        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.item_information');
        $url .= "?" . $method . "=" . $identifier . "&csToken={$csToken}&csiURL=" . $csiURL . "&csiVersion=" . APP_VERSION;
        $json = $fh->getContents($url);

        try {
            // Parse the returned XML file
            $obj = @Loader::helper('json')->decode($json);
            if (is_object($obj)) {
                $mi = new self();
                $mi->setPropertiesFromJSONObject($obj);
                if ($mi->getMarketplaceItemID() > 0) {
                    return $mi;
                }
            }
        } catch (Exception $e) {
            throw new Exception(t('Unable to connect to marketplace to retrieve item'));
        }
    }

    /**
     * @return \Concrete\Core\Marketplace\RemoteItem;
     *
     * @param $mpID
     *
     * @throws Exception
     */
    public static function getByHandle($mpHandle)
    {
        return self::getRemotePackageObject('mpHandle', $mpHandle);
    }

    /**
     * @return \Concrete\Core\Marketplace\RemoteItem;
     *
     * @param $mpID
     *
     * @throws Exception
     */
    public static function getByID($mpID)
    {
        return self::getRemotePackageObject('mpID', $mpID);
    }
}
