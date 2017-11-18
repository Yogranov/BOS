<?php
namespace BugOrderSystem;

require_once "Order.php";

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
    private $wantEmail;
    //private $ordersInfo = array();

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
        $this->wantEmail =   (bool)$clientData["ClientWantsMails"];
        //$this->ordersInfo =  array();
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
            throw new \Exception("Client Already Exist in the array!");

        if (count($clientData) == 0)
            throw new \Exception("Client not exist in Database!");

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
            throw new \Exception("Illegal Id!");

        $res = @self::$clients[$clientId];

        if (empty($res)) {
            $clientsData = BugOrderSystem::GetDb()->where(self::TABLE_KEY_COLUMN, $clientId)->getOne(self::TABLE_NAME);
            if (empty($clientsData))
                throw new \Exception("No client data found! Client not exist!");

            $res = self::addClientByClientData($clientId, $clientsData);
        }
        return $res;
    }

    /**
     * @param array $clientData
     * @return Client
     * @throws \Exception
     */
    public static function Add(array $clientData) {
        $sqlObject = BugOrderSystem::GetDB();
        $success = $sqlObject->insert(self::TABLE_NAME, $clientData);
        if (!$success)
            throw new \Exception("Unable to add client!\n\r ".$sqlObject->getLastError());
        $res = &self::getById($success);
        return $res;
    }

    /**
     * @param string $phoneNumber
     * @return int/bool
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
     * @throws \Exception
     */
    public function Remove() {
        $sqlObject = BugOrderSystem::GetDB();
        $success = $sqlObject->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME);
        if (!$success)
            throw new \Exception("Unable to delete client (".$this->id."):\n\r ".$sqlObject->getLastError());

        unset(self::$clients[$this->id]);
    }

    /**
     * @param string $message
     * @param string $subject
     * @param string $AttachedFile
     * @throws \Exception
     */
    public function SendEmail(string $message, string $subject, string $AttachedFile = "") {
        if (!$this->wantEmail)
            exit;

        if (empty($this->email))
            throw new Exception("Email not exist!", $this);

        $emailObject = BugOrderSystem::GetEmail($subject, $message);
        $emailObject->addAddress($this->email, $this->firstName." ".$this->lastName);
        $emailObject->Body = $message;
        $emailObject->Subject = $subject;
        //if (is_string($AttachedFile) && file_exists($AttachedFile)) {
            //\Services::dump($AttachedFile);
            //$emailObject->addAttachment($AttachedFile);
        //}

        if (!$emailObject->send())
            throw new \Exception($emailObject->ErrorInfo);
    }

    /**
     * @param string $newEmail
     * @throws Exception
     */
    public function ChangeEmail(string $newEmail) {
        if($newEmail) {
            $emailObject = new \PHPMailer();
            if (!$emailObject::validateAddress($newEmail))
                throw new Exception("Invalid client Email address ({0})!", null, $newEmail);

            $this->email = $newEmail;
            $this->Update();
        }
    }

    /**
     * @throws Exception
     */
    private function Update() {
        $updateArray = array(
            "Email" => $this->email,
            "FirstName" => $this->firstName,
            "LastName" => $this->lastName,
            "PhoneNumber" => $this->phoneNumber,
            "ClientWantsMails" => (string)$this->wantEmail
        );

        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new Exception("לא ניתן לעדכן את {0}", $updateArray, $this);
    }

    /**
     * @param callable $function_doEachIteration
     * @param array $OrderByArray
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
    
    public function AddOrderInfo() {
        
    }

    /**
     * @return string
     */
    public function GetFullName () {
        return $this->firstName." ".$this->lastName;
    }

    /**
     * @return bool
     */
    public function IsWantEmail(): bool {
        return $this->wantEmail;
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
     * @param string $firstName
     */
    public function SetFirstName(string $firstName) {
        $this->firstName = $firstName;
        $this->Update();
    }

    /**
     * @param string $lastName
     */
    public function SetLastName(string $lastName) {
        $this->lastName = $lastName;
        $this->Update();
    }

    /**
     * @param mixed $phoneNumber
     */
    public function SetPhoneNumber(string $phoneNumber) {
        $this->phoneNumber = $phoneNumber;
        $this->Update();
    }

    /**
     * @param int $wantEmail
     */
    public function SetWantEmail(int $wantEmail) {
        $this->wantEmail = (bool)$wantEmail;
        $this->Update();
    }

    public function UpdateRegular(array $updateData) {
        BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateData, 1);
    }


    /**
     * @return string
     */
    public function __toString() {
        return $this->firstName." ".$this->lastName." (".$this->email.")";
    }
}