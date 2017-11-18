<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:58
 */

namespace BugOrderSystem;


class Seller {

    private static $sellers = array();
    private static $loadedAll = false;

    const TABLE_KEY_COLUMN = "Id";
    const TABLE_NAME = "sellers";

    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $sellerStatus;

    private  function __construct(array $sellerData) {
        $this->id = $sellerData["Id"];
        $this->firstName = $sellerData["FirstName"];
        $this->lastName = $sellerData["LastName"];
        $this->email = $sellerData["Email"];
        $this->sellerStatus = ESellerStatus::search($sellerData["Status"]);
    }


    private static function addSellerBySellerData(int $sellerId, array $sellerData){
        $res = @self::$sellers[$sellerId];
        if(!empty($res))
            throw new \Exception("Seller already exists on this array");

        if(count($sellerData) == 0)
            throw new \Exception("Seller doesn't exists on DB");

        $res = self::$sellers[$sellerId] = new Seller($sellerData);
        return $res;
    }

    /**
     * @param int $sellerId
     * @return Seller
     * @throws \Exception
     */
    public static function &GetById(int $sellerId) {
        if (empty($sellerId))
            throw new \Exception("Illegal Id!");

        $res = @self::$sellers[$sellerId];

        if (empty($res)) {
            $sellerData = BugOrderSystem::GetDb()->where(self::TABLE_KEY_COLUMN, $sellerId)->getOne(self::TABLE_NAME);
            if (empty($sellerData))
                throw new \Exception("No seller data found! seller not exist!");

            $res = self::addSellerBySellerData($sellerId, $sellerData);
        }
        return $res;
    }

    /**
     * @param callable $function_doEachIteration
     */
    public static function LoopAll(callable $function_doEachIteration, array $OrderByArray = array()) {
        if (!self::$loadedAll) {
            $dbObject = BugOrderSystem::GetDB();
            foreach ($OrderByArray as $orderBy) {
                $dbObject->orderBy($orderBy[0], $orderBy[1]);
            }
            $sellerData = $dbObject->get(self::TABLE_NAME);

            foreach ($sellerData as $seller) {
                if (!array_key_exists($seller[self::TABLE_KEY_COLUMN], self::$sellers)) {
                    self::addSellerBySellerData($seller[self::TABLE_KEY_COLUMN], $seller);
                }
            }

            self::$loadedAll = true;
        }

        foreach (self::$sellers as $sellerObject) {
            call_user_func($function_doEachIteration, $sellerObject);
        }
    }

    /**
     * @return array
     */
    public static function GetSellers(): array {
        return self::$sellers;
    }

    /**
     * @return ESellerStatus
     */
    public function GetStatus() {
        return $this->sellerStatus;
    }


    /**
     * @return mixed
     */
    public function GetId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function GetFullName() {
        return $this->firstName . " " . $this->lastName;
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
     * @param array $sellerData
     * @return Seller
     * @throws \Exception
     */
    public static function Add(array $sellerData){
        $sqlObject = BugOrderSystem::GetDB();
        $success = $sqlObject->insert(self::TABLE_NAME, $sellerData);
        if (!$success)
            throw new \Exception("Unable to add seller!\n\r ".$sqlObject->getLastError());
        $res = &self::getById($sellerData["Id"]);
        return $res;
    }


    /**
     * @throws \Exception
     */
    public function Fire() {
        $firedStatus = ESellerStatus::Fired();

        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, array("Status" => $firedStatus->getValue()), 1);
        if(!$success)
            throw new \Exception("Unable to fire seller id ".$this->id);

        $this->sellerStatus = $firedStatus;
    }

    public function BackToWork() {
        $ActiveStatus = ESellerStatus::Active();

        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, array("Status" => $ActiveStatus->getValue()), 1);
        if(!$success)
            throw new \Exception("Unable to fire seller id ".$this->id);

        $this->sellerStatus = $ActiveStatus;


    }
        /**
     * @throws \Exception
     */
    public function Remove() {
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME, 1);
        if(!$success)
            throw new \Exception("Unable to remove seller id ".$this->id);

        unset(self::$sellers[$this->id]);
    }

    /**
     * @param string $newEmail
     * @throws \Exception
     */
    public function ChangeEmail(string $newEmail) {
        $valid = \PHPMailer::validateAddress($newEmail);
        if(!$valid)
            throw new \Exception("Illegal email address.");

        $newEmailArray = array("Email" => $newEmail);

        $success = BugOrderSystem::GetDB()->where("Email", $this->email)->update(self::TABLE_NAME, $newEmailArray, 1);
        if(!$success)
            throw new \Exception("Unable to change email right now");

        $this->email = $newEmail;
    }

    /**
     * @param string $message
     * @param string $subject
     * @param string $AttachedFile
     * @throws \Exception
     */
    public function SendEmail(string $message, string $subject, string $AttachedFile = "") {
        if (empty($this->email))
            throw new Exception("Email not exist!", $this);

        $emailObject = BugOrderSystem::GetEmail($subject, $message);
        $emailObject->addAddress($this->email, $this->firstName." ".$this->lastName);
        //if (is_string($AttachedFile) && file_exists($AttachedFile)) {
        //\Services::dump($AttachedFile);
        //$emailObject->addAttachment($AttachedFile);
        //}

        if (!$emailObject->send())
            throw new \Exception($emailObject->ErrorInfo);
    }



    public function isExists() {
        if($this->id > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function Update(array $sellerData) {
        if (empty($sellerData))
            throw new \Exception("לא ניתן לעדכן מוכרן, חסר מידע");

        BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $sellerData);
    }



}