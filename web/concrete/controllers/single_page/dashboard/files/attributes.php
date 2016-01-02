<?
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;

class Attributes extends DashboardAttributesPageController
{

	public function view()
	{
		$this->renderList(FileKey::getList(), Type::getAttributeTypeList('file'));
	}

	public function edit($akID = null)
	{
		$key = FileKey::getByID($akID);
		$this->renderEdit($key,
			\URL::to('/dashboard/files/attributes', 'view')
		);
	}

	public function update($akID = null)
	{
		$this->edit($akID);
		$key = FileKey::getByID($akID);
		$category = Category::getByHandle('file');
		$this->executeUpdate($category, $key,
			\URL::to('/dashboard/files/attributes', 'view')
		);
	}

	public function select_type($type = null)
	{
		$type = Type::getByID($type);
		$this->renderAdd($type,
			\URL::to('/dashboard/files/attributes', 'view')
		);
	}

	public function add($type = null)
	{
		$this->select_type($type);
		$type = Type::getByID($type);
		$category = Category::getByHandle('file');
		$this->executeAdd($category, $type, \URL::to('/dashboard/files/attributes', 'view'));
	}

	public function delete($akID = null)
	{
		$key = FileKey::getByID($akID);
		$category = Category::getByHandle('file');
		$this->executeDelete($category, $key,
			\URL::to('/dashboard/files/attributes', 'view')
		);
	}



}
