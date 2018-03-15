<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 01-Oct-17
 * Time: 17:30
 */

namespace BugOrderSystem;


class Reminder {

    private static $reminders = array();
    private static $loadedAll = false;

    const TABLE_NAME = "reminders";
    const TABLE_KEY_COLUMN = "Id";
    const  TABLE_SHOP_COLUMN = "Shop";

    private $id;
    private $remind;
    private $sellerId;
    private $shopId;
    private $timestamp;


    /**
     * Reminder constructor.
     * @param array $remindData
     */
    private function __construct(array $remindData) {
        $this->id = $remindData["Id"];
        $this->remind = $remindData["Remind"];
        $this->sellerId = $remindData["Seller"];
        $this->shopId = $remindData["Shop"];
        $this->timestamp = new \DateTime($remindData["Timestamp"]);
    }


    /**
     * @param $remindId
     * @param $remindData
     * @return mixed
     * @throws Exception
     */
    private static function AddRemindByRemindData($remindId, $remindData)
    {
        $res = @self::$reminders[$remindId];

        if (!empty($res))
            throw new Exception("Remind {0} already exists on this array!", $res, $remindId);

        if (count($remindData) == 0)
            throw new Exception("No data found on this remind {0}, remind {0} not exists!", null, $remindId);

        self::$reminders[$remindId] = new Reminder($remindData);
        return self::$reminders[$remindId];

    }

    /**
     * @param int $remindId
     * @return Reminder
     * @throws Exception
     */
    public static function &GetById(int $remindId)
    {
        if (empty($remindId))
            throw new Exception("Illegal remind Id {0}!", null, $remindId);

        $res = @self::$reminders[$remindId];
        if (empty($res)) {
            $remindData = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $remindId)->getOne(self::TABLE_NAME);

            if (empty($remindData))
                throw new Exception("No data found on remind {0}!", null, $remindId);

            $res = self::AddRemindByRemindData($remindId, $remindData);
        }

        return $res;
    }


    /**
     * @param array $remindData
     * @return Reminder
     * @throws Exception
     */
    public static function Add(array $remindData) {
        $sqlObject = BugOrderSystem::GetDB();
        $success = $sqlObject->insert(self::TABLE_NAME, $remindData);

        if (!$success)
            throw new Exception("Unable to Add new remind.", $remindData);

        $res = &self::GetById($success);
        return $res;

    }


    /**
     * @throws Exception
     */
    public function Delete () {
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME);
        if(!$success) {
            throw new Exception("Cannot delete remind {0} right now!",null,$this->id);
        } else {
            unset(self::$reminders[$this->id]);
        }
    }

    /**
     * @param int $reminderId
     * @return bool
     * @throws Exception
     */
    public static function IsExist(int $reminderId) {
        return (bool)BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $reminderId)->count;
    }

    /**
     * @param callable $function_doEachIteration
     * @param array $OrderByArray
     * @throws Exception
     */
    public static function LoopAll(callable $function_doEachIteration, array $OrderByArray = array())
    {
        if (!self::$loadedAll) {
            $dbObject = BugOrderSystem::GetDB();
            foreach ($OrderByArray as $orderBy) {
                $dbObject->orderBy($orderBy[0], $orderBy[1]);
            }
            $remindData = $dbObject->get(self::TABLE_NAME);

            foreach ($remindData as $remind) {
                if (!array_key_exists($remind[self::TABLE_KEY_COLUMN], self::$reminders)) {
                    self::AddRemindByRemindData($remind[self::TABLE_KEY_COLUMN], $remind);
                }
            }

            self::$loadedAll = true;
        }

        foreach (self::$reminders as $remindObject) {
            call_user_func($function_doEachIteration, $remindObject);
        }
    }


    /**
     * @param Shop $shop
     * @return array
     * @throws Exception
     */
    public static function GetShopReminders(Shop $shop)
    {
        $shopReminders = array();
        Reminder::LoopAll(function (Reminder $reminder) use ($shop, &$shopReminders) {

            if ($reminder->GetShop() === $shop) {
                array_push($shopReminders, $reminder);
            }
        });
        return $shopReminders;
    }

    /**
     * @return array
     */
    public static function GetReminders(): array {
        return self::$reminders;
    }

    /**
     * @return int
     */
    public function GetId() {
        return $this->id;
    }


    /**
     * @return Shop
     * @throws \Exception
     */
    public function GetShop() {
        $shop = &Shop::GetById($this->shopId);
        return $shop;
    }

    /**
     * @return Seller
     * @throws DBException
     * @throws Exception
     * @throws \Exception
     */
    public function GetSeller() {
        $seller = &Seller::GetById($this->sellerId);
        return $seller;
    }

    /**
     * @return Reminder
     */
    public function GetRemind() {
        return $this->remind;
    }

    /**
     * @return \DateTime
     */
    public function GetTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param $shopId
     * @return array
     * @throws Exception
     */
    public static function GetAllReminds($shopId) {
        $allRemindsObj = BugOrderSystem::GetDB()->where(self::TABLE_SHOP_COLUMN,$shopId)->get(self::TABLE_NAME);
        $allRemindsArray = array();


        foreach ($allRemindsObj as $remind) {
            array_push($allRemindsArray, $remind);
        }

        return $allRemindsArray;
    }

}