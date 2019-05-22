<?php
namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Application\Application;

defined('C5_EXECUTE') or die("Access Denied.");

class Factory
{
    /**
     * @var Application 
     */
    protected $app;
    
    protected $bindings = [];
    
    public function registerClass($handle, $class)
    {
        $this->bindings[$handle] = $class;
    }
    
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->registerClass('group', GroupEntity::class);
        $this->registerClass('user', UserEntity::class);
        $this->registerClass('page_owner', PageOwnerEntity::class);
        $this->registerClass('file_uploader', FileUploaderEntity::class);
        $this->registerClass('conversation_message_author', ConversationMessageAuthorEntity::class);
        $this->registerClass('group_combination', GroupCombinationEntity::class);
        $this->registerClass('group_set', GroupSetEntity::class);
        $this->registerClass('site_group', SiteGroupEntity::class);
    }

    public function createEntity(Type $type)
    {
        if (isset($this->bindings[$type->getAccessEntityTypeHandle()])) {
            $class = $this->bindings[$type->getAccessEntityTypeHandle()];
        } else {
            // We're probably looking for a legacy permission access entity type.
            // @deprecated
            $class = overrideable_core_class('Core\\Permission\\Access\\Entity\\'
                . $this->app->make('helper/text')->camelcase($type->getAccessEntityTypeHandle()) . 'Entity',
                DIRNAME_CLASSES . '/Permission/Access/Entity/'
                . $this->app->make('helper/text')->camelcase($type->getAccessEntityTypeHandle()) . 'Entity.php',
                $type->getPackageHandle()
            );
        }
        return $this->app->make($class);
    }

}
