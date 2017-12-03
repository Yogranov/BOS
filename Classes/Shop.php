<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:59
 */

namespace BugOrderSystem;

require_once "Seller.php";
require_once "Reminder.php";


class Shop
{
    /**
     * @var Shop[]
     */
    private static $shops = array();
    private static $loadedAll = false;

    const TABLE_NAME = "shops";
    const TABLE_KEY_COLUMN = "Id";
    const TABLE_NAME_COLUMN = "Name";


    private $id;
    private $password;
    private $shopName;
    private $location;
    /**
     * @var int
     */
    private $region;
    private $phoneNumber;
    private $manager;
    private $email;

    /**
     * @var Seller[]
     */
    private $sellers = array();

    /**
     * Shop constructor.
     * @param array $shopData
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    private function __construct(array $shopData) {
        $this->id = $shopData["Id"];
        $this->password = $shopData["Password"];
        $this->shopName = $shopData["Name"];
        $this->location = $shopData["Location"];
        $this->region = $shopData["Region"];
        $this->phoneNumber = $shopData["PhoneNumber"];
        $this->manager = Seller::GetById($shopData["Manager"]);
        $this->email = $shopData["Email"];

        $sellersArray = BugOrderSystem::GetDB()->where("ShopId", $shopData["Id"])->get("sellers", null, "Id");
        foreach ($sellersArray as $sellerId) {
            $this->sellers[$sellerId["Id"]] = &Seller::GetById($sellerId["Id"]);
        }
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
            $shopData = $dbObject->get(self::TABLE_NAME);

            foreach ($shopData as $shop) {
                if (!array_key_exists($shop[self::TABLE_KEY_COLUMN], self::$shops)) {
                    self::addShopByShopData($shop[self::TABLE_KEY_COLUMN], $shop);
                }
            }

            self::$loadedAll = true;
        }

        foreach (self::$shops as $shop) {
            call_user_func($function_doEachIteration, $shop);
        }
    }


    /**
     * @param $shopId
     * @param array $shopData
     * @return Shop
     * @throws \Exception
     */
    private static function addShopByShopData($shopId, array $shopData){
        $res = @self::$shops[$shopId];

        if(!empty($res))
            throw new Exception("Shop {0} already exists in this array", null, $res);

        if(count($shopData) == 0)
            throw new Exception("Shop {0} doesn't exists in DB", null, $shopId);

        self::$shops[$shopId] = new Shop($shopData);
        return self::$shops[$shopId];
    }


    /**
     * @param int $shopId
     * @return Shop
     * @throws \Exception
     */
    public static function &GetById(int $shopId) {
        if(empty($shopId))
            throw new Exception("Illegal Id!");

        $res = @self::$shops[$shopId];

        if(empty($res)) {
            $shopData = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $shopId)->getOne(self::TABLE_NAME);

            if(empty($shopData))
                throw new Exception("Shop Id ({0}) not founded, Shop doesn't exists", null, $shopId);

            $res = self::addShopByShopData($shopId, $shopData);
        }

        return $res;
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
    public function GetPassword() {
        return $this->password;
    }

    /**
     * @return string
     */
    public function GetShopName() {
        return $this->shopName;
    }

    /**
     * @return int
     */
    public function GetRegion() {
        return $this->region;
    }

    /**
     * @return string
     */
    public function GetLocation() {
        return $this->location;
    }

    /**
     * @return string
     */
    public function GetPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * @return Seller
     */
    public function GetManager() {
        return $this->manager;
    }


    /**
     * @return string
     */
    public function GetEmail() {
        return $this->email;
    }

    /**
     * @return Seller[]
     */
    public function GetActiveSellers() {
        $active = ESellerStatus::Active();
        $sellersArray = array();
        foreach ($this->sellers as $sellerId => $sellerObject) {
            if ($sellerObject->GetStatus() == $active) {
                $sellersArray[$sellerId] = $sellerObject;
            }
        }

        return $sellersArray;
    }

    /**
     * @return Seller[]
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    public function GetFiredSellers() {
        $fired = ESellerStatus::Fired();
        $sellersArray = array();
        foreach ($this->sellers as $sellerId => $sellerObject) {
            if ($sellerObject->GetStatus() == $fired) {
                $sellersArray[$sellerId] = &Seller::GetById($sellerId);
            }
        }

        return $sellersArray;
    }

    /**
     * @return string
     */
    public function __ToString() {
    return $this->id . " " . $this->shopName;

    }

    /**
     * @throws DBException
     * @throws \Exception
     */
    public function Remove() {
        $sucsses = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME);
        if(!$sucsses)
            throw new DBException("Unable to delete shop {0}", null, $this);

        $logText = "נמחקה החנות ".$this." מהמערכת";
        BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO());

        unset(self::$shops[$this->id]);
    }

    /**
     * @param array $shopData
     * @return shop
     * @throws \Exception
     */
    public static function AddShop(array $shopData) {
        $check = BugOrderSystem::GetDB()->where(self::TABLE_NAME_COLUMN, $shopData["Name"])->getOne(self::TABLE_NAME,1);
        if ($check != 0)
            throw new Exception("Cannot add shop, shop {0} is already exists.", null, $shopData["Name"]);

        $sqlSubject = BugOrderSystem::GetDB();
        $sucsses = $sqlSubject->insert(self::TABLE_NAME, $shopData);

        if(!$sucsses)
            throw new Exception("Ubable to insert a new shop to DB right now.");

        $res = &self::GetById($shopData["Id"]);
        $logText = "נוספה חנות חדשה בשם ".$res;
        BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO());
        return $res;
    }

    /**
     * @param Region $newRegion
     * @throws \Exception
     */
    public function ChangeRegion(Region $newRegion) {
        if (empty($newRegion))
            throw new Exception("Illegal Region Object!");

        if ($this->region == $newRegion->GetId())
            throw new Exception("Same region! unable to change!");

        $oldRegion = &Region::GetById($this->region);
        $this->region = $newRegion->GetId();
        $newRegion->AddShop($this);
        $oldRegion->RemoveShop($this);

        $logText = "החנות ".$this." עברה מאיזור ".$oldRegion." לאיזור ".$newRegion;
        BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO());
    }


    /**
     * @param string $message
     * @param string $subject
     * @param string $AttachedFile
     * @throws Exception
     * @throws \Exception
     */
    public function SendEmail(string $message, string $subject, string $AttachedFile = "") {
        if (empty($this->email))
            throw new Exception("Email not exist!", $this);

        $emailObject = BugOrderSystem::GetEmail($subject, $message);
        $emailObject->addAddress($this->email);
        //if (is_string($AttachedFile) && file_exists($AttachedFile)) {
        //\Services::dump($AttachedFile);
        //$emailObject->addAttachment($AttachedFile);
        //}

        if (!$emailObject->send())
            throw new Exception($emailObject->ErrorInfo);

        $logText = "אימייל נשלח אל החנות ".$this;
        BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO(), array("Subject" => $subject, "Message" => $message));
    }

    /**
     * @return Seller[]
     */
    public function GetSellers () {
        return $this->sellers;
    }

    /**
     * @param $shopName
     * @param bool $update
     * @throws Exception
     */
    public function SetShopName($shopName, bool $update = true) {
        $this->shopName = $shopName;
        if ($update)
            $this->Update();
    }

    /**
     * @param $location
     * @param bool $update
     * @throws Exception
     */
    public function SetLocation($location, bool $update = true) {
        $this->location = $location;
        if ($update)
            $this->Update();
    }

    /**
     * @param $region
     * @param bool $update
     * @throws Exception
     */
    public function SetRegion($region, bool $update = true) {
        $this->region = $region;
        if ($update)
            $this->Update();
    }

    /**
     * @param $phoneNumber
     * @param bool $update
     * @throws Exception
     */
    public function SetPhoneNumber($phoneNumber, bool $update = true) {
        $this->phoneNumber = $phoneNumber;
        if ($update)
            $this->Update();
    }

    /**
     * @param $email
     * @param bool $update
     * @throws Exception
     */
    public function SetEmail($email, bool $update = true) {
        $this->email = $email;
        if ($update)
            $this->Update();
    }

    /**
     * @param Seller $manager
     * @param bool $update
     * @throws Exception
     */
    public function SetManager(Seller $manager, bool $update = true) {
        $this->manager = $manager;
        if ($update)
            $this->Update();
    }


    /**
     * @throws Exception
     * @throws \Exception
     */
    public function Update() {
        $updateArray = array(
            "Name" => $this->shopName,
            "Location" => $this->location,
            "PhoneNumber" => $this->phoneNumber,
            "Manager" => $this->GetManager()->GetId(),
            "Email" => $this->email,
            "Region" => $this->region
        );
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new Exception("לא ניתן לעדכן את {0}", $updateArray, $this);

        $logText = "החנות ".$this." עודכנה";
        BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO(), $updateArray);
    }



}


