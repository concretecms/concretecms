<?php

namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Url\UrlInterface;
use Concrete\Core\User\Event\AddUser;
use Concrete\Core\User\PrivateMessage\Limit;
use Concrete\Core\User\PrivateMessage\Mailbox as UserPrivateMessageMailbox;
use Imagine\Image\ImageInterface;
use Concrete\Flysystem\AdapterInterface;
use Concrete\Core\Mail\Importer\MailImporter;
use View;
use Config;
use Events;
use User as ConcreteUser;
use UserAttributeKey;
use Concrete\Core\Attribute\Value\UserValue as UserAttributeValue;
use Group;
use Hautelook\Phpass\PasswordHash;
use Session;
use Core;
use Database;
use Concrete\Core\User\Avatar\AvatarServiceInterface;

class UserInfo extends Object implements \Concrete\Core\Permission\ObjectInterface
{

    protected $avatarService;
    protected $application;
    protected $connection;

    public function __construct(Connection $connection, Application $application, AvatarServiceInterface $avatarService)
    {
        $this->avatarService = $avatarService;
        $this->application = $application;
        $this->connection = $connection;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return 'UserInfo: ' . $this->getUserID();
    }

    /**
     * @return int
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->uID;
    }

    /**
     * @return string
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\UserInfoResponse';
    }

    /**
     * @return string|false
     */
    public function getPermissionAssignmentClassName()
    {
        return false;
    }

    /**
     * @return string|false
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    /**
     * @return Group[]
     */
    public function getUserBadges()
    {
        $db = $this->connection;
        $groups = array();
        $r = $db->Execute('select g.gID from Groups g inner join UserGroups ug on g.gID = ug.gID where g.gIsBadge = 1 and ug.uID = ? order by ugEntered desc', array($this->getUserID()));
        while ($row = $r->FetchRow()) {
            $groups[] = Group::getByID($row['gID']);
        }

        return $groups;
    }


    /**
     * Deletes a user.
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

        $db = $this->connection;

        $r = $db->Execute('select avID, akID from UserAttributeValues where uID = ?', array($this->uID));
        while ($row = $r->FetchRow()) {
            $uak = UserAttributeKey::getByID($row['akID']);
            $av = $this->getAttributeValueObject($uak);
            if (is_object($av)) {
                $av->delete();
            }
        }

        $r = $db->query("DELETE FROM OauthUserMap WHERE user_id = ?", array(intval($this->uID)));

        $r = $db->query("DELETE FROM UserSearchIndexAttributes WHERE uID = ?", array(intval($this->uID)));

        $r = $db->query("DELETE FROM UserGroups WHERE uID = ?", array(intval($this->uID)));
        $r = $db->query("DELETE FROM Users WHERE uID = ?", array(intval($this->uID)));
        $r = $db->query("DELETE FROM UserValidationHashes WHERE uID = ?", array(intval($this->uID)));

        $r = $db->query("DELETE FROM Piles WHERE uID = ?", array(intval($this->uID)));

        $r = $db->query("UPDATE Blocks set uID=? WHERE uID = ?", array(intval(USER_SUPER_ID), intval($this->uID)));
        $r = $db->query("UPDATE Pages set uID=? WHERE uID = ?", array(intval(USER_SUPER_ID), intval($this->uID)));
    }

    /**
     * @param \Concrete\Core\User\PrivateMessage\PrivateMessage $msg
     *
     * @return bool
     */
    public function canReadPrivateMessage($msg)
    {
        return $msg->getMessageUserID() == $this->getUserID();
    }

    /**
     * @param ImageInterface $image
     */
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
                'mimetype' => 'image/jpeg',
            )
        );

        $db = $this->connection;
        $db->query("update Users set uHasAvatar = 1 where uID = ?", array($this->getUserID()));

        // run any internal event we have for user update
        $ui = self::getByID($this->uID);
        $ue = new \Concrete\Core\User\Event\UserInfo($ui);
        Events::dispatch('on_user_update', $ue);
    }

    /**
     * @param UserInfo $recipient
     * @param string $subject
     * @param string $text
     * @param \Concrete\Core\User\PrivateMessage\PrivateMessage $inReplyTo
     *
     * @return \Concrete\Core\Error\Error|false|null
     */
    public function sendPrivateMessage($recipient, $subject, $text, $inReplyTo = false)
    {
        if (Limit::isOverLimit($this->getUserID())) {
            return Limit::getErrorObject();
        }
        $antispam = Core::make('helper/validation/antispam');
        $messageText = t('Subject: %s', $subject);
        $messageText .= "\n";
        $messageText .= t('Message: %s', $text);

        $additionalArgs = array('user' => $this);
        if (!$antispam->check($messageText, 'private_message', $additionalArgs)) {
            return false;
        }

        $subject = ($subject == '') ? t('(No Subject)') : $subject;
        $db = $this->connection;
        $dt = Core::make('helper/date');
        $msgDateCreated = $dt->getOverridableNow();
        $v = array($this->getUserID(), $msgDateCreated, $subject, $text, $recipient->getUserID());
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
            $mh = Core::make('mail');
            $mh->addParameter('msgSubject', $subject);
            $mh->addParameter('msgBody', $text);
            $mh->addParameter('msgAuthor', $this->getUserName());
            $mh->addParameter('msgDateCreated', $msgDateCreated);
            $mh->addParameter('profileURL', $this->getUserPublicProfileUrl());
            $mh->addParameter('profilePreferencesURL', View::url('/account/profile/edit'));
            $mh->to($recipient->getUserEmail());
            $mh->addParameter('siteName', tc('SiteName', Config::get('concrete.site')));

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
     * Gets the User object of the current UserInfo object ($this).
     *
     * @return User
     */
    public function getUserObject()
    {
        // returns a full user object - groups and everything - for this userinfo object
        $nu = ConcreteUser::getByUserID($this->uID);

        return $nu;
    }

    /**
     * @param array $data
     *
     * @return bool|null
     */
    public function update($data)
    {
        $db = $this->connection;
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
            $emailChanged = false;
            if (isset($data['uEmail'])) {
                if ($uEmail != $data['uEmail']) {
                    $emailChanged = true;
                }
                $uEmail = $data['uEmail'];
            }
            if (isset($data['uHasAvatar'])) {
                $uHasAvatar = $data['uHasAvatar'];
            }
            if (isset($data['uTimezone'])) {
                $uTimezone = $data['uTimezone'];
            }
            if (isset($data['uDefaultLanguage']) && $data['uDefaultLanguage'] != '') {
                $uDefaultLanguage = $data['uDefaultLanguage'];
            }

            $testChange = false;

            if (isset($data['uPassword']) && $data['uPassword'] != null) {
                if ($data['uPassword'] == $data['uPasswordConfirm']) {
                    $dh = Core::make('helper/date');
                    $dateTime = $dh->getOverridableNow();
                    $v = array($uName, $uEmail, $this->getUserObject()->getUserPasswordHasher()->HashPassword($data['uPassword']), $uHasAvatar ? 1 : 0, $uTimezone, $uDefaultLanguage, $dateTime, $this->uID);
                    $r = $db->prepare("update Users set uName = ?, uEmail = ?, uPassword = ?, uHasAvatar = ?, uTimezone = ?, uDefaultLanguage = ?, uLastPasswordChange = ? where uID = ?");
                    $res = $db->execute($r, $v);

                    $testChange = true;

                    $currentUser = new User();
                    $session = Core::make('session');
                    if ($currentUser->isLoggedIn() && $currentUser->getUserID() == $session->get('uID')) {
                        $session->set('uLastPasswordChange', $dateTime);
                    }
                }
            } else {
                $v = array($uName, $uEmail, $uHasAvatar ? 1 : 0, $uTimezone, $uDefaultLanguage, $this->uID);
                $r = $db->prepare("update Users set uName = ?, uEmail = ?, uHasAvatar = ?, uTimezone = ?, uDefaultLanguage = ? where uID = ?");
                $res = $db->execute($r, $v);
            }

            if ($emailChanged) {
                $db->query("DELETE FROM UserValidationHashes WHERE uID = ?", array(intval($this->uID)));
            }

            // now we check to see if the user is updated his or her own logged in record
            $session = Core::make('session');
            if ($session->has('uID') && ($session->get('uID') == $this->uID)) {
                $session->set('uName', $uName);
                $session->set('uTimezone', $uTimezone);
                $session->set('uDefaultLanguage', $uDefaultLanguage);
            }

            // run any internal event we have for user update
            $ui = self::getByID($this->uID);
            $ue = new \Concrete\Core\User\Event\UserInfo($ui);
            Events::dispatch('on_user_update', $ue);

            if ($testChange) {
                $ue = new \Concrete\Core\User\Event\UserInfoWithPassword($ui);
                Events::dispatch('on_user_change_password', $ue);
            }

            return $res;
        }
    }

    /**
     * @param int[] $groupArray
     */
    public function updateGroups($groupArray)
    {
        $db = $this->connection;
        $q = "select gID from UserGroups where uID = '{$this->uID}'";
        $r = $db->query($q);
        if ($r) {
            $existingGIDArray = array();
            while ($row = $r->fetchRow()) {
                $existingGIDArray[] = $row['gID'];
            }
        }

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
            $db->query($q2);
            // fire the user group removal event for each of the groups we've deleted
            foreach ($existingGIDArray as $gID) {
                $ue = new \Concrete\Core\User\Event\UserGroup($this->getUserObject());
                $ue->setGroupObject(Group::getByID($gID));
                Events::dispatch('on_user_exit_group', $ue);
            }
        }
    }

    /**
     * @return string
     */
    public function setupValidation()
    {
        $db = $this->connection;
        $hash = $db->GetOne("select uHash from UserValidationHashes where uID = ? order by uDateGenerated desc", array($this->uID));
        if ($hash) {
            return $hash;
        } else {
            $h = Core::make('helper/validation/identifier');
            $hash = $h->generate('UserValidationHashes', 'uHash');
            $db->Execute("insert into UserValidationHashes (uID, uHash, uDateGenerated) values (?, ?, ?)", array($this->uID, $hash, time()));

            return $hash;
        }
    }

    /**
     * @return true
     */
    public function markValidated()
    {
        $db = $this->connection;
        $v = array($this->uID);
        $db->query("update Users set uIsValidated = 1, uIsFullRecord = 1 where uID = ?", $v);
        $db->query("update UserValidationHashes set uDateRedeemed = " . time() . " where uID = ?", $v);

        $ue = new \Concrete\Core\User\Event\UserInfo($this);
        Events::dispatch('on_user_validate', $ue);

        return true;
    }

    /**
     * @param string $newPassword
     *
     * @return bool
     */
    public function changePassword($newPassword)
    {
        $db = $this->connection;
        if ($this->uID) {
            $dh = Core::make('helper/date');
            $dateTime = $dh->getOverridableNow();
            $v = array(
                $this->getUserObject()->getUserPasswordHasher()->HashPassword($newPassword),
                $dateTime,
                $this->uID,
            );
            $q = "update Users set uPassword = ?, uLastPasswordChange = ?  where uID = ?";
            $r = $db->prepare($q);
            $res = $db->execute($r, $v);

            $ue = new \Concrete\Core\User\Event\UserInfoWithPassword($this);
            $ue->setUserPassword($newPassword);

            $currentUser = new User();
            $session = Core::make('session');
            if ($currentUser->isLoggedIn() && $currentUser->getUserID() == $session->get('uID')) {
                $session->set('uLastPasswordChange', $dateTime);
            }

            Events::dispatch('on_user_change_password', $ue);

            return $res;
        }
    }

    /**
     */
    public function activate()
    {
        $db = $this->connection;
        $q = "update Users set uIsActive = 1 where uID = '{$this->uID}'";
        $db->query($q);
        $ue = new \Concrete\Core\User\Event\UserInfo($this);
        Events::dispatch('on_user_activate', $ue);
    }

    /**
     */
    public function deactivate()
    {
        $db = $this->connection;
        $q = "update Users set uIsActive = 0 where uID = '{$this->uID}'";
        $db->query($q);
        $ue = new \Concrete\Core\User\Event\UserInfo($this);
        Events::dispatch('on_user_deactivate', $ue);
    }

    /**
     * @return string
     */
    public function resetUserPassword()
    {
        // resets user's password, and returns the value of the reset password
        if ($this->uID > 0) {
            $newPassword = '';
            $chars = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
            for ($i = 0; $i < 7; $i++) {
                $newPassword .= substr($chars, rand() % strlen($chars), 1);
            }
            $this->changePassword($newPassword);

            return $newPassword;
        }
    }

    public function getUserAvatar()
    {
        return $this->avatarService->getAvatar($this);
    }

    /**
     * @return null|Concrete\Core\Url\UrlInterface
     */
    public function getUserPublicProfileUrl()
    {
        if (!$this->application['config']->get('concrete.user.profiles_enabled')) {
            return null;
        }
        $url = $this->application->make('url/manager');
        return $url->resolve(array(
            '/members/profile',
            'view',
            $this->getUserID()
            )
        );
    }

    /**
     * @return bool
     */
    public function hasAvatar()
    {
        return $this->avatarService->userHasAvatar($this);
    }

    /**
     * @return int
     */
    public function getLastLogin()
    {
        return $this->uLastLogin;
    }

    /**
     * @return string|null
     */
    public function getLastIPAddress()
    {
        $ip = new \Concrete\Core\Utility\IPAddress($this->uLastIP, true);

        return $ip->getIp($ip::FORMAT_IP_STRING);
    }

    /**
     * @return int
     */
    public function getPreviousLogin()
    {
        return $this->uPreviousLogin;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->uIsActive;
    }

    /**
     * @return bool
     */
    public function isValidated()
    {
        return $this->uIsValidated;
    }

    /**
     * @return bool
     */
    public function isFullRecord()
    {
        return $this->uIsFullRecord;
    }

    /**
     * @return int
     */
    public function getNumLogins()
    {
        return $this->uNumLogins;
    }

    /**
     * @return int
     */
    public function getUserID()
    {
        return $this->uID;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->uName;
    }

    /**
     * @return string
     */
    public function getUserDisplayName()
    {
        return $this->getUserName();
    }

    /**
     * @return string
     */
    public function getUserPassword()
    {
        return $this->uPassword;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->uEmail;
    }

    /**
     * Returns the user's timezone.
     *
     * @return string
     */
    public function getUserTimezone()
    {
        return $this->uTimezone;
    }

    /**
     * @return string
     */
    public function getUserDefaultLanguage()
    {
        return $this->uDefaultLanguage;
    }

    /**
     * Gets the date a user was added to the system.
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getUserDateAdded()
    {
        return $this->uDateAdded;
    }

    /**
     * Gets the date a user was last active on the site.
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getLastOnline()
    {
        return $this->uLastOnline;
    }

    /**
     * @param UserAttributeKey[] $attributes
     */
    public function saveUserAttributesForm($attributes)
    {
        foreach ($attributes as $uak) {
            $uak->saveAttributeForm($this);
        }

        $ue = new \Concrete\Core\User\Event\UserInfoWithAttributes($this);
        $ue->setAttributes($attributes);
        Events::dispatch('on_user_attributes_saved', $ue);
    }


    /**
     * Sets the attribute of a user info object to the specified value, and saves it in the database.
     *
     * @param UserAttributeKey|string $ak
     * @param mixed $value
     */
    public function setAttribute($ak, $value)
    {
        if (!is_object($ak)) {
            $ak = UserAttributeKey::getByHandle($ak);
        }
        $ak->setAttribute($this, $value);
        $this->reindex();
    }

    /**
     * @param UserAttributeKey|string $ak
     */
    public function clearAttribute($ak)
    {
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
     */
    public function reindex()
    {
        $attribs = UserAttributeKey::getAttributes(
            $this->getUserID(),
            'getSearchIndexValue'
        );
        $db = $this->connection;

        $db->Execute('delete from UserSearchIndexAttributes where uID = ?', array($this->getUserID()));
        $searchableAttributes = array('uID' => $this->getUserID());

        $key = new UserAttributeKey();
        $key->reindex('UserSearchIndexAttributes', $searchableAttributes, $attribs);
    }

    /**
     * Gets the value of the attribute for the user.
     *
     * @param UserAttributeKey|string $ak
     * @param string|false $displayMode
     *
     * @return mixed|null
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

    /**
     * @param UserAttributeKey|string $ak
     */
    public function getAttributeField($ak)
    {
        if (!is_object($ak)) {
            $ak = UserAttributeKey::getByHandle($ak);
        }
        $value = $this->getAttributeValueObject($ak);
        $ak->render('form', $value);
    }

    /**
     * @param UserAttributeKey|string $ak
     * @param bool $createIfNotFound
     *
     * @return \Concrete\Core\Attribute\Value\UserValue|false
     */
    public function getAttributeValueObject($ak, $createIfNotFound = false)
    {
        $db = $this->connection;
        $av = false;
        if (!is_object($ak)) {
            $ak = UserAttributeKey::getByHandle($ak);
        }
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

    /**
     * Magic method for user attributes. This is db expensive but pretty damn cool
     * so if the attrib handle is "my_attribute", then get the attribute with $ui->getUserMyAttribute(), or "uFirstName" become $ui->getUserUfirstname();.
     * @return mixed|null
     */
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 7) == 'getUser') {
            $nm = preg_replace('/(?!^)[[:upper:]]/', '_\0', $nm);
            $nm = strtolower($nm);
            $nm = str_replace('get_user_', '', $nm);

            return $this->getAttribute($nm);
        }
    }


    /**
     * @deprecated
     */
    public static function add($data)
    {
        return Core::make('user/registration')->create($data);
    }

    /**
     * @deprecated
     */
    public static function addSuperUser($uPasswordEncrypted, $uEmail)
    {
        return Core::make('user/registration')->createSuperUser($uPasswordEncrypted, $uEmail);
    }

    /**
     * @deprecated
     */
    public static function register($data)
    {
        return Core::make('user/registration')->createFromPublicRegistration($data);
    }

    /**
     * @deprecated
     */
    public static function getByID($uID)
    {
        return Core::make('Concrete\Core\User\UserInfoFactory')->getByID($uID);
    }

    /**
     * @deprecated
     */
    public static function getByUserName($uName)
    {
        return Core::make('Concrete\Core\User\UserInfoFactory')->getByName($uName);
    }

    /**
     * @deprecated
     */
    public static function getByEmail($uEmail)
    {
        return Core::make('Concrete\Core\User\UserInfoFactory')->getByEmail($uEmail);
    }

    /**
     * @deprecated
     */
    public static function getByValidationHash($uHash, $unredeemedHashesOnly = true)
    {
        return Core::make('Concrete\Core\User\UserInfoFactory')->getByValidationHash($uHash, $unredeemedHashesOnly);
    }

}
