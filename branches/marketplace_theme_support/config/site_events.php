<?

/** 
 * The syntax for Events::extend is pretty simple. There are two ways of doing it
 * If you're specifying the exact method an event should run when it happens, you need four arguments
 * <code>
 * Events::extend('event_name', 'ClassToBeInstantiated', 'StaticMethodToBeRun', 'PathToMethod');
 * </code>
 * For example: Events::extend('on_user_add', 'SpecialUserClassFoundInFile', 'setupUserJoinInfo', 'models/some_special_user_class.php');
 * If you're extending page events, a shortcut to this syntax for page-type-specific actions can 
 * be accomplished with just two parameters:
 * <code>
 * Events::extend('page_type_handle', 'event_name');
 * </code>
 * This is a shortcut to instantiating the page type's controller and running the "on_page_whatever" method specified.
 * If you don't specify ANY events for the page type, and just pass a page type, then it will be assumed that they ALL should be registered.
 * If one or more don't exist, it won't die, they just won't be run, and will fail silently
 */

Events::extendPageType('discussion', 'on_page_view');
Events::extendPageType('discussion_post');