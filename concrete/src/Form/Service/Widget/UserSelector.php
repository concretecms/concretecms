<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\View\View;

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
        $v = View::getRequestInstance();
        $v->requireAsset('core/users');

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
        $selectedUID = $userInfo ? $userInfo->getUserID() : null;

        $permissions = new Checker();
        if ($permissions->canAccessUserSearch()) {
            $identifier = $this->app->make(Identifier::class)->getString(32);
            $args = ['inputName' => $fieldName];
            if ($userInfo) {
                $args['uID'] = $userInfo->getUserID();
            }
            $args = json_encode($args);
            $html = <<<EOL
<div data-user-selector="{$identifier}"></div>
<script>
$(function() {
    $('[data-user-selector={$identifier}]').concreteUserSelector({$args});
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
<div class="ccm-item-selector">
    <div class="ccm-item-selector-item-selected">
        <input type="hidden" name="{$fieldName}" value="{$selectedUID}">
        <div class="ccm-item-selector-item-selected-thumbnail">
            <img src="{$uAvatar}" alt="admin" class="u-avatar">
        </div>
        <div class="ccm-item-selector-item-selected-title">{$uName}</div>
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
     * @param array $miscFields additional fields appended to the hidden input element (a hash array of attributes name => value), possibly including 'class'
     *
     * @return string
     *
     * @example
     * <code>
     *     $userSelector->quickSelect('userID', USER_SUPER_ID); // prints out the admin user and makes it changeable.
     * </code>.
     */
    public function quickSelect($fieldName, $uID = false, $miscFields = [])
    {
        $v = View::getRequestInstance();
        $v->requireAsset('selectize');

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
        $selectedUID = $userInfo ? $userInfo->getUserID() : null;

        $valt = $this->app->make('token');
        $token = $valt->generate('quick_user_select_' . $fieldName);

        $identifier = $this->app->make(Identifier::class)->getString(32);

        $selectizeOptions = [
            'valueField' => 'value',
            'labelField' => 'label',
            'searchField' => ['label'],
            'maxItems' => 1,
        ];
        if ($userInfo) {
            $selectizeOptions += [
                'options' => [
                    [
                        'label' => h($userInfo->getUserDisplayName()),
                        'value' => $selectedUID,
                    ],
                ],
                'items' => [
                    $selectedUID,
                ],
            ];
        }
        $selectizeOptions = json_encode($selectizeOptions);
        $input = $this->app->make('helper/form')->hidden($fieldName, '', $miscFields);
        $ajaxUrlBase = json_encode(REL_DIR_FILES_TOOLS_REQUIRED . '/users/autocomplete?key=' . rawurlencode($fieldName) . '&token=' . rawurldecode($token));

        return <<<EOT
<span id="ccm-quick-user-selector-{$identifier}" class="ccm-quick-user-selector">{$input}</span>
<script>
$(function () {
    var options = {$selectizeOptions};
    options.load = function(query, callback) {
        if (!query.length) {
            return callback();
        }
        $.ajax({
            url: {$ajaxUrlBase} + '&term=' + encodeURIComponent(query),
            type: 'GET',
			dataType: 'json',
            error: function() {
                callback();
            },
            success: function(res) {
                callback(res);
            }
        });
    };
    $('#ccm-quick-user-selector-{$identifier} input')
        .unbind()
        .selectize(options)
    ;
});
</script>
EOT
        ;
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
        $searchLink = $this->app->make(ResolverManagerInterface::class)->resolve(['/ccm/system/dialogs/user/search']);
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
    <td><a href="#" class="ccm-user-list-clear icon-link"><i class="fa fa-minus-circle ccm-user-list-clear-button"></i></a></td>
</tr>
EOT
                ;
            }
        }
        $noUsersStyle = $preselectedUsers === '' ? '' : ' style="display: none"';

        return <<<EOT
<table id="ccmUserSelect-{$identifier}" class="table table-condensed" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <th>{$i18n['username']}</th>
            <th>{$i18n['emailAddress']}</th>
            <th style="width: 1px"><a class="icon-link ccm-user-select-item dialog-launch" dialog-append-buttons="true" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="{$i18n['chooseUser']}" href="{$searchLink}"><i class="fa fa-plus-circle" /></a></th>
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
        userSelectCallback = function(e, data) {
            e.stopPropagation();
            var uID = data.uID,
                uName = data.uName,
                uEmail = data.uEmail;
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
                .append($('<td><a href="#" class="ccm-user-list-clear icon-link"><i class="fa fa-minus-circle ccm-user-list-clear-button"></i></a></td>'))
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
            ConcreteEvent.subscribe('UserSearchDialogSelectUser', userSelectCallback)
        })
    ;
    ConcreteEvent.subscribe('UserSearchDialogAfterSelectUser', function(e) {
        ConcreteEvent.unsubscribe('UserSearchDialogSelectUser');
        jQuery.fn.dialog.closeTop();
    });
});
</script>
EOT
        ;
    }
}
