<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Permission\Checker;
use Concrete\Core\User\Avatar\EmptyAvatar;
use UserInfo;
use URL;
use Loader;

class UserSelector
{
    /**
     * Creates form fields and JavaScript user chooser for choosing a user. For use with inclusion in blocks and addons.
     * <code>
     *     $dh->selectUser('userID', '1'); // prints out the admin user and makes it changeable.
     * </code>.
     *
     * @param int $uID
     */
    public function selectUser($fieldName, $uID = false)
    {
        $v = \View::getInstance();
        $v->requireAsset('core/users');
        $permissions = new Checker();

        $selectedUID = 0;
        if (isset($_REQUEST[$fieldName])) {
            $selectedUID = intval($_REQUEST[$fieldName]);
        } else {
            if ($uID > 0) {
                $selectedUID = $uID;
            }
        }

        if ($selectedUID) {
            $args = "{'inputName': '{$fieldName}', 'uID': {$selectedUID}}";
        } else {
            $args = "{'inputName': '{$fieldName}'}";
        }

        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        if ($permissions->canAccessUserSearch()) {

            $html = <<<EOL
            <div data-user-selector="{$identifier}"></div>
            <script type="text/javascript">
            $(function() {
                $('[data-user-selector={$identifier}]').concreteUserSelector({$args});
            });
            </script>
EOL;

        } else {

            // Read only
            $ui = false;
            if ($selectedUID) {
                $ui = UserInfo::getByID($selectedUID);
            }

            if (is_object($ui)) {
                $uName = $ui->getUserDisplayName();
                $uAvatar = $ui->getUserAvatar()->getPath();
            } else {
                $uName = t('(None Selected)');
                $uAvatar = \Config::get('concrete.icons.user_avatar.default');
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


    public function quickSelect($key, $val = false, $args = array())
    {
        $v = \View::getInstance();
        $v->requireAsset('selectize');
        $form = Loader::helper('form');
        $valt = Loader::helper('validation/token');
        $token = $valt->generate('quick_user_select_' . $key);

        $selectedUID = 0;
        if (isset($_REQUEST[$key])) {
            $selectedUID = $_REQUEST[$key];
        } else {
            if ($val > 0) {
                $selectedUID = $val;
            }
        }

        $uName = '';
        if ($selectedUID > 0) {
            $ui = UserInfo::getByID($selectedUID);
            $uName = $ui->getUserDisplayName();
        }

        $html = "
		<script type=\"text/javascript\">
		$(function () {
			$('.ccm-quick-user-selector input').unbind().selectize({
                valueField: 'value',
                labelField: 'label',
                searchField: ['label'],";

        if ($val) {
            $html .= "options: [{'label': '" . h($uName) . "', 'value': " . intval($selectedUID) . "}],
				items: [" . intval($selectedUID) . "],";
        }

        $html .= "maxItems: 1,
                load: function(query, callback) {
                    if (!query.length) return callback();
                    $.ajax({
                        url: '" . REL_DIR_FILES_TOOLS_REQUIRED . "/users/autocomplete?key=" . $key . "&token=" . $token . "&term=' + encodeURIComponent(query),
                        type: 'GET',
						dataType: 'json',
                        error: function() {
                            callback();
                        },
                        success: function(res) {
                            callback(res);
                        }
                    });
                }
		    });
		});
		</script>";
        $html .= '<span class="ccm-quick-user-selector">'.$form->hidden($key, '', $args).'</span>';

        return $html;
    }

    public function selectMultipleUsers($fieldName, $users = array())
    {
        $html = '';
        $html .= '<table id="ccmUserSelect' . $fieldName . '" class="table table-condensed" cellspacing="0" cellpadding="0" border="0">';
        $html .= '<tr>';
        $html .= '<th>' . t('Username') . '</th>';
        $html .= '<th>' . t('Email Address') . '</th>';
        $html .= '<th style="width: 1px"><a class="icon-link ccm-user-select-item dialog-launch" dialog-append-buttons="true" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' . t('Choose User') . '" href="'. URL::to('/ccm/system/dialogs/user/search') . '"><i class="fa fa-plus-circle" /></a></th>';
        $html .= '</tr><tbody id="ccmUserSelect' . $fieldName . '_body" >';
        foreach ($users as $ui) {
            $html .= '<tr id="ccmUserSelect' . $fieldName . '_' . $ui->getUserID() . '" class="ccm-list-record">';
            $html .= '<td><input type="hidden" name="' . $fieldName . '[]" value="' . $ui->getUserID() . '" />' . $ui->getUserName() . '</td>';
            $html .= '<td>' . $ui->getUserEmail() . '</td>';
            $html .= '<td><a href="javascript:void(0)" class="ccm-user-list-clear icon-link"><i class="fa fa-minus-circle ccm-user-list-clear-button"></i></a>';
            $html .= '</tr>';
        }
        if (count($users) == 0) {
            $html .= '<tr class="ccm-user-selected-item-none"><td colspan="3">' . t('No users selected.') . '</td></tr>';
        }
        $html .= '</tbody></table><script type="text/javascript">
		$(function() {
			$("#ccmUserSelect' . $fieldName . ' .ccm-user-select-item").dialog();
			$("a.ccm-user-list-clear").click(function() {
				$(this).parents(\'tr\').remove();
			});

			$("#ccmUserSelect' . $fieldName . ' .ccm-user-select-item").on(\'click\', function() {
				ConcreteEvent.subscribe(\'UserSearchDialogSelectUser\', function(e, data) {
					var uID = data.uID, uName = data.uName, uEmail = data.uEmail;
					e.stopPropagation();
					$("tr.ccm-user-selected-item-none").hide();
					if ($("#ccmUserSelect' . $fieldName . '_" + uID).length < 1) {
						var html = "";
						html += "<tr id=\"ccmUserSelect' . $fieldName . '_" + uID + "\" class=\"ccm-list-record\"><td><input type=\"hidden\" name=\"' . $fieldName . '[]\" value=\"" + uID + "\" />" + uName + "</td>";
						html += "<td>" + uEmail + "</td>";
						html += "<td><a href=\"javascript:void(0)\" class=\"ccm-user-list-clear icon-link\"><i class=\"fa fa-minus-circle ccm-user-list-clear-button\" /></a>";
						html += "</tr>";
						$("#ccmUserSelect' . $fieldName . '_body").append(html);
					}
					$("a.ccm-user-list-clear").click(function() {
						$(this).parents(\'tr\').remove();
					});
				});
				ConcreteEvent.subscribe(\'UserSearchDialogAfterSelectUser\', function(e) {
					jQuery.fn.dialog.closeTop();
				});
			});
		});

		</script>';

        return $html;
    }
}
