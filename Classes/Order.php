<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:57
 */

namespace BugOrderSystem;

require_once "OrderProducts.php";

class Order
{

    private static $orders = array();
    private static $loadedAll = false;

    const TABLE_NAME = "orders";
    const TABLE_KEY_COLUMN = "OrderId";

    private $id;
    private $clientId;
    private $shopId;
    private $sellerId;
    private $remarks;
    private $status = 0;
    private $timeStamp;
    private $updateTime;

    /**
     * @var OrderProducts[]
     */
    private $orderProducts = array();

    /**
     * Order constructor.
     * @param array $orderData
     */
    private function __construct(array $orderData)
    {
        $this->id = $orderData["OrderId"];
        $this->clientId = $orderData["ClientId"];
        $this->shopId = $orderData["ShopId"];
        $this->sellerId = $orderData["SellerId"];
        $this->remarks = $orderData["Remarks"];
        $this->status = $orderData["Status"];
        $this->timeStamp = new \DateTime($orderData["Timestamp"]);
        $this->updateTime = new \DateTime($orderData["updateTime"]);


        $orderArray = BugOrderSystem::GetDB()->where("OrderId", $orderData["OrderId"])->get("orderproducts", null);
        foreach ($orderArray as $orderProduct) {
            $this->orderProducts[$orderProduct["ProductId"]] = new OrderProducts($this->id, $orderProduct["ProductName"], $orderProduct["ProductBarcode"]);
        }
    }

    /**
     * @param $orderId
     * @param $orderData
     * @return mixed
     * @throws Exception
     */
    private static function AddOrderByOrderData($orderId, $orderData)
    {
        $res = @self::$orders[$orderId];

        if (!empty($res))
            throw new Exception("Order {0} already exists on this array!", $res, $orderId);

        if (count($orderData) == 0)
            throw new Exception("No data found on this order {0}, order {0} not exists!", null, $orderId);

        self::$orders[$orderId] = new Order($orderData);
        return self::$orders[$orderId];

    }

    /**
     * @param int $orderId
     * @return Order
     * @throws Exception
     */
    public static function &GetById(int $orderId)
    {
        if (empty($orderId))
            throw new Exception("Illegal order Id {0}!", null, $orderId);

        $res = @self::$orders[$orderId];

        if (empty($res)) {
            $orderData = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $orderId)->getOne(self::TABLE_NAME);

            if (empty($orderData))
                throw new Exception("No data found on order {0}!", null, $orderId);

            $res = self::AddOrderByOrderData($orderId, $orderData);
        }

        return $res;
    }

    /**
     * @param array $OrderData
     * @return Order
     * @throws Exception
     */
    public static function Add(array $OrderData)
    {
        $sqlObject = BugOrderSystem::GetDB();
        $success = $sqlObject->insert(self::TABLE_NAME, $OrderData);

        if (!$success)
            throw new Exception("Unable to Add new order.", $OrderData);

        $res = &self::GetById($success);
        return $res;

    }

    /**
     * @param callable $function_doEachIteration
     * @param array $OrderByArray
     */
    public static function LoopAll(callable $function_doEachIteration, array $OrderByArray = array())
    {
        if (!self::$loadedAll) {
            $dbObject = BugOrderSystem::GetDB();
            foreach ($OrderByArray as $orderBy) {
                $dbObject->orderBy($orderBy[0], $orderBy[1]);
            }
            $orderData = $dbObject->orderBy("Timestamp","DESC")->get(self::TABLE_NAME);

            foreach ($orderData as $order) {
                if (!array_key_exists($order[self::TABLE_KEY_COLUMN], self::$orders)) {
                    self::AddOrderByOrderData($order[self::TABLE_KEY_COLUMN], $order);
                }
            }

            self::$loadedAll = true;
        }

        foreach (self::$orders as $order) {
            call_user_func($function_doEachIteration, $order);
        }
    }

    /**
     * @param Shop $shop
     * @return Order[]
     */
    public static function GetShopOrders(Shop $shop)
    {
        $shopOrders = array();
        Order::LoopAll(function (Order $order) use ($shop, &$shopOrders) {
            if ($order->GetShop() === $shop) {
                array_push($shopOrders, $order);
            }
        });
        return $shopOrders;
    }

    /**
     * @param Shop $shop
     * @return Order[]
     */
    public static function GetOldOrders(Shop $shop) {
            $shopOrdersOld = array();
            Order::LoopAll(function (Order $order) use ($shop, &$shopOrdersOld) {
                if ($order->GetShop() === $shop) {
                    if($order->GetStatus() == EOrderStatus::Delivered() || $order->GetStatus() == EOrderStatus::Aborted()) {
                        array_push($shopOrdersOld, $order);
                    }
                }
            });
            return $shopOrdersOld;
    }

    /**
     * @param Shop $shop
     * @return array
     */
    public static function GetArrivedOrders(Shop $shop) {
        $shopOrdersOld = array();
        Order::LoopAll(function (Order $order) use ($shop, &$shopOrdersOld) {
            if ($order->GetShop() === $shop) {
                if($order->GetStatus() == EOrderStatus::Delivered()) {
                    array_push($shopOrdersOld, $order);
                }
            }
        });
        return $shopOrdersOld;
    }

    /**
     * @param Shop $shop
     * @param \DateTime|null $since
     * @return array
     */
    public static function GetCanceledOrders(Shop $shop, \DateTime $since = null) {
        $shopOrdersOld = array();
        Order::LoopAll(function (Order $order) use ($shop, &$shopOrdersOld, $since) {
            if ($order->GetShop() === $shop) {
                if($order->GetStatus() == EOrderStatus::Aborted()) {
                    if (empty($since) || ($order->GetTimeStamp() >= $since)) {
                        array_push($shopOrdersOld, $order);
                    }
                }
            }
        });
        return $shopOrdersOld;
    }

    /**
     * @param Shop $shop
     * @return Order[]
     */
    public static function GetActiveOrders(Shop $shop) {
        $shopOrdersActive = array();
        Order::LoopAll(function (Order $order) use ($shop, &$shopOrdersActive) {
            if ($order->GetShop() === $shop && $order->GetStatus() != EOrderStatus::Delivered() &&
                $order->GetStatus() != EOrderStatus::Aborted() &&
                $order->GetStatus() != EOrderStatus::Pre_order()) {
                array_push($shopOrdersActive, $order);
            }
        });
        return $shopOrdersActive;
    }

    /**
     * @param Shop $shop
     * @param $searchKey
     * @return array
     */
    public static function GetSearchOrders(Shop $shop, $searchKey) {
        $shopOrdersActive = array();
        Order::LoopAll(function (Order $order) use ($shop, &$shopOrdersActive, $searchKey) {
            if ($order->GetShop() === $shop) {
                if(strpos($order->GetClient()->GetFullName(),$searchKey) !== false ||
                    strpos($order->GetSeller()->GetFullName(),$searchKey) !== false ||
                    strpos($order->GetId(),$searchKey) !== false ||
                    strpos($order->GetClient()->GetPhoneNumber(),$searchKey) !== false) {
                    array_push($shopOrdersActive, $order);
                }
            }
        });
        return $shopOrdersActive;
    }


    /**
     * @param Shop $shop
     * @return Order[]
     */
    public static function GetPreOrders(Shop $shop) {
        $shopPreOrders = array();
        Order::LoopAll(function (Order $order) use ($shop, &$shopPreOrders) {
            if ($order->GetShop() === $shop) {
                if($order->GetStatus() == EOrderStatus::Pre_order()) {
                    array_push($shopPreOrders, $order);
                }
            }
        });
        return $shopPreOrders;
    }

    /**
     * @param string $ProductName
     * @param string $ProductBarcode
     * @param string $Remarks
     * @throws Exception
     */
    public function AddOrderProduct(string $ProductName, string $ProductBarcode, string $Remarks) {
        $orderProductObject = new OrderProducts($this->id, $ProductName, $ProductBarcode, $Remarks);
        if (array_key_exists($orderProductObject->GetId(),$this->orderProducts))
            throw new Exception("המוצר {0} כבר קיים בהזמנה של הלקוח ולכן לא ניתן להוסיפו!", $this->orderProducts, $orderProductObject);

        $this->orderProducts[$orderProductObject->GetId()] = $orderProductObject;
    }

    /**
     * @return int
     */
    public function GetId() {
        return $this->id;
    }

    /**
     * @return Shop
     */
    public function GetShop() {
        $shop = &Shop::GetById($this->shopId);
        return $shop;
    }

    /**
     * @return Seller
     */
    public function GetSeller() {
        $seller = &Seller::GetById($this->sellerId);
        return $seller;
    }

    /**
     * @return \DateTime
     */
    public function GetUpdateTime() {
        return $this->updateTime;
    }


    /**
     * @return string
     */
    public function GetRemarks() {
        return (string)$this->remarks;
    }

    /**
     * @return EOrderStatus
     */
    public function GetStatus() {
        $statusEnum = EOrderStatus::search($this->status);
        return $statusEnum;
    }

    /**
     * @return \DateTime
     */
    public function GetTimeStamp() {
        return $this->timeStamp;
    }

    /**
     * @return OrderProducts[]
     */
    public function GetOrderProducts() {
        return $this->orderProducts;
    }

    /**
     * @return string
     */
    public function __toString() {
        return "הזמנה " . $this->id;
    }

    /**
     * @return Client
     */
    public function GetClient() {
        $clientObject = &Client::GetById($this->clientId);
        return $clientObject;
    }

    public function ChangeStatus($status) {
        $info = array("Status" => $status);
        BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $info);

        $logText = "סטטוס {$this} השתנה";
        BugOrderSystem::GetLog()->Write($logText, \Log\ELogLevel::INFO(), array($status));

        $this->status = $status;
        return $this->status;
    }


    public function Update(array $data){
        if (empty($data))
            throw new \Exception("לא ניתן לעדכן הזמנה, חסר מידע");

        BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $data);
    }



}