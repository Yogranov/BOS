<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 28-Oct-17
 * Time: 17:30
 */

namespace BugOrderSystem;


class Region
{
    /**
     * @var Region[]
     */
    private static $regions = array();

    const TABLE_NAME = "regions";
    const TABLE_KEY_COLUMN = "Id";
    const TABLE_NAME_COLUMN = "Name";


    private $id;
    private $name;
    private $password;
    private $manager;

    /**
     * @var Shop[]
     */
    private $shops = array();

    /**
     * Shop constructor.
     * @param array $regionData
     */
    private function __construct(array $regionData)
    {
        $this->id = (int)$regionData["Id"];
        $this->name = $regionData["Name"];
        $this->password = $regionData["Password"];
        $this->manager = &Seller::GetById($regionData["Manager"]);

        $shopsId = BugOrderSystem::GetDB()->where("Region", $this->id)->get("shops", null, "Id");
        foreach ($shopsId as $shop) {
            $shopObj = &Shop::GetById($shop["Id"]);
            $this->shops[$shop["Id"]] = $shopObj;
        }

    }


    /**
     * @param int $regionId
     * @return Region
     * @throws Exception
     */
    public static function &GetById(int $regionId) {
        if(empty($regionId))
            throw new Exception("Iligel Id ({0})",null, $regionId);

        $res = @self::$regions[$regionId];

        if(empty($res)) {
            $regionData = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $regionId)->getOne(self::TABLE_NAME);

            if(empty($regionData))
                throw new Exception("Region Id ({0}) not founded, Region doesn't exists.",null,$regionId);

            $res = self::addRegionByRegionData($regionId, $regionData);
        }
        return $res;
    }

    /**
     * @param int $regionId
     * @param array $regionData
     * @return Region
     * @throws Exception
     */
    private static function addRegionByRegionData(int $regionId, array $regionData){
        $res = @self::$regions[$regionId];

        if(!empty($res))
            throw new Exception("Region {0} already exists in this array",null,$regionId);

        if(count($regionData) == 0)
            throw new Exception("Region {0} doesn't exists on DB",null,$regionId);

        self::$regions[$regionId] = new Region($regionData);
        return self::$regions[$regionId];

    }

    /**
     * @param array $regionData
     * @return Region
     * @throws Exception
     */
    public static function Add(array $regionData) {
        $check = BugOrderSystem::GetDB()->where(self::TABLE_NAME_COLUMN, $regionData["Name"])->getOne(self::TABLE_NAME,1);
        if ($check != 0)
            throw new Exception("Cannot add region, region %1 is already exists.", null,$regionData["Name"]);

        $sqlSubject = BugOrderSystem::GetDB();
        $sucsses = $sqlSubject->insert(self::TABLE_NAME, $regionData);

        if(!$sucsses)
            throw new Exception("Ubable to insert a new region to DB right now.");

        $res = &self::GetById($sucsses);
        return $res;
    }

    /**
     * @throws Exception
     */
    public function Remove() {
        $sqlSubject = BugOrderSystem::GetDB();
        $sucsses = $sqlSubject->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME);
        if(!$sucsses)
            throw new Exception("Unable to delete this region right now.");

        unset(self::$regions[$this->id]);
    }

    /**
     * @return int
     */
    public function GetId() {
        return $this->id;
    }

    /**
     * @param Shop $shop
     * @throws Exception
     */
    public function AddShop(Shop $shop) {
        if (array_key_exists($shop->GetId(), $this->shops))
            throw new Exception("Unable to add shop %1 to the region %2 - shop already included",null, $shop, $this);

        $this->shops[$shop->GetId()] = $shop;
    }

    /**
     * @param Shop $shop
     * @throws Exception
     */
    public function RemoveShop(Shop $shop) {
        if (!array_key_exists($shop->GetId(), $this->shops))
            throw new Exception("Unable to remove shop %1 from region %2 - shop not included",null, $shop, $this);

        unset($this->shops[$shop->GetId()]);
    }

    /**
     * @return mixed
     */
    public function GetName() {
        return $this->name;
    }

    /**
     * @return Seller
     */
    public function GetManager(): Seller {
        return $this->manager;
    }

    /**
     * @return Shop[]
     */
    public function GetShops(): array {
        return $this->shops;
    }



    public function __toString() {
        return $this->name.' ('.$this->id.')';
    }


}