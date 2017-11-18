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
     * @return mixed
     * @throws \Exception
     */
    private static function addShopByShopData($shopId, array $shopData){
        $res = @self::$shops[$shopId];

        if(!empty($res))
            throw new \Exception("Shop already exists in this array");

        if(count($shopData) == 0)
            throw new \Exception("Shop doesn't exists on DB");

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
            throw new \Exception("Iligel Id");

        $res = @self::$shops[$shopId];

        if(empty($res)) {
            $shopData = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $shopId)->getOne(self::TABLE_NAME);

            if(empty($shopData))
                throw new \Exception("Shop Id not founded, Shop doesn't exists");

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

    public function GetPassword() {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function GetShopName() {
        return $this->shopName;
    }

    /**
     * @return Region
     */
    public function GetRegion() {
        return $this->region;
    }

    /**
     * @return mixed
     */
    public function GetLocation() {
        return $this->location;
    }

    /**
     * @return mixed
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
     * @return mixed
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
     * @throws \Exception
     */
    public function Remove() {
        $sqlSubject = BugOrderSystem::GetDB();
        $sucsses = $sqlSubject()->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME);
        if(!$sucsses)
            throw new \Exception("Unable to delete this shop right now.");

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
            throw new Exception("Cannot add shop, shop %1 is already exists.", null,$shopData["Name"]);

        $sqlSubject = BugOrderSystem::GetDB();
     $sucsses = $sqlSubject->insert(self::TABLE_NAME, $shopData);

     if(!$sucsses)
         throw new \Exception("Ubable to insert a new shop to DB right now.");

     $res = &self::GetById($sucsses);
     return $res;
    }

    /**
     * @param Region $newRegion
     * @throws \Exception
     */
    public function ChangeRegion(Region $newRegion) {
        if (empty($newRegion))
            throw new \Exception("Illegal Region Object!");

        if ($this->region == $newRegion->GetId())
            throw new \Exception("Same region! unable to change!");

        $oldRegion = &Region::GetById($this->region);
        $this->region = $newRegion->GetId();
        $newRegion->AddShop($this);
        $oldRegion->RemoveShop($this);
    }


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
            throw new \Exception($emailObject->ErrorInfo);
    }


    /**
     * @return Seller[]
     */
    public function GetSellers () {
        return $this->sellers;
    }

    /**
     * @param mixed $shopName
     */
    public function SetShopName($shopName) {
        $this->shopName = $shopName;
        $this->Update();
    }

    /**
     * @param mixed $location
     */
    public function SetLocation($location) {
        $this->location = $location;
        $this->Update();
    }

    /**
     * @param mixed $region
     */
    public function SetRegion($region) {
        $this->region = $region;
        $this->Update();
    }

    /**
     * @param mixed $phoneNumber
     */
    public function SetPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
        $this->Update();
    }

    /**
     * @param mixed $email
     */
    public function SetEmail($email) {
        $this->email = $email;
        $this->Update();
    }


    /**
     * @param mixed $manager
     */
    public function SetManager(Seller $manager) {
        $this->manager = $manager;
        $this->Update();
    }


    /**
     * @throws Exception
     */
    private function Update() {

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
    }



}


