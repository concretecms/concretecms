<?php

namespace Concrete\Block\ExternalForm;

use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    /**
     * @var string[]
     */
    public $helpers = ['file', 'form'];

    /**
     * @var string
     */
    protected $btTable = 'btExternalForm';

    /**
     * @var int
     */
    protected $btInterfaceWidth = 420;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 350;

    /**
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * @var string|null
     */
    protected $filename;

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Include external forms in the filesystem and place them on pages.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('External Form');
    }

    /**
     * @return string|null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string|null
     */
    public function getExternalFormFilenamePath()
    {
        $filename = null;
        if ($this->filename) {
            if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL . '/' . $this->filename)) {
                $filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL . '/' . $this->filename;
            } elseif (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE . '/' . $this->filename)) {
                $filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE . '/' . $this->filename;
            }
        }

        return $filename;
    }

    /**
     * @param string $method
     * @param array<string,mixed> $parameters
     *
     * @return bool|void
     */
    public function isValidControllerTask($method, $parameters = [])
    {
        $controller = $this->getController();
        if ($controller) {
            if (method_exists($controller, $method)) {
                return true;
            }

            return parent::isValidControllerTask($method, $parameters);
        }
    }

    /**
     * @param array<string,mixed> $args
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool|\Concrete\Core\Error\ErrorList\ErrorList|mixed|object
     */
    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');
        if (!$args['filename']) {
            $e->add(t('You must specify an external form.'));
        } else {
            $filename = $args['filename'];
            if (substr($filename, -4) === '.php') {
                // Let's run our regular expression check on everything BEFORE ".php"
                $filenameToCheck = substr($filename, 0, -4);
            } else {
                $filenameToCheck = $filename; // We just check the entirety of what's passed in.
            }

            if (!preg_match('/^[A-Za-z0-9_-]+$/i', $filenameToCheck)) {
                $e->add(
                    t('External forms may only contain letters, numbers, dashes and underscores.')
                );
            }
        }

        return $e;
    }

    /**
     * @param string $method
     * @param array<string,mixed> $parameters
     *
     * @return mixed|void
     */
    public function runAction($method, $parameters = [])
    {
        $controller = $this->getController();
        if ($controller) {
            $controller->runAction($method, $parameters);
            foreach ($controller->getSets() as $key => $value) {
                $this->set($key, $value);
            }
        }

        return parent::runAction($method, $parameters);
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->set('filenames', $this->getFormList());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return array<int,string>
     */
    public function getFormList()
    {
        $forms = [];
        $fh = $this->app->make('helper/file');

        if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL)) {
            $forms = array_merge(
                $forms,
                $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL, ['controller'])
            );
        }
        if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE)) {
            $forms = array_merge(
                $forms,
                $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE, ['controller'])
            );
        }

        return $forms;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $this->set('filenames', $this->getFormList());
    }

    /**
     * Return the external forms controller.
     *
     * @return \Concrete\Core\Controller\AbstractController|null
     */
    protected function getController()
    {
        try {
            $class = camelcase(substr($this->filename, 0, strrpos($this->filename, '.php')));
            $cl = $this->app->make(
                overrideable_core_class(
                    'Block\\ExternalForm\\Form\\Controller\\' . $class,
                    DIRNAME_BLOCKS . '/external_form/form/controller/' . $this->filename
                )
            );
            $cl->bID = $this->bID;

            return $cl;
        } catch (\Exception $e) {
            return null;
        }
    }
}
