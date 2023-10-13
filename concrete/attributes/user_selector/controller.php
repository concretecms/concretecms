<?php
namespace Concrete\Attribute\UserSelector;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\Attribute\OpenApiSpecifiableInterface;
use Concrete\Core\Api\Attribute\SupportsAttributeValueFromJsonInterface;
use Concrete\Core\Api\Fractal\Transformer\UserTransformer;
use Concrete\Core\Api\OpenApi\SpecProperty;
use Concrete\Core\Api\Resources;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;

class Controller extends AttributeTypeController implements
    OpenApiSpecifiableInterface,
    SupportsAttributeValueFromJsonInterface,
    ApiResourceValueInterface
{
    protected $searchIndexFieldDefinition = [
        'type' => 'integer',
        'options' => ['default' => 0, 'notnull' => false],
    ];

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('user');
    }

    public function getAttributeValueClass()
    {
        return NumberValue::class;
    }

    public function form()
    {
        $value = null;
        if ($this->request->isPost()) {
            $value = $this->post('value');
        } else {
            if (is_object($this->attributeValue)) {
                $value = $this->getAttributeValue()->getValue();
            }
            if (!$value) {
                if ($this->request->query->has($this->attributeKey->getAttributeKeyHandle())) {
                    $value = $this->createAttributeValue(
                        (int)$this->request->query->get($this->attributeKey->getAttributeKeyHandle())
                    );
                }
            }
        }
        $this->set('value', $value);
        $this->set('user_selector', $this->app->make('helper/form/user_selector'));
    }

    public function getDisplayValue()
    {
        $uID = $this->getAttributeValue()->getValue();
        $ui = UserInfo::getByID($uID);
        if (is_object($ui)) {
            return '<a href="'.$ui->getUserPublicProfileUrl().'">'.$ui->getUserName().'</a>';
        }
    }

    public function getPlainTextValue()
    {
        $uID = $this->getAttributeValue()->getValue();
        $user = User::getByUserID($uID);
        if (is_object($user)) {
            return $user->getUserName();
        }
    }

    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        if ($value instanceof User) {
            $value = $value->getUserID();
        }
        $av->setValue($value);

        return $av;
    }

    public function searchForm($list)
    {
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), (int)$this->request('value'));
        return $list;
    }

    public function search()
    {
        $user_selector = $this->app->make('helper/form/user_selector');
        echo $user_selector->selectUser($this->field('value'), $this->request('value'));
    }

    public function getSearchIndexValue()
    {
        return $this->attributeValue->getValue();
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        if (isset($data['value'])) {
            return $this->createAttributeValue((int) $data['value']);
        }
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $user = User::getByUserID($akv->value);
            if (is_object($user)) {
                return $user->getUserID();
            }
        }
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        if (is_object($this->attributeValue)) {
            $uID = $this->getAttributeValue()->getValue();
            $user = User::getByUserID($uID);
            $avn = $akn->addChild('value', $user->getUserID());
        }
    }

    public function validateForm($p)
    {
        return $p['value'] != false;
    }

    public function validateValue()
    {
        /** @var NumberValue $val */
        $val = $this->getAttributeValue()->getValue();
        /** @var ErrorList $error */
        $error = $this->app->make('helper/validation/error');
        /** @var UserInfoRepository $repository */
        $repository = $this->app->make(UserInfoRepository::class);
        $user = $repository->getByID($val);
        if (!$user) {
            $error->add(new FieldNotPresentError(new AttributeField($this->getAttributeKey())));
        }

        return $error;
    }

    public function getOpenApiSpecProperty(Key $key): SpecProperty
    {
        return new SpecProperty(
            $key->getAttributeKeyHandle(),
            $key->getAttributeKeyDisplayName(),
            'integer'
        );
    }

    public function createAttributeValueFromNormalizedJson($json)
    {
        return $this->createAttributeValue($json);
    }

    public function getApiValueResource(): ?ResourceInterface
    {
        if ($this->getAttributeValue()) {
            $uID = $this->getAttributeValue()->getValue();
            if ($uID) {
                $user = $this->app->make(UserInfoRepository::class)->getByID($uID);
                return new Item($user, new UserTransformer(), Resources::RESOURCE_USERS);
            }
        }
        return null;
    }



}
