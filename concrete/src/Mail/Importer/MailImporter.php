<?php
namespace Concrete\Core\Mail\Importer;

use Concrete\Core\Foundation\ConcreteObject;
use Core;
use Database;
use Concrete\Core\Package\PackageList;
use Laminas\Mail\Storage\Pop3    as MailStoragePop3;
use Laminas\Mail\Storage\Imap    as MailStorageImap;
use Laminas\Mail\Storage\Message as MailMessage;
use Laminas\Mail\Exception       as MailException;

class MailImporter extends ConcreteObject
{
    /**
     * gets the text string that's used to identify the body of the message.
     *
     * @return string
     */
    public function getMessageBodyHeader()
    {
        return t('--- Reply ABOVE. Do not alter this line --- [' . $this->validationHash  . '] ---');
    }

    public function getValidationHash()
    {
        return $this->validationHash;
    }

    public function getMessageBodyHashRegularExpression()
    {
        return t('/\-\-\- Reply ABOVE\. Do not alter this line \-\-\- \[(.*)\] \-\-\-/i');
    }

    public static function getList()
    {
        $db = Database::connection();
        $importers = [];
        foreach ($db->fetchFirstColumn('select miID from MailImporters order by miID asc') as $miID) {
            $importers[] = static::getByID($miID);
        }

        return $importers;
    }

    /**
     * @return static[]
     */
    public static function getEnabledList()
    {
        $db = Database::connection();
        $importers = [];
        foreach ($db->fetchFirstColumn('select miID from MailImporters where miIsEnabled = 1 order by miID asc') as $miID) {
            $importers[] = static::getByID($miID);
        }

        return $importers;
    }

    public static function getByID($miID)
    {
        $db  = Database::connection();
        $row = $db->GetRow("select miID, miHandle, miServer, miUsername, miPassword, miEncryption, miIsEnabled, miEmail, miPort, miConnectionMethod, Packages.pkgID, pkgHandle from MailImporters left join Packages on MailImporters.pkgID = Packages.pkgID where miID = ?", array($miID));

        if (isset($row['miID'])) {
            $txt = Core::make('helper/text');

            if (isset($row['pkgID'])) {
              $pkgHandle = PackageList::getHandle($row['pkgID']);
              $mi        = Core::make('\\Concrete\\Package\\' . $txt->camelcase($pkgHandle) . '\\Src\\Mail\\Importer\\Type\\' . $txt->camelcase($row['miHandle']));
            } else {
              $mi = Core::make('\\Concrete\\Core\\Mail\\Importer\\Type\\' . $txt->camelcase($row['miHandle']));
            }

            $mi->setPropertiesFromArray($row);

            return $mi;
        }

        return false;
    }

    public static function getByHandle($miHandle)
    {
        $db = Database::connection();
        $miID = $db->GetOne("select miID from MailImporters where miHandle = ?", $miHandle);

        return static::getByID($miID);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->Execute('delete from MailImporters where miID = ?', array($this->miID));
    }

    public static function getListByPackage($pkg)
    {
        $db = Database::connection();
        $importers = [];
        foreach ($db->fetchFirstColumn('select miID from MailImporters where pkgID = ? order by miID asc', [$pkg->getPackageID()]) as $miID) {
            $importers[] = static::getByID($miID);
        }

        return $importers;
    }

    public function getMailImporterID()
    {
        return $this->miID;
    }
    public function getMailImporterName()
    {
        $txt = Core::make('helper/text');

        return $txt->unhandle($this->miHandle);
    }
    public function getMailImporterHandle()
    {
        return $this->miHandle;
    }
    public function getMailImporterServer()
    {
        return $this->miServer;
    }
    public function getMailImporterUsername()
    {
        return $this->miUsername;
    }
    public function getMailImporterPassword()
    {
        return $this->miPassword;
    }
    public function getMailImporterEncryption()
    {
        return $this->miEncryption;
    }
    public function getMailImporterEmail()
    {
        return $this->miEmail;
    }
    public function getMailImporterPort()
    {
        return $this->miPort;
    }
    public function isMailImporterEnabled()
    {
        return $this->miIsEnabled;
    }
    public function getMailImporterConnectionMethod()
    {
        return $this->miConnectionMethod;
    }
    public function getPackageID()
    {
        return $this->pkgID;
    }
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    public static function add($args, $pkg = null)
    {
        $db = Database::connection();
        $args = $args + array(
          'miPort' => null,
          'miIsEnabled' => null,
          'miEncryption' => null,
          'miConnectionMethod' => null,
          'miServer' => null,
          'miUsername' => null,
          'miPassword' => null,
          'miEmail' => null,
        );
        extract($args);

        if ($miPort < 1) {
            $miPort = 0;
        }

        if ($miIsEnabled != 1) {
            $miIsEnabled = 0;
        }

        if ($miEncryption == '') {
            $miEncryption = null;
        }

        if (!$miConnectionMethod) {
            $miConnectionMethod = 'POP';
        }

        $pkgID = ($pkg == null) ? 0 : $pkg->getPackageID();

        $db->Execute('insert into MailImporters (miHandle, miServer, miUsername, miPassword, miEncryption, miIsEnabled, miEmail, miPort, miConnectionMethod, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $miHandle,
                Core::make('helper/security')->sanitizeString($miServer),
                $miUsername,
                $miPassword,
                $miEncryption,
                $miIsEnabled,
                Core::make('helper/security')->sanitizeString($miEmail),
                $miPort,
                $miConnectionMethod,
                $pkgID,
            ));

        $miID = $db->Insert_ID();

        return static::getByID($miID);
    }

    public function update($args)
    {
        $db = Database::connection();
        extract($args);

        if ($miPort < 1) {
            $miPort = 0;
        }

        if ($miIsEnabled != 1) {
            $miIsEnabled = 0;
        }

        if ($miEncryption == '') {
            $miEncryption = null;
        }

        $db->Execute('update MailImporters set miServer = ?, miUsername = ?, miPassword = ?, miEncryption = ?, miIsEnabled = ?, miEmail = ?, miPort = ?, miConnectionMethod = ? where miID = ?',
            array(
                Core::make('helper/security')->sanitizeString($miServer),
                $miUsername,
                $miPassword,
                $miEncryption,
                $miIsEnabled,
                Core::make('helper/security')->sanitizeString($miEmail),
                $miPort,
                $miConnectionMethod,
                $this->miID,
            ));
    }

    public function setupBody($body)
    {
        return $this->getMessageBodyHeader() . "\n\n" . $body;
    }

    public function getValidationErrorMessage()
    {
        return t('Unable to process email. Check that your email contains the validation hash present in the original message.');
    }

    public function setupValidation($email, $dataObject)
    {
        $db = Database::connection();
        $h = Core::make('helper/validation/identifier');
        $hash = $h->generate('MailValidationHashes', 'mHash');
        $args = array($email, $this->miID, $hash, time(), serialize($dataObject));
        $db->Execute("insert into MailValidationHashes (email, miID, mHash, mDateGenerated, data) values (?, ?, ?, ?, ?)", $args);
        $this->validationHash = $hash;

        return $hash;
    }

    /**
     * @return MailImportedMessage[]
     */
    public function getPendingMessages()
    {
        $messages = [];

        $args = [
          'host'     => $this->miServer,
          'user'     => $this->miUsername,
          'password' => $this->miPassword
        ];

        if ($this->miEncryption != '') {
            $args['ssl'] = $this->miEncryption;
        }

        if ($this->miPort > 0) {
            $args['port'] = $this->miPort;
        }

        if ($this->miConnectionMethod == 'IMAP') {
            $mail = new MailStorageImap($args);
        } else {
            $mail = new MailStoragePop3($args);
        }

        // Returns a map with $index => $uniqueID
        $mailIDMap = $mail->getUniqueId();

        foreach ($mail as $i => $m) {
            $messages[] = new MailImportedMessage($mail, $m, $i, $mailIDMap[$i]);
        }

        return $messages;
    }

    public function cleanup(MailImportedMessage $me)
    {
        $db = Database::connection();
        $db->query("update MailValidationHashes set mDateRedeemed = " . time() . " where mHash = ?", array($me->getValidationHash()));
        $me->delete();
    }
}
