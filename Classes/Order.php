<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:57
 */

namespace BugOrderSystem;

require_once "Products.php";
require_once "OrderProducts.php";

use Log\ELogLevel;

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
    private $timeStamp;
    private $statusUpdateTimestamp;
    private $OrderInnerStatus;
    private $emailNotification;
    /**
     * @var EOrderStatus
     */
    private $status;
    /**
     * @var OrderProducts[]
     */
    private $orderProducts = array();

    /**
     * Order constructor.
     * @param array $orderData
     * @throws \Exception
     */
    private function __construct(array $orderData) {
        //\Services::dump($orderData);
        $this->id = $orderData["OrderId"];
        $this->clientId = $orderData["ClientId"];
        $this->shopId = $orderData["ShopId"];
        $this->sellerId = $orderData["SellerId"];
        $this->remarks = $orderData["Remarks"];
        $this->timeStamp = new \DateTime($orderData["Timestamp"]);

        $this->JsonStringToOrderProduct($orderData["products"]);

        if (in_array($orderData["Status"], EOrderStatus::toArray()))
            $this->OrderInnerStatus = EOrderStatus::search($orderData["Status"]);

        $this->status = $this->GetStatus();
        $this->emailNotification = $orderData["Email"];
        $this->statusUpdateTimestamp = new \DateTime($orderData["LastStatusUpdateTimestamp"]);
    }

    /**
     * @param int $orderId
     * @param array $orderData
     * @return Order
     * @throws Exception
     * @throws \Exception
     */
    private static function AddOrderByOrderData(int $orderId, array $orderData)
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
     * @return string
     */
    private function convertOrderProductArrayToJsonString() {
        if (is_array($this->orderProducts) && count($this->orderProducts) > 0) {
            $jsonArray = array();
            foreach ($this->orderProducts as $barcode => $orderProduct) {
                $jsonArray[$barcode] = array($orderProduct->GetQuantity(), $orderProduct->GetStatus()->getValue(), $orderProduct->GetRemarks());
            }
            if (is_array($jsonArray) && count($jsonArray) > 0)
                return json_encode($jsonArray);
        }

        return json_encode("");
    }

    /**
     * @param string|null $jsonProducts
     * @throws Exception
     * @throws \Exception
     */
    private function JsonStringToOrderProduct(string $jsonProducts = null) {
        if (is_null($jsonProducts))
            return;

        $orderProductsJson = (array)@json_decode($jsonProducts);
        foreach ($orderProductsJson as $productBarcode => $productArray) {
            list($quantity, $status, $remarks) = $productArray;
            $productObject = &Products::GetByBarcode($productBarcode);
            $this->innerAddOrderProduct($productObject, $quantity, $remarks, EProductStatus::search($status), false);
        }
    }

    /**
     * @param int $orderId
     * @return Order
     * @throws Exception
     * @throws \Exception
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
     * @param Client $client
     * @param Shop $shop
     * @param Seller $seller
     * @param string $remarks
     * @param string|null $emailNotification
     * @return Order
     * @throws Exception
     * @throws \Exception
     */
    public static function &Add(Client $client, Shop $shop, Seller $seller, string $remarks = "", string $emailNotification = null)
    {
        $now = new \DateTime('now', new \DateTimeZone(Constant::SYSTEM_TIMEZONE));
        $currentTime = $now->format("Y-m-d H:i:s");

        $OrderData= array(
            "ClientId" => $client->GetId(),
            "ShopId" => $shop->GetId(),
            "SellerId" => $seller->GetId(),
            "Remarks" => $remarks,
            "Email" => $emailNotification,
            "Status" => 0,
            "Timestamp" => $currentTime,
            "LastStatusUpdateTimestamp" => $currentTime
        );

        $success = BugOrderSystem::GetDB()->insert(self::TABLE_NAME, $OrderData);
        if (!$success)
            throw new Exception("Unable to Add new order", $OrderData);

        $logText = "הזמנה חדשה מספר {orderId} נוספה ל{shop} על-ידי {seller}";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("orderId" => $success, "shop" => $shop, "seller" => $seller));

        $res = &self::GetById($success);
        return $res;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function Remove() {
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->delete(self::TABLE_NAME, 1);
        if (!$success)
            throw new Exception("לא ניתן למחוק את {0}!", null, $this);

        unset(self::$orders[$this->id]);

        $logText = "ה{order} נמחקה!";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("order" => $this));
    }

    /**
     * @param callable $function_doEachIteration
     * @param array $OrderByArray
     * @throws Exception
     * @throws \Exception
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
     * @throws Exception
     * @throws \Exception
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
     * @throws Exception
     * @throws \Exception
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
     * @return Order[]
     * @throws Exception
     * @throws \Exception
     */
    public static function GetArrivedOrders(Shop $shop, \DateTime $since = null) {
        $shopOrdersOld = array();
        Order::LoopAll(function (Order $order) use ($shop, &$shopOrdersOld, $since) {
            if ($order->GetShop() === $shop) {
                if($order->GetStatus() == EOrderStatus::Delivered()) {
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
     * @param \DateTime|null $since
     * @return Order[]
     * @throws Exception
     * @throws \Exception
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
     * @throws Exception
     * @throws \Exception
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
     * @return Order[]
     * @throws Exception
     * @throws \Exception
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
     * @throws Exception
     * @throws \Exception
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
     * @return int
     */
    public function GetId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function GetNotificationEmail() {
        return $this->emailNotification;
    }

    /**
     * @param string $message
     * @param string $subject
     * @param string $AttachedFile
     * @throws Exception
     * @throws \Exception
     */
    public function SendEmail(string $message, string $subject, string $AttachedFile = "") {
        if (empty($this->emailNotification))
            throw new Exception("Email not exist!", $this);

        $emailObject = BugOrderSystem::GetEmail($subject, $message);
        $emailObject->addAddress($this->emailNotification, $this->GetClient()->GetFullName());
        //if (is_string($AttachedFile) && file_exists($AttachedFile)) {
        //\Services::dump($AttachedFile);
        //$emailObject->addAttachment($AttachedFile);
        //}

        if (!$emailObject->send())
            throw new Exception($emailObject->ErrorInfo);

        $logText = "נשלח מייל אל הלקוח מהזמנה {client} לכתובת {emailAddress}";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("client" => $this->id, "emailAddress" => $this->emailNotification));
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
     * @return string
     */
    public function GetRemarks() {
        return (string)$this->remarks;
    }

    /**
     * @return EOrderStatus
     * @throws \Exception
     */
    public function GetStatus() {
        //status calculation//
        if (is_array($this->orderProducts) && count($this->orderProducts) > 0) {
            $leastProductStatus = "";
            $mapKeys = array_keys(Constant::ORDER_PRODUCT_STATUS_TO_ORDER_STATUS_MAP);

            foreach ($this->orderProducts as $barcode => $productObject) {
                $ProductStatus = $productObject->GetStatus();
                if (!empty($leastProductStatus)) {
                    $currentProductLoc = @array_search($ProductStatus->getName(), $mapKeys);
                    if ($currentProductLoc !== false && $currentProductLoc > @array_search($leastProductStatus->getName(), $mapKeys))
                        continue;
                }

                $leastProductStatus = $ProductStatus;
            }

            if (!empty($leastProductStatus)) {
                $orderCalculatedStatusKey = Constant::ORDER_PRODUCT_STATUS_TO_ORDER_STATUS_MAP[$leastProductStatus->getName()];
                $this->status = EOrderStatus::$orderCalculatedStatusKey();
                return $this->status;
            }
        }

        $this->status = EOrderStatus::Unknown();
        return $this->status;
    }

    /**
     * @return \DateTime
     */
    public function GetTimeStamp() {
        return $this->timeStamp;
    }

    /**
     * @return Client
     * @throws \Exception
     */
    public function GetClient() {
        $clientObject = &Client::GetById($this->clientId);
        return $clientObject;
    }

    /**
     * @return OrderProducts[]
     */
    public function GetOrderProducts() {
        return $this->orderProducts;
    }

    /**
     * @param Products $product
     * @param int $quantity
     * @param string|null $remarks
     * @throws Exception
     * @throws \Exception
     */
    public function AddOrderProduct(Products $product, int $quantity = 1, string $remarks = null) {
        $this->innerAddOrderProduct($product, $quantity, $remarks);
    }
    /**
     * @param Products $product
     * @param int $quantity
     * @param string|null $remarks
     * @param EProductStatus|null $status
     * @param bool $log
     * @throws Exception
     * @throws \Exception
     */
    private function innerAddOrderProduct(Products $product, int $quantity = 1, string $remarks = null, EProductStatus $status = null, bool $log = true) {
        if (is_array($this->orderProducts) && array_key_exists($product->GetBarcode(), $this->orderProducts))
            throw new Exception("{0} כבר קיים ב{1}!", null, $product, $this);

        $newProduct = false;
        if (is_null($status)) {
            $status = EProductStatus::Created();
            $newProduct = True;
        }

        $orderProduct = new OrderProducts($this->id, $product, $quantity, $status, $remarks);
        $this->orderProducts[$product->GetBarcode()] = $orderProduct;
        $this->ProductsUpdate(false, $newProduct);

        if ($log) {
            $logText = "{product} נוסף ל{order} בכמות {quantity}";
            BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("order" => $this, "product" => $product, "quantity" => $quantity));
        }
    }

    /**
     * @throws Exception
     */
    private function setStatusUpdateTimestamp() {
        $now = new \DateTime('now', new \DateTimeZone(Constant::SYSTEM_TIMEZONE));
        BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, array("LastStatusUpdateTimestamp" => $now->format("Y-m-d H:i:s")), 1);
        $this->statusUpdateTimestamp = $now;
    }

    /**
     * @return \DateTime
     */
    public function GetStatusUpdateTimestamp() {
        return $this->statusUpdateTimestamp;
    }

    /**
     * @param int $sellerId
     * @param bool $update
     * @throws Exception
     * @throws \Exception
     */
    public function SetSellerId(int $sellerId, bool $update = true) {
        $this->sellerId = $sellerId;
        if ($update)
            $this->Update();
    }

    /**
     * @param string $remarks
     * @param bool $update
     * @throws Exception
     * @throws \Exception
     */
    public function SetRemarks(string $remarks, bool $update = true) {
        $this->remarks = $remarks;
        if ($update)
            $this->Update();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function Update() {
        $updateArray = array(
            "SellerId" => $this->sellerId,
            "products" => $this->convertOrderProductArrayToJsonString(),
            "Remarks" => $this->remarks
        );
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new Exception("לא ניתן לעדכן את {0}", $updateArray, $this);

        $logText = "{order} עודכן!";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("order" => $this, "OrderArray" => $updateArray));
    }

    /**
     * @param bool $log
     * @param bool $statusChange
     * @throws Exception
     * @throws \Exception
     */
    public function ProductsUpdate(bool $log = true, bool $statusChange = false) {
        $jsonString = $this->convertOrderProductArrayToJsonString();
        $updateArray = array(
            "products" => $jsonString
        );
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new Exception("לא ניתן לעדכן את המוצרים של {0}", $updateArray, $this);

        if ($log) {
            $logText = " עודכנו המוצרים {products} של {order}!";
            BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("order" => $this, "products"=> array_keys($this->GetOrderProducts())));
        }

        if ($statusChange)
            $this->setStatusUpdateTimestamp();
    }

    /**
     * @return string
     */
    public function __toString() {
        return "הזמנה " . $this->id;
    }
}