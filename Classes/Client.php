<?php
namespace BugOrderSystem;

require_once "Order.php";

use Log\ELogLevel;

class Client {
    const TABLE_NAME = "clients";
    const TABLE_KEY_COLUMN = "Id";
    
    private static $clients = array();
    private static $loadedAll = false;

    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $phoneNumber;

    /**
     * Client constructor.
     * @param array $clientData
     */
    private function __construct(array $clientData) {
        $this->id =          $clientData["Id"];
        $this->firstName =   $clientData["FirstName"];
        $this->lastName =    $clientData["LastName"];
        $this->email =       $clientData["Email"];
        $this->phoneNumber = $clientData["PhoneNumber"];
    }

    /**
     * @param int $clientId
     * @param array $clientData
     * @return Client
     * @throws \Exception
     */
    private static function addClientByClientData(int $clientId, array $clientData) {
        $res = @self::$clients[$clientId];

        if (!empty($res))
            throw new Exception("Client {0} Already Exist in the array!",null, $clientId);

        if (count($clientData) == 0)
            throw new Exception("Client {0} not exist in Database!",null,$clientId);

        self::$clients[$clientId] = new Client($clientData);

        return self::$clients[$clientId];
    }

    /**
     * @param int $clientId
     * @return Client
     * @throws \Exception
     */
    public static function &GetById(int $clientId) {
        if (empty($clientId))
            throw new Exception("Illegal Id! ({0})",null, $clientId);

        $res = @self::$clients[$clientId];

        if (empty($res)) {
            $clientsData = BugOrderSystem::GetDb()->where(self::TABLE_KEY_COLUMN, $clientId)->getOne(self::TABLE_NAME);
            if (empty($clientsData))
                throw new Exception("No client data found! Client {0} not exist!",null, $clientId);

            $res = self::addClientByClientData($clientId, $clientsData);
        }
        return $res;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $phoneNumber
     * @param string $email
     * @return Client
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    public static function &Add(string $firstName, string $lastName, string $phoneNumber, string $email) {
        if(empty($firstName))
            throw new Exception("???? ?????????? ???? ????????");

        if(empty($lastName))
            throw new Exception("???? ?????????? ???? ??????????");

        if(empty($phoneNumber))
            throw new Exception("???? ?????????? ???????? ?????????? ");

        if(self::isPhoneExist($phoneNumber))
            throw new Exception("???? ???????? ???????????? ????????, ?????????? {0} ?????? ???????? ????????????",null, $phoneNumber);

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception("???????????? {0} ???? ????????",null, $email);

        $clientData = array(
            "FirstName" => $firstName,
            "LastName" => $lastName,
            "PhoneNumber" => $phoneNumber,
            "Email" => $email
        );
        $success = BugOrderSystem::GetDB()->insert(self::TABLE_NAME, $clientData);
        if (!$success)
            throw new DBException("Unable to add client!", $clientData);

        $res = &self::getById($success);

        $logText = "???????? ???????? ?????? {clientName} ???????? ???????????? {clientPhone}";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("clientName" => $res, "clientPhone" => $res->GetPhoneNumber()));

        return $res;
    }

    /**
     * @param string $phoneNumber
     * @return bool
     * @throws Exception
     */
    public static function isPhoneExist(string $phoneNumber) {
        $data = BugOrderSystem::GetDB()->where("PhoneNumber", $phoneNumber)->getOne(self::TABLE_NAME);

        if (BugOrderSystem::GetDB()->count >= 1) {
            return $data["Id"];
        }
        else {
            return False;
        }
    }

    /**
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    public function Remove() {
        $sqlObject = BugOrderSystem::GetDB();
        $success = $sqlObject->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME);
        if (!$success)
            throw new DBException("Unable to delete client {0}",null, $this);

        unset(self::$clients[$this->id]);

        $logText = "?????????? {client} ????????";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("client" => $this));
    }


    /**
     * @param string $message
     * @param string $subject
     * @param \SplFileObject[]|null $AttachedFiles
     * @throws Exception
     * @throws \Exception
     */
    public function SendEmail(string $message, string $subject, array $AttachedFiles = null) {
        if (empty($this->email))
            throw new Exception("Email not exist!", $this);

        $emailObject = BugOrderSystem::GetEmail($subject, $message);
        $emailObject->addAddress($this->email, $this->firstName." ".$this->lastName);

        foreach ($AttachedFiles as $file) {
            if ($file->isFile())
                $emailObject->addAttachment($file->getRealPath(), $file->getFilename(), "base64", $file->getType());
        }

        if (!$emailObject->send())
            throw new Exception($emailObject->ErrorInfo);

        $logText = "???????? ???????? ???? ?????????? {client}";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("client" => $this, "Email" => $emailObject));
    }

    /**
     * @param callable $function_doEachIteration
     * @param array $OrderByArray
     * @throws \Exception
     */
    public static function LoopAll(callable $function_doEachIteration, array $OrderByArray = array()) {
        if (!self::$loadedAll) {
            $dbObject = BugOrderSystem::GetDB();
            foreach ($OrderByArray as $orderBy) {
                $dbObject->orderBy($orderBy[0], $orderBy[1]);
            }
            $clientsData = $dbObject->get(self::TABLE_NAME);

            foreach ($clientsData as $client) {
                if (!array_key_exists($client[self::TABLE_KEY_COLUMN], self::$clients)) {
                    self::addClientByClientData($client[self::TABLE_KEY_COLUMN], $client);
                }
            }

            self::$loadedAll = true;
        }

        foreach (self::$clients as $client) {
            call_user_func($function_doEachIteration, $client);
        }
    }


    /**
     * @return string
     */
    public function GetFullName () {
        return $this->firstName." ".$this->lastName;
    }

    /**
     * @return mixed
     */
    public function GetFirstName() {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function GetLastName() {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function GetEmail() {
        return $this->email;
    }

    /**
     * @return int
     */
    public function GetId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function GetPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * @param string $newEmail
     * @param bool $update
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    public function ChangeEmail(string $newEmail, bool $update = true) {
        if($newEmail) {
            $emailObject = new \PHPMailer();
            if (!$emailObject::validateAddress($newEmail))
                throw new Exception("Invalid client Email address ({0})!", null, $newEmail);

            $this->email = $newEmail;
            if ($update)
                $this->Update();
        }
    }

    /**
     * @param string $firstName
     * @param bool $update
     * @throws DBException
     * @throws \Exception
     */
    public function SetFirstName(string $firstName, bool $update = true) {
        $this->firstName = $firstName;
        if ($update)
            $this->Update();
    }

    /**
     * @param string $lastName
     * @param bool $update
     * @throws DBException
     * @throws \Exception
     */
    public function SetLastName(string $lastName, bool $update = true) {
        $this->lastName = $lastName;
        if ($update)
            $this->Update();
    }

    /**
     * @param string $phoneNumber
     * @param bool $update
     * @throws DBException
     * @throws \Exception
     */
    public function SetPhoneNumber(string $phoneNumber, bool $update = true) {
        $this->phoneNumber = $phoneNumber;
        if ($update)
            $this->Update();
    }

    /**
     * @param bool $log
     * @throws DBException
     * @throws \Exception
     */
    public function Update(bool $log = true) {
        $updateArray = array(
            "Email" => $this->email,
            "FirstName" => $this->firstName,
            "LastName" => $this->lastName,
            "PhoneNumber" => $this->phoneNumber
        );

        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new DBException("{0} ???? ???????? ?????????? ????", $updateArray, $this);

        if ($log) {
            $logText = "?????????? {client} ??????????";
            BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("client" => $this, "UpdateArray" => $updateArray));
        }
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->firstName." ".$this->lastName." (".$this->email.")";
    }
}