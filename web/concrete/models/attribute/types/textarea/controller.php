<? defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('attribute/types/default/controller');
Loader::library('3rdparty/htmLawed');

class TextareaAttributeTypeController extends Concrete5_Controller_AttributeType_Textarea  {}