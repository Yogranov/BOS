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
    private $responsible;

    /**
     * Family constructor.
     * @param array $familyData
     * @throws DBException
     * @throws Exception
     */
    private function __construct(array $familyData) {
        $this->id = $familyData["Id"];
        $this->name = $familyData["Name"];
        $this->responsible = $familyData["Responsible"];
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
     * @return Seller
     * @throws DBException
     * @throws Exception
     */
    public function GetResponsible(): Seller {
        return Seller::GetById($this->responsible);
    }



}