<?php

namespace Concrete\Block\Testimonial;

defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Block\BlockController;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;

class Controller extends BlockController implements FileTrackableInterface
{
    /**
     * @var string[]
     */
    public $helpers = ['form'];

    /**
     * @var int
     */
    protected $btInterfaceWidth = 450;

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = true;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 560;

    /**
     * @var string[]
     */
    protected $btExportFileColumns = ['fID', 'awardImageID'];

    /**
     * @var string
     */
    protected $btTable = 'btTestimonial';

    /**
     * @var int
     */
    protected $fID;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $position;

    /**
     * @var string
     */
    protected $company;

    /**
     * @var string
     */
    protected $companyURL;

    /**
     * @var string
     */
    protected $paragraph;

    /**
     * @var int
     */
    protected $awardImageID;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Displays a quote or paragraph next to biographical information and a person's picture.");
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Testimonial');
    }

    /**
     * @return string
     */
    public function getSearchableContent()
    {
        return $this->name . "\n" . $this->position . "\n" . $this->company . "\n" . $this->paragraph;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $image = false;
        if ($this->fID) {
            $f = File::getByID($this->fID);
            if (is_object($f)) {
                $image = $this->app->make('html/image', ['f' => $f])->getTag();
                $image->alt($this->name);
            }
        }
        $this->set('image', $image);
        $awardImage = false;
        if ($this->awardImageID) {
            $f = File::getByID($this->awardImageID);
            if (is_object($f)) {
                $awardImage = $this->app->make('html/image', ['f' => $f])->getTag();
            }
        }
        $this->set('awardImage', $awardImage);
    }

    /**
     * @param array<string,mixed> $args
     *
     * @return void
     */
    public function save($args)
    {
        $args['fID'] = (isset($args['fID']) && $args['fID'] != '') ? $args['fID'] : 0;
        $args['awardImageID'] = (isset($args['awardImageID']) && $args['awardImageID'] != '') ? $args['awardImageID'] : 0;
        parent::save($args);
    }

    /**
     * @return int[]
     */
    public function getUsedFiles()
    {
        if ($this->fID) {
            return [$this->fID];
        }

        return [];
    }
}
