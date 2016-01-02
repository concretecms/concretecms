<?
namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;

class Attributes extends DashboardAttributesPageController
{

	public function view()
	{
		$this->renderList(UserKey::getList(), Type::getAttributeTypeList('user'));
	}

	public function edit($akID = null)
	{
		$key = UserKey::getByID($akID);
		$this->renderEdit($key,
			\URL::to('/dashboard/users/attributes', 'view')
		);
	}

	public function update($akID = null)
	{
		$this->edit($akID);
		$key = UserKey::getByID($akID);
		$category = Category::getByHandle('user');
		$this->executeUpdate($category, $key,
			\URL::to('/dashboard/users/attributes', 'view')
		);
	}

	public function select_type($type = null)
	{
		$type = Type::getByID($type);
		$this->renderAdd($type,
			\URL::to('/dashboard/users/attributes', 'view', $id)
		);
	}

	public function add($type = null)
	{
		$this->select_type($type);
		$type = Type::getByID($type);
		$category = Category::getByHandle('user');
		$this->executeAdd($category, $type, \URL::to('/dashboard/users/attributes', 'view'));
	}

	public function delete($akID = null)
	{
		$key = UserKey::getByID($akID);
		$category = Category::getByHandle('user');
		$this->executeDelete($category, $key,
			\URL::to('/dashboard/users/attributes', 'view')
		);
	}



}
