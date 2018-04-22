<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 17-Apr-18
 * Time: 16:09
 */

namespace BugOrderSystem;
use Log\ELogLevel;


class Family {

    private static $families = array();
    private static $loadedAll = false;

    const TABLE_NAME = "families";
    const TABLE_KEY_COLUMN = "Id";

    private $id;
    private $name;
    /**
     * @var Seller
     */
    private $responsibleSeller;

    /**
     * Family constructor.
     * @param array $familyData
     * @throws DBException
     * @throws Exception
     */
    private function __construct(array $familyData) {
        $this->id = $familyData["Id"];
        $this->name = $familyData["Name"];
        $this->responsibleSeller = &Seller::GetById($familyData["Responsible"]);
    }

    /**
     * @param int $familyId
     * @param array $familyData
     * @return mixed
     * @throws DBException
     * @throws Exception
     */
    private static function addFamilyByFamilyData(int $familyId, array $familyData){
        $res = @self::$families[$familyId];
        if(!empty($res))
            throw new Exception("Family {0} already exists on this array",null,$familyId);

        if(count($familyData) == 0)
            throw new DBException("Family {0} doesn't exists on DB",null,$familyId);

        self::$families[$familyId] = new Family($familyData);
        return self::$families[$familyId];
    }

    /**
     * @param int $familyId
     * @return Family
     * @throws DBException
     * @throws Exception
     */
    public static function &GetById(int $familyId) {
        if (empty($familyId))
            throw new Exception("Illegal Id! ({0})",null, $familyId);

        $res = @self::$families[$familyId];

        if (empty($res)) {
            $familyData = BugOrderSystem::GetDb()->where(self::TABLE_KEY_COLUMN, $familyId)->getOne(self::TABLE_NAME);
            if (empty($familyData))
                throw new DBException("No family data found! family {0} not exist!", null, $familyId);

            $res = self::addFamilyByFamilyData($familyId, $familyData);
        }
        return $res;
    }

    /**
     * @param callable $function_doEachIteration
     * @param array $OrderByArray
     * @throws DBException
     * @throws Exception
     */
    public static function LoopAll(callable $function_doEachIteration, array $OrderByArray = array()) {
        if (!self::$loadedAll) {
            $dbObject = BugOrderSystem::GetDB();
            foreach ($OrderByArray as $orderBy) {
                $dbObject->orderBy($orderBy[0], $orderBy[1]);
            }
            $familyData = $dbObject->get(self::TABLE_NAME);

            foreach ($familyData as $family) {
                if (!array_key_exists($family[self::TABLE_KEY_COLUMN], self::$families)) {
                    self::addFamilyByFamilyData($family[self::TABLE_KEY_COLUMN], $family);
                }
            }

            self::$loadedAll = true;
        }

        foreach (self::$families as $family) {
            call_user_func($function_doEachIteration, $family);
        }
    }

    /**
     * @param Seller $seller
     * @return Family[]
     * @throws DBException
     * @throws Exception
     */
    public static function GetSellerResponsibility(Seller $seller) {
        $resposibleFamily = array();
        self::LoopAll(function (Family $family) use (&$resposibleFamily, $seller) {
            if ($family->responsibleSeller === $seller)
                array_push($resposibleFamily, $family);
        });

        return $resposibleFamily;
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
    public function GetName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString() {
        return "משפחה " . $this->GetName() . " (" . $this->GetId() . ")";
    }


}