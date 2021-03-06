<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:58
 */

namespace BugOrderSystem;

use Log\ELogLevel;

class Seller {

    private static $sellers = array();
    private static $loadedAll = false;

    const TABLE_KEY_COLUMN = "Id";
    const TABLE_NAME = "sellers";

    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $email;
    /**
     * @var ESellerStatus
     */
    private $sellerStatus;

    /**
     * Seller constructor.
     * @param array $sellerData
     * @throws \Exception
     */
    private function __construct(array $sellerData) {
        $this->id = $sellerData["Id"];
        $this->firstName = $sellerData["FirstName"];
        $this->lastName = $sellerData["LastName"];
        $this->email = $sellerData["Email"];
        $this->sellerStatus = ESellerStatus::search($sellerData["Status"]);
    }

    /**
     * @param int $sellerId
     * @param array $sellerData
     * @return Seller
     * @throws DBException
     * @throws \Exception
     */
    private static function addSellerBySellerData(int $sellerId, array $sellerData){
        $res = @self::$sellers[$sellerId];
        if(!empty($res))
            throw new Exception("Seller {0} already exists on this array",null,$sellerId);

        if(count($sellerData) == 0)
            throw new DBException("Seller {0} doesn't exists on DB",null,$sellerId);

        self::$sellers[$sellerId] = new Seller($sellerData);
        return self::$sellers[$sellerId];
    }


    /**
     * @param int $sellerId
     * @return Seller
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    public static function &GetById(int $sellerId) {
        if (empty($sellerId))
            throw new Exception("Illegal Id! ({0})",null, $sellerId);

        $res = @self::$sellers[$sellerId];

        if (empty($res)) {
            $sellerData = BugOrderSystem::GetDb()->where(self::TABLE_KEY_COLUMN, $sellerId)->getOne(self::TABLE_NAME);
            if (empty($sellerData))
                throw new DBException("No seller data found! seller {0} not exist!", null, $sellerId);

            $res = self::addSellerBySellerData($sellerId, $sellerData);
        }
        return $res;
    }

    /**
     * @param callable $function_doEachIteration
     * @param array $OrderByArray
     * @throws Exception
     * @throws \Exception
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
     * @return int|mixed
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
     * @return string
     */
    public function GetFirstName() {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function GetLastName() {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function GetEmail() {
        return $this->email;
    }

    /**
     * @param array $sellerData
     * @return Seller
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     * Todo: need to change the array property to proper variables properties
     */
    public static function Add(array $sellerData){
        $sqlObject = BugOrderSystem::GetDB();
        if($sqlObject->where("Id",$sellerData["Id"])->getOne(self::TABLE_NAME))
            throw new Exception("???? ???????? ???????? ?????????? ????, ???????? {0} ?????? ???????? ????????????", $sellerData, $sellerData["Id"]);
        $success = $sqlObject->insert(self::TABLE_NAME, $sellerData);
        if (!$success)
            throw new DBException("Unable to add seller!", $sellerData);
        $res = &self::getById($sellerData["Id"]);

        $logText = "?????????? {seller} ???????? ?????????? {shop}";
        $shop = &Shop::GetById($sellerData["ShopId"]);
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("seller" => $res, "shop" => $shop, "data" => $sellerData));

        return $res;
    }

    /**
     * @throws DBException
     * @throws \Exception
     */
    public function Fire() {
        $responsibleFamilies = Family::GetSellerResponsibility($this);
        if (count($responsibleFamilies) > 0)
            throw new Exception("Unable to Fire seller {0} because he is responsible over {1} product families!", $responsibleFamilies, $this, count($responsibleFamilies));

        $this->sellerStatus = ESellerStatus::Fired();
        $this->update();

        $logText = "?????????? {seller} ????????";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("seller" => $this));
    }

    /**
     * @throws DBException
     * @throws \Exception
     */
    public function BackToWork() {
        $this->sellerStatus = ESellerStatus::Active();
        $this->update();

        $logText = "?????????? {seller} ?????? ??????????";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("seller" => $this));
    }

    /**
     * @throws DBException
     * @throws \Exception
     */
    public function Remove() {
        $responsibleFamilies = Family::GetSellerResponsibility($this);
        if (count($responsibleFamilies) > 0)
            throw new Exception("Unable to delete seller {0} because he is responsible over {1} product families!", $responsibleFamilies, $this, count($responsibleFamilies));

        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME, 1);
        if(!$success)
            throw new DBException("Unable to remove seller {0}", null, $this);

        unset(self::$sellers[$this->id]);

        $logText = "?????????? {seller} ???????? ??????????????";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("seller" => $this));
    }

    /**
     * @param string $newEmail
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    public function ChangeEmail(string $newEmail) {
        $valid = \PHPMailer::validateAddress($newEmail);
        if(!$valid)
            throw new Exception("Illegal email address ({0})",null, $newEmail);

        $this->email = $newEmail;
        $this->update();
    }

    /**
     * @param string $message
     * @param string $subject
     * @param array|null $AttachedFiles
     * @param bool $log
     * @throws Exception
     * @throws \Exception
     */
    public function SendEmail(string $message, string $subject, array $AttachedFiles = null, bool $log = true) {
        if (empty($this->email))
            throw new Exception("Unable to send email to {0} email not exist!", null, $this);

        $emailObject = BugOrderSystem::GetEmail($subject, $message);
        $emailObject->addAddress($this->email, $this->firstName." ".$this->lastName);
        foreach ($AttachedFiles as $file) {
            if ($file->isFile())
                $emailObject->addAttachment($file->getRealPath(), $file->getFilename(), "base64", $file->getType());
        }

        if (!$emailObject->send())
            throw new Exception($emailObject->ErrorInfo);

        if ($log) {
            $logText = "???????????? ???????? ???? ???????????? {seller}";
            BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO(), array("seller" => $this , "Email" => $emailObject));
        }
    }

    /**
     * @throws DBException
     * @throws \Exception
     */
    private function update() {
        $updateArray = array(
            "FirstName" => $this->firstName,
            "LastName" => $this->lastName,
            "Email" => $this->email,
            "Status" => $this->sellerStatus->getValue()
        );

        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new DBException("{0} ???? ???????? ?????????? ????", $updateArray, $this);

        $logText = "?????????? ?????????? {seller}";
        BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO(), array("updateArray" => $updateArray, "seller" => $this));
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->GetFullName() .' ('. $this->id .')';
    }


}