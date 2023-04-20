<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Component\UserSelectInstanceFactory;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Utility\Service\Validation\Numbers;

class UserSelector
{
    /**
     * The application container instance.
     *
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Application\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Build the HTML to be placed in a page to choose a user using a popup dialog.
     *
     * @param string $fieldName the name of the field
     * @param int|false $uID the ID of the user to be initially selected
     *
     * @return string
     *
     * @example
     * <code>
     *     $userSelector->selectUser('userID', USER_SUPER_ID); // prints out the admin user and makes it changeable.
     * </code>.
     */
    public function selectUser($fieldName, $uID = false)
    {
        $request = $this->app->make(Request::class);
        if ($request->request->has($fieldName)) {
            $selectedUID = $request->request->get($fieldName);
        } elseif ($request->query->has($fieldName)) {
            $selectedUID = $request->query->get($fieldName);
        } else {
            $selectedUID = $uID;
        }

        if ($selectedUID && $this->app->make(Numbers::class)->integer($selectedUID, 1)) {
            $userInfo = $this->app->make(UserInfoRepository::class)->getByID((int) $selectedUID);
        } else {
            $userInfo = null;
        }

        $selectedUID = $userInfo ? $userInfo->getUserID() : 0;

        $permissions = new Checker();
        if ($permissions->canAccessUserSearch()) {
            $identifier = $this->app->make(Identifier::class)->getString(32);
            $chooseText = t('Choose a User');

            $html = <<<EOL
<div data-concrete-user-input="{$identifier}">
    <concrete-user-input :user-id="{$selectedUID}" choose-text="{$chooseText}" input-name="{$fieldName}"></concrete-user-input>
</div>
<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-concrete-user-input="{$identifier}"]',
            components: config.components
        })
    })
});
</script>
EOL;
        } else {
            // Read only
            $uAvatar = null;
            if ($userInfo) {
                $uName = $userInfo->getUserDisplayName();
                $a = $userInfo->getUserAvatar();
                if ($a) {
                    $uAvatar = $a->getPath();
                }
            } else {
                $uName = t('(None Selected)');
            }
            if (!$uAvatar) {
                $uAvatar = $this->app->make('config')->get('concrete.icons.user_avatar.default');
            }

            $html = <<<EOL
<div class="ccm-item-selector-group">
    <input type="hidden" name="{$fieldName}" value="{$selectedUID}">
    <div class="ccm-item-selector-loaded">
        <div class="btn btn-secondary">
            <span><img src="{$uAvatar}" alt="admin" class="u-avatar"></span>
            <span class="ccm-item-selector-title">{$uName}</span>
        </div>
    </div>
</div>
EOL;
        }

        return $html;
    }

    /**
     * Build the HTML to be placed in a page to choose a user using a select with users pupulated dynamically with ajax requests.
     *
     * @param string $fieldName the name of the field
     * @param int|false $uID the ID of the user to be initially selected
     * @param string $labelFormat The format of the results. Valid options are
     * 'auto', 'username', 'email', 'username_email'
     * 'auto': Use username and email if username is supported in the site. Otherwise use email.
     * 'username': Use username only
     * 'email': Use email only
     * 'username_email': Username and email.
     * @param bool $includeAvatar Whether to include user avatar in results.
     * @return string
     *
     * @example
     * <code>
     *     $userSelector->quickSelect('userID', USER_SUPER_ID); // prints out the admin user and makes it changeable.
     * </code>.
     *
     * @noinspection DuplicatedCode
     */
    public function quickSelect($fieldName, $uID = false, $labelFormat = UserSelectInstanceFactory::LABEL_FORMAT_AUTO, $includeAvatar = true)
    {
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        /** @var UserSelectInstanceFactory $userSelectInstanceFactory */
        $userSelectInstanceFactory = $this->app->make(UserSelectInstanceFactory::class);
        $userSelectInstance = $userSelectInstanceFactory->createInstance($labelFormat, $includeAvatar);

        if ($request->request->has($fieldName)) {
            $selectedUserId = $request->request->get($fieldName);
        } elseif ($request->query->has($fieldName)) {
            $selectedUserId = $request->query->get($fieldName);
        } else {
            $selectedUserId = $uID;
        }

        if ($selectedUserId && $this->app->make(Numbers::class)->integer($selectedUserId, 1)) {
            $userInfo = $this->app->make(UserInfoRepository::class)->getByID((int) $selectedUserId);
        } else {
            $userInfo = null;
        }

        $selectedUserId = $userInfo ? $userInfo->getUserID() : 'null';
        $identifier = $this->app->make(Identifier::class)->getString(32);
        $html = <<<EOL
<div data-concrete-select-user-input="{$identifier}">
    <concrete-user-select 
    access-token="{$userSelectInstance->getAccessToken()}" 
    label-format="{$userSelectInstance->getLabelFormatPropValue()}"
    :include-avatar="{$userSelectInstance->getIncludeAvatarPropValue()}" 
    :user-id="{$selectedUserId}" 
    input-name="{$fieldName}"
    ></concrete-user-select>
</div>
<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-concrete-select-user-input="{$identifier}"]',
            components: config.components
        })
    })
});
</script>
EOL;
        return $html;
    }

    /**
     * Build the HTML to be placed in a page to choose multiple users using a popup dialog.
     *
     * @param string $fieldName the name of the field
     * @param \Concrete\Core\Entity\User\User[]|\Concrete\Core\User\UserInfo[]|int[]\Traversable $users The users to be initially selected
     *
     * @return string
     */
    public function selectMultipleUsers($fieldName, $users = [])
    {
        $identifier = $this->app->make(Identifier::class)->getString(32);
        $i18n = [
            'username' => t('Username'),
            'emailAddress' => t('Email Address'),
            'chooseUser' => t('Choose User'),
            'noUsers' => t('No users selected.'),
        ];
        $searchLink = $this->app->make(ResolverManagerInterface::class)->resolve(['/ccm/system/dialogs/user/search']) .
            '?multipleSelection=1';
        $valn = $this->app->make(Numbers::class);
        $userInfoRepository = $this->app->make(UserInfoRepository::class);
        $preselectedUsers = '';
        foreach ($users as $user) {
            if ($valn->integer($user)) {
                $user = $userInfoRepository->getById($user);
            }
            if (is_object($user)) {
                $preselectedUsers .= <<<EOT
<tr data-ccm-user-id="{$user->getUserID()}" class="ccm-list-record">
    <td><input type="hidden" name="{$fieldName}[]" value="{$user->getUserID()}" />{$user->getUserName()}</td>
    <td>{$user->getUserEmail()}</td>
    <td><a href="#" class="ccm-user-list-clear icon-link"><i class="fas fa-minus-circle ccm-user-list-clear-button"></i></a></td>
</tr>
EOT;
            }
        }
        $noUsersStyle = $preselectedUsers === '' ? '' : ' style="display: none"';

        return <<<EOT
<table id="ccmUserSelect-{$identifier}" class="table table-condensed" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <th>{$i18n['username']}</th>
            <th>{$i18n['emailAddress']}</th>
            <th style="width: 1px"><a class="icon-link ccm-user-select-item dialog-launch" dialog-append-buttons="true" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="{$i18n['chooseUser']}" href="{$searchLink}"><i class="fas fa-plus-circle" /></a></th>
        </tr>
    </thead>
    <tbody>
        {$preselectedUsers}
        <tr class="ccm-user-selected-item-none"{$noUsersStyle}><td colspan="3">{$i18n['noUsers']}</td></tr>
    </tbody>
</table>
<script>
$(function() {
    var container = $('#ccmUserSelect-{$identifier}'),
        noUsersRow = container.find('tr.ccm-user-selected-item-none'),
        updateNoUsers = function() {
            if (container.find('tr[data-ccm-user-id]').length === 0) {
                noUsersRow.show();
            } else {
                noUsersRow.hide();
            }
        },
        userSelectCallback = function(user) {
            var uID = user.id,
                uName = user.name,
                uEmail = user.email;
            if (container.find('tr[data-ccm-user-id=' + uID + ']').length > 0) {
                return;
            }
            noUsersRow.before($('<tr data-ccm-user-id="' + uID + '" class="ccm-list-record" />')
                .append($('<td />')
                    .text(uName)
                    .prepend($('<input type="hidden" name="{$fieldName}[]" />').val(uID))
                )
                .append($('<td />')
                    .text(uEmail)
                )
                .append($('<td><a href="#" class="ccm-user-list-clear icon-link"><i class="fas fa-minus-circle ccm-user-list-clear-button"></i></a></td>'))
            );
            updateNoUsers();
        };
    container.on('click', 'a.ccm-user-list-clear', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        updateNoUsers();
    });
    container.find('.ccm-user-select-item')
        .dialog()
        .on('click', function(e) {
            ConcreteEvent.subscribe('UserSearchDialogSelectUser.core', function(event, data) {
                jQuery.fn.dialog.closeTop()
                ConcreteEvent.unbind(event);
                if (data.users) {
                    data.users.forEach(function(user) {
                        userSelectCallback(user)
                    })
                }
            })
        })
    ;
});
</script>
EOT
            ;
    }
}
