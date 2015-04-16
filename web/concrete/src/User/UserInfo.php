<?php
namespace Concrete\Core\User;

use Concrete\Core\File\StorageLocation\StorageLocation;
use \Concrete\Core\Foundation\Object;
use Concrete\Core\User\Event\AddUser;
use Concrete\Core\User\PrivateMessage\Limit;
use Concrete\Core\User\PrivateMessage\Mailbox as UserPrivateMessageMailbox;
use Imagine\Image\ImageInterface;
use Concrete\Flysystem\AdapterInterface;
use Concrete\Core\Mail\Importer\MailImporter;
use Loader;
use View;
use Config;
use Events;
use User as ConcreteUser;
use UserAttributeKey;
use \Concrete\Core\Attribute\Value\UserValue as UserAttributeValue;
use \Concrete\Core\Attribute\Key\Key as AttributeKey;
use Group;
use \Hautelook\Phpass\PasswordHash;
use Session;
use Core;

class UserInfo extends Object implements \Concrete\Core\Permission\ObjectInterface
{
    public function __toString()
    {
        return 'UserInfo: ' . $this->getUserID();
    }

    public function getPermissionObjectIdentifier() {return $this->uID;}

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\UserInfoResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return false;
    }
    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    /* magic method for user attributes. This is db expensive but pretty damn cool */
    // so if the attrib handle is "my_attribute", then get the attribute with $ui->getUserMyAttribute(), or "uFirstName" become $ui->getUserUfirstname();
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 7) == 'getUser') {
            $nm = preg_replace('/(?!^)[[:upper:]]/','_\0', $nm);
            $nm = strtolower($nm);
            $nm = str_replace('get_user_', '', $nm);

            return $this->getAttribute($nm);
        }
    }

    /**
     * returns the UserInfo object for a give user's uID
     * @param int $uID
     * @return UserInfo
     */
    public static function getByID($uID)
    {
        return UserInfo::get('where uID = ?', $uID);
    }

    /**
     * returns the UserInfo object for a give user's username
     * @param string $uName
     * @return UserInfo
     */
    public static function getByUserName($uName)
    {
        return UserInfo::get('where uName = ?', $uName);
    }

    /**
     * returns the UserInfo object for a give user's email address
     * @param string $uEmail
     * @return UserInfo
     */
    public static function getByEmail($uEmail)
    {
        return UserInfo::get('where uEmail = ?', $uEmail);
    }

    /**
     * @param string $uHash
     * @param boolean $unredeemedHashesOnly
     * @return UserInfo
     */
    public static function getByValidationHash($uHash, $unredeemedHashesOnly = true)
    {
        $db = Loader::db();
        if ($unredeemedHashesOnly) {
            $uID = $db->GetOne("select uID from UserValidationHashes where uHash = ? and uDateRedeemed = 0", array($uHash));
        } else {
            $uID = $db->GetOne("select uID from UserValidationHashes where uHash = ?", array($uHash));
        }
        if ($uID) {
            $ui = UserInfo::getByID($uID);

            return $ui;
        }
    }

    public function getUserBadges()
    {
        $db = Loader::db();
        $groups = array();
        $r = $db->Execute('select g.gID from Groups g inner join UserGroups ug on g.gID = ug.gID where g.gIsBadge = 1 and ug.uID = ? order by ugEntered desc', array($this->getUserID()));
        while ($row = $r->FetchRow()) {
            $groups[] = Group::getByID($row['gID']);
        }

        return $groups;
    }

    private function get($where, $var)
    {
        $db = Loader::db();
        $q = "select Users.uID, Users.uLastLogin, Users.uLastIP, Users.uIsValidated, Users.uPreviousLogin, Users.uIsFullRecord, Users.uNumLogins, Users.uDateAdded, Users.uIsActive, Users.uDefaultLanguage, Users.uLastOnline, Users.uHasAvatar, Users.uName, Users.uEmail, Users.uPassword, Users.uTimezone, Users.uLastPasswordChange from Users " . $where;
        $r = $db->query($q, array($var));
        if ($r && $r->numRows() > 0) {
            $ui = new UserInfo();
            $row = $r->fetchRow();
            $ui->setPropertiesFromArray($row);
            $r->free();
        }

        if (is_object($ui)) {
            return $ui;
        }
    }

    /**
     * @param array $data
     * @return UserInfo
     */
    public static function add($data)
    {

        $uae = new AddUser($data);
        $uae = Events::dispatch('on_before_user_add', $uae);
        if (!$uae->proceed()) {
            return false;
        }

        $db = Loader::db();
        $dh = Loader::helper('date');
        $uDateAdded = $dh->getOverridableNow();
        $hasher = new PasswordHash(Config::get('concrete.user.password.hash_cost_log2'), Config::get('concrete.user.password.hash_portable'));

        if ($data['uIsValidated'] == 1) {
            $uIsValidated = 1;
        } elseif (isset($data['uIsValidated']) && $data['uIsValidated'] == 0) {
            $uIsValidated = 0;
        } else {
            $uIsValidated = -1;
        }

        if (isset($data['uIsFullRecord']) && $data['uIsFullRecord'] == 0) {
            $uIsFullRecord = 0;
        } else {
            $uIsFullRecord = 1;
        }

        $password_to_insert = $data['uPassword'];
        $hash = $hasher->HashPassword($password_to_insert);

        if (isset($data['uDefaultLanguage']) && $data['uDefaultLanguage'] != '') {
            $uDefaultLanguage = $data['uDefaultLanguage'];
        }
        $v = array($data['uName'], $data['uEmail'], $hash, $uIsValidated, $uDateAdded, $uDateAdded, $uIsFullRecord, $uDefaultLanguage, 1);
        $r = $db->prepare("insert into Users (uName, uEmail, uPassword, uIsValidated, uDateAdded, uLastPasswordChange, uIsFullRecord, uDefaultLanguage, uIsActive) values (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $res = $db->execute($r, $v);
        if ($res) {
            $newUID = $db->Insert_ID();
            $ui = UserInfo::getByID($newUID);

            if (is_object($ui)) {

                $uo = $ui->getUserObject();
                $groupControllers = \Group::getAutomatedOnRegisterGroupControllers($uo);
                foreach($groupControllers as $ga) {
                    if ($ga->check($uo)) {
                        $uo->enterGroup($ga->getGroupObject());
                    }
                }

                // run any internal event we have for user add
                $ue = new \Concrete\Core\User\Event\UserInfoWithPassword($ui);
                $ue->setUserPassword($data['uPassword']);
                Events::dispatch('on_user_add', $ue);
            }

            return $ui;
        }
    }

    public function addSuperUser($uPasswordEncrypted, $uEmail)
    {
        $db = Loader::db();
        $dh = Loader::helper('date');
        $uDateAdded = $dh->getOverridableNow();

        $v = array(USER_SUPER_ID, USER_SUPER, $uEmail, $uPasswordEncrypted, 1, $uDateAdded, $uDateAdded);
        $r = $db->prepare("insert into Users (uID, uName, uEmail, uPassword, uIsActive, uDateAdded, uLastPasswordChange) values (?, ?, ?, ?, ?, ?, ?)");
        $res = $db->execute($r, $v);
        if ($res) {
            $newUID = $db->Insert_ID();

            return UserInfo::getByID($newUID);
        }
    }

    /**
     * Deletes a user
     * @return void
     */
    public function delete()
    {
        // we will NOT let you delete the admin user
        if ($this->uID == USER_SUPER_ID) {
            return false;
        }

        // run any internal event we have for user deletion

        $ue = new \Concrete\Core\User\Event\DeleteUser($this);
        $ue = Events::dispatch('on_user_delete', $ue);
        if (!$ue->proceed()) {
            return false;
        }

        $db = Loader::db();

        $r = $db->Execute('select avID, akID from UserAttributeValues where uID = ?', array($this->uID));
        while ($row = $r->FetchRow()) {
            $uak = UserAttributeKey::getByID($row['akID']);
            $av = $this->getAttributeValueObject($uak);
            if (is_object($av)) {
                $av->delete();
            }
        }

        $r = $db->query("DELETE FROM UserSearchIndexAttributes WHERE uID = ?",array(intval($this->uID)) );

        $r = $db->query("DELETE FROM UserGroups WHERE uID = ?",array(intval($this->uID)) );
        $r = $db->query("DELETE FROM Users WHERE uID = ?",array(intval($this->uID)));
        $r = $db->query("DELETE FROM UserValidationHashes WHERE uID = ?",array(intval($this->uID)));

        $r = $db->query("DELETE FROM Piles WHERE uID = ?",array(intval($this->uID)));

        $r = $db->query("UPDATE Blocks set uID=? WHERE uID = ?",array( intval(USER_SUPER_ID), intval($this->uID)));
        $r = $db->query("UPDATE Pages set uID=? WHERE uID = ?",array( intval(USER_SUPER_ID), intval($this->uID)));
    }

    /**
     * Called only by the getGroupMembers function it sets the "type" of member for this group. Typically only used programmatically
     * @param string $type
     * @return void
     */
    public function setGroupMemberType($type)
    {
        $this->gMemberType = $type;
    }

    public function getGroupMemberType()
    {
        return $this->gMemberType;
    }

    public function canReadPrivateMessage($msg)
    {
        return $msg->getMessageUserID() == $this->getUserID();
    }

    public function updateUserAvatar(ImageInterface $image)
    {
        $fsl = StorageLocation::getDefault()->getFileSystemObject();
        $image = $image->get('jpg');
        $file = REL_DIR_FILES_AVATARS . '/' . $this->getUserID() . '.jpg';
        if ($fsl->has($file)) {
            $fsl->delete($file);
        }

        $fsl->write(
            $file,
            $image,
            array(
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => 'image/jpeg'
            )
        );

        $db = Loader::db();
        $db->query("update Users set uHasAvatar = 1 where uID = ?", array($this->getUserID()));
    }

    public function sendPrivateMessage($recipient, $subject, $text, $inReplyTo = false)
    {
        if (Limit::isOverLimit($this->getUserID())) {
            return Limit::getErrorObject();
        }
        $antispam = Loader::helper('validation/antispam');
        $messageText = t('Subject: %s', $subject);
        $messageText .= "\n";
        $messageText .= t('Message: %s', $text);

        $additionalArgs = array('user' => $this);
        if (!$antispam->check($messageText, 'private_message', $additionalArgs)) {
            return false;
        }

        $subject = ($subject == '') ? t('(No Subject)') : $subject;
        $db = Loader::db();
        $dt = Loader::helper('date');
        $v = array($this->getUserID(), $dt->getOverridableNow(), $subject, $text, $recipient->getUserID());
        $db->Execute('insert into UserPrivateMessages (uAuthorID, msgDateCreated, msgSubject, msgBody, uToID) values (?, ?, ?, ?, ?)', $v);

        $msgID = $db->Insert_ID();

        if ($msgID > 0) {
            // we add the private message to the sent box of the sender, and the inbox of the recipient
            $v = array($msgID, $this->getUserID(), $this->getUserID(), UserPrivateMessageMailbox::MBTYPE_SENT, 0, 1);
            $db->Execute('insert into UserPrivateMessagesTo (msgID, uID, uAuthorID, msgMailboxID, msgIsNew, msgIsUnread) values (?, ?, ?, ?, ?, ?)', $v);
            $v = array($msgID, $recipient->getUserID(), $this->getUserID(), UserPrivateMessageMailbox::MBTYPE_INBOX, 1, 1);
            $db->Execute('insert into UserPrivateMessagesTo (msgID, uID, uAuthorID, msgMailboxID, msgIsNew, msgIsUnread) values (?, ?, ?, ?, ?, ?)', $v);
        }

        // If the message is in reply to another message, we make a note of that here
        if (is_object($inReplyTo)) {
            $db->Execute('update UserPrivateMessagesTo set msgIsReplied = 1 where uID = ? and msgID = ?', array($this->getUserID(), $inReplyTo->getMessageID()));
        }

        // send the email notification
        if ($recipient->getAttribute('profile_private_messages_notification_enabled')) {
            $mh = Loader::helper('mail');
            $mh->addParameter('msgSubject', $subject);
            $mh->addParameter('msgBody', $text);
            $mh->addParameter('msgAuthor', $this->getUserName());
            $mh->addParameter('msgDateCreated', $msgDateCreated);
            $mh->addParameter('profileURL', View::url('/members/profile', 'view', $this->getUserID()));
            $mh->addParameter('profilePreferencesURL', View::url('/account/profile/edit'));
            $mh->to($recipient->getUserEmail());
            $mh->addParameter('siteName', Config::get('concrete.site'));

            $mi = MailImporter::getByHandle("private_message");
            if (is_object($mi) && $mi->isMailImporterEnabled()) {
                $mh->load('private_message_response_enabled');
                // we store information ABOUT the message here. The mail handler has to know how to handle this.
                $data = new \stdClass();
                $data->msgID = $msgID;
                $data->toUID = $recipient->getUserID();
                $data->fromUID = $this->getUserID();
                $mh->enableMailResponseProcessing($mi, $data);
            } else {
                $mh->load('private_message');
            }
            $mh->sendMail();
        }
    }

    /**
     * gets the user object of the current UserInfo object ($this)
     * @return User
     */
    public function getUserObject()
    {
        // returns a full user object - groups and everything - for this userinfo object
        $nu = ConcreteUser::getByUserID($this->uID);

        return $nu;
    }

    /**
     * Sets the attribute of a user info object to the specified value, and saves it in the database
    */
    public function setAttribute($ak, $value)
    {
        if (!is_object($ak)) {
            $ak = UserAttributeKey::getByHandle($ak);
        }
        $ak->setAttribute($this, $value);
        $this->reindex();
    }

    public function clearAttribute($ak)
    {
        $db = Loader::db();
        if (!is_object($ak)) {
            $ak = UserAttributeKey::getByHandle($ak);
        }
        $cav = $this->getAttributeValueObject($ak);
        if (is_object($cav)) {
            $cav->delete();
        }
        $this->reindex();
    }

    /**
     * Reindex the attributes on this file.
     * @return void
     */
    public function reindex()
    {
        $attribs = UserAttributeKey::getAttributes(
            $this->getUserID(),
            'getSearchIndexValue'
        );
        $db = Loader::db();

        $db->Execute('delete from UserSearchIndexAttributes where uID = ?', array($this->getUserID()));
        $searchableAttributes = array('uID' => $this->getUserID());

        $key = new UserAttributeKey();
        $key->reindex('UserSearchIndexAttributes', $searchableAttributes, $attribs);
    }

    /**
     * Gets the value of the attribute for the user
     */
    public function getAttribute($ak, $displayMode = false)
    {
        if (!is_object($ak)) {
            $ak = UserAttributeKey::getByHandle($ak);
        }
        if (is_object($ak)) {
            $av = $this->getAttributeValueObject($ak);
            if (is_object($av)) {
                if (func_num_args() > 2) {
                    $args = func_get_args();
                    array_shift($args);

                    return call_user_func_array(array($av, 'getValue'), $args);
                } else {
                    return $av->getValue($displayMode);
                }
            }
        }
    }

    public function getAttributeField($ak)
    {
        if (!is_object($ak)) {
            $ak = UserAttributeKey::getByHandle($ak);
        }
        $value = $this->getAttributeValueObject($ak);
        $ak->render('form', $value);
    }

    public function getAttributeValueObject($ak, $createIfNotFound = false)
    {
        $db = Loader::db();
        $av = false;
        $v = array($this->getUserID(), $ak->getAttributeKeyID());
        $avID = $db->GetOne("select avID from UserAttributeValues where uID = ? and akID = ?", $v);
        if ($avID > 0) {
            $av = UserAttributeValue::getByID($avID);
            if (is_object($av)) {
                $av->setUser($this);
                $av->setAttributeKey($ak);
            }
        }

        if ($createIfNotFound) {
            $cnt = 0;

            // Is this avID in use ?
            if (is_object($av)) {
                $cnt = $db->GetOne("select count(avID) from UserAttributeValues where avID = ?", $av->getAttributeValueID());
            }

            if ((!is_object($av)) || ($cnt > 1)) {
                $newAV = $ak->addAttributeValue();
                $av = UserAttributeValue::getByID($newAV->getAttributeValueID());
                $av->setUser($this);
            }
        }

        return $av;
    }

    public function update($data)
    {
        $db = Loader::db();
        if ($this->uID) {
            $ux = $this->getUserObject();
            $uName = $this->getUserName();
            $uEmail = $this->getUserEmail();
            $uHasAvatar = $this->hasAvatar();
            $uTimezone = $this->getUserTimezone();
            $uDefaultLanguage = $ux->getUserDefaultLanguage();
            if (isset($data['uName'])) {
                $uName = $data['uName'];
            }
            if (isset($data['uEmail'])) {
                $uEmail = $data['uEmail'];
            }
            if (isset($data['uHasAvatar'])) {
                $uHasAvatar = $data['uHasAvatar'];
            }
            if ( isset($data['uTimezone'])) {
                $uTimezone = $data['uTimezone'];
            }
            if (isset($data['uDefaultLanguage']) && $data['uDefaultLanguage'] != '') {
                $uDefaultLanguage = $data['uDefaultLanguage'];
            }

            $testChange = false;

            if ($data['uPassword'] != null) {
                if ($data['uPassword'] == $data['uPasswordConfirm']) {

                    $dh = Loader::helper('date');
                    $dateTime = $dh->getOverridableNow();
                    $v = array($uName, $uEmail, $this->getUserObject()->getUserPasswordHasher()->HashPassword($data['uPassword']), $uHasAvatar, $uTimezone, $uDefaultLanguage, $dateTime, $this->uID);
                    $r = $db->prepare("update Users set uName = ?, uEmail = ?, uPassword = ?, uHasAvatar = ?, uTimezone = ?, uDefaultLanguage = ?, uLastPasswordChange = ? where uID = ?");
                    $res = $db->execute($r, $v);

                    $testChange = true;

                    $currentUser = new User();
                    $session = Core::make('session');
                    if($currentUser->isLoggedIn() && $currentUser->getUserID() == $session->get('uID')) {
                        $session->set('uLastPasswordChange', $dateTime);
                    }

                } else {
                    $updateGroups = false;
                }
            } else {
                $v = array($uName, $uEmail, $uHasAvatar, $uTimezone, $uDefaultLanguage, $this->uID);
                $r = $db->prepare("update Users set uName = ?, uEmail = ?, uHasAvatar = ?, uTimezone = ?, uDefaultLanguage = ? where uID = ?");
                $res = $db->execute($r, $v);
            }

            // now we check to see if the user is updated his or her own logged in record
            $session = Core::make('session');
            if ($session->has('uID') && ($session->get('uID') == $this->uID)) {
                $session->set('uName', $uName);
                $session->set('uTimezone', $uTimezone);
                $session->set('uDefaultLanguage', $uDefaultLanguage);
            }

            // run any internal event we have for user update
            $ui = UserInfo::getByID($this->uID);
            $ue = new \Concrete\Core\User\Event\UserInfo($ui);
            Events::dispatch('on_user_update', $ue);

            if ($testChange) {
                $ue = new \Concrete\Core\User\Event\UserInfoWithPassword($ui);
                Events::dispatch('on_user_change_password', $ue);
            }

            return $res;
        }
    }

    public function updateGroups($groupArray)
    {
        $db = Loader::db();
        $q = "select gID from UserGroups where uID = '{$this->uID}'";
        $r = $db->query($q);
        if ($r) {
            $existingGIDArray = array();
            while ($row = $r->fetchRow()) {
                $existingGIDArray[] = $row['gID'];
            }
        }

        $dh = Loader::helper('date');

        $datetime = $dh->getOverridableNow();
        if (is_array($groupArray)) {
            foreach ($groupArray as $gID) {
                $key = array_search($gID, $existingGIDArray);
                if ($key !== false) {
                    // we remove this item from the existing GID array
                    unset($existingGIDArray[$key]);
                } else {
                    // this item is new, so we add it.
                    $_ux = $this->getUserObject();
                    $g = Group::getByID($gID);
                    $_ux->enterGroup($g);
                }
            }
        }


        // now we go through the existing GID Array, and remove everything, since whatever is left is not wanted.

        // Fire on_user_exit_group event for each group exited
        foreach ($existingGIDArray as $gID) {
            $group = Group::getByID($gID);
            if ($group) {
                $ue = new \Concrete\Core\User\Event\UserGroup($this->getUserObject());
                $ue->setGroupObject($group);
                Events::dispatch('on_user_exit_group', $ue);
            }
        }

        // Remove from db
        if (count($existingGIDArray) > 0) {
            $inStr = implode(',', $existingGIDArray);
            $q2 = "delete from UserGroups where uID = '{$this->uID}' and gID in ({$inStr})";
            $r2 = $db->query($q2);
            // fire the user group removal event for each of the groups we've deleted
            foreach ($existingGIDArray as $gID) {
                $ue = new \Concrete\Core\User\Event\UserGroup($this->getUserObject());
                $ue->setGroupObject(Group::getByID($gID));
                Events::dispatch('on_user_exit_group', $ue);
            }

        }
    }

    public function saveUserAttributesForm($attributes)
    {
        foreach($attributes as $uak) {
            $uak->saveAttributeForm($this);
        }

        $ue = new \Concrete\Core\User\Event\UserInfoWithAttributes($this);
        $ue->setAttributes($attributes);
        Events::dispatch('on_user_attributes_saved', $ue);
    }

    /**
     * @param array $data
     * @return UserInfo
     */
    public function register($data)
    {
        // slightly different than add. this is public facing
		if (Config::get('concrete.user.registration.validate_email')) {
			$data['uIsValidated'] = 0;
		}
        $ui = UserInfo::add($data);

        return $ui;
    }

    public function setupValidation()
    {
        $db = Loader::db();
        $hash = $db->GetOne("select uHash from UserValidationHashes where uID = ? order by uDateGenerated desc", array($this->uID));
        if ($hash) {
            return $hash;
        } else {
            $h = Loader::helper('validation/identifier');
            $hash = $h->generate('UserValidationHashes', 'uHash');
            $db->Execute("insert into UserValidationHashes (uID, uHash, uDateGenerated) values (?, ?, ?)", array($this->uID, $hash, time()));

            return $hash;
        }
    }

    public function markValidated()
    {
        $db = Loader::db();
        $v = array($this->uID);
        $db->query("update Users set uIsValidated = 1, uIsFullRecord = 1 where uID = ?", $v);
        $db->query("update UserValidationHashes set uDateRedeemed = " . time() . " where uID = ?", $v);

        $ue = new \Concrete\Core\User\Event\UserInfo($this);
        Events::dispatch('on_user_validate', $ue);

        return true;
    }

    public function changePassword($newPassword)
    {
        $db = Loader::db();
        if ($this->uID) {
            $dh = Loader::helper('date');
            $dateTime = $dh->getOverridableNow();
            $v = array(
                $this->getUserObject()->getUserPasswordHasher()->HashPassword($newPassword),
                $dateTime,
                $this->uID
            );
            $q = "update Users set uPassword = ?, uLastPasswordChange = ?  where uID = ?";
            $r = $db->prepare($q);
            $res = $db->execute($r, $v);

            $ue = new \Concrete\Core\User\Event\UserInfoWithPassword($this);
            $ue->setUserPassword($newPassword);

            $currentUser = new User();
            $session = Core::make('session');
            if($currentUser->isLoggedIn() && $currentUser->getUserID() == $session->get('uID')) {
                $session->set('uLastPasswordChange', $dateTime);
            }

            Events::dispatch('on_user_change_password', $ue);

            return $res;
        }
    }

    public function activate()
    {
        $db = Loader::db();
        $q = "update Users set uIsActive = 1 where uID = '{$this->uID}'";
        $r = $db->query($q);
        $ue = new \Concrete\Core\User\Event\UserInfo($this);
        Events::dispatch('on_user_activate', $ue);
    }

    public function deactivate()
    {
        $db = Loader::db();
        $q = "update Users set uIsActive = 0 where uID = '{$this->uID}'";
        $r = $db->query($q);
        $ue = new \Concrete\Core\User\Event\UserInfo($this);
        Events::dispatch('on_user_deactivate', $ue);
    }

    public function resetUserPassword()
    {
        // resets user's password, and returns the value of the reset password
        $db = Loader::db();
        if ($this->uID > 0) {
            $newPassword = '';
            $chars = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
            for ($i = 0; $i < 7; $i++) {
                $newPassword .= substr($chars, rand() %strlen($chars), 1);
            }
            $this->changePassword($newPassword);

            return $newPassword;
        }
    }

    public function hasAvatar()
    {
        return $this->uHasAvatar;
    }

    public function getLastLogin()
    {
        return $this->uLastLogin;
    }

    public function getLastIPAddress()
    {
        $ip = new \Concrete\Core\Utility\IPAddress($this->uLastIP, true);
        return $ip->getIp($ip::FORMAT_IP_STRING);
    }

    public function getPreviousLogin()
    {
        return $this->uPreviousLogin;
    }

    public function isActive()
    {
        return $this->uIsActive;
    }

    public function isValidated()
    {
        return $this->uIsValidated;
    }

    public function isFullRecord()
    {
        return $this->uIsFullRecord;
    }

    public function getNumLogins()
    {
        return $this->uNumLogins;
    }

    public function getUserID()
    {
        return $this->uID;
    }

    public function getUserName()
    {
        return $this->uName;
    }

    public function getUserDisplayName()
    {
        return $this->getUserName();
    }

    public function getUserPassword()
    {
        return $this->uPassword;
    }

    public function getUserEmail()
    {
        return $this->uEmail;
    }

    /**
     * returns the user's timezone
     * @return string timezone
     */
    public function getUserTimezone()
    {
        return $this->uTimezone;
    }

    public function getUserDefaultLanguage()
    {
        return $this->uDefaultLanguage;
    }

    /**
     * Gets the date a user was added to the system,
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getUserDateAdded()
    {
        return $this->uDateAdded;
    }

    public function getUserStartDate()
    {
        return $this->upStartDate;
    }

    /**
     * Gets the date a user was last active on the site
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getLastOnline()
    {
        return $this->uLastOnline;
    }

    public function getUserEndDate()
    {
        return $this->upEndDate;
    }

}
