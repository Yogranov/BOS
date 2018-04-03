<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 06-Jan-18
 * Time: 21:06
 */

namespace BugOrderSystem;

use Log\ELogLevel;

class Products {

    const TABLE_NAME = "products";
    const TABLE_KEY_COLUMN = "Barcode";

    /**
     * @var Products[]
     */
    private static $products = array();

    private $barcode;
    private $name;
    private $remarks;

    /**
     * Products constructor.
     * @param array $productData
     */
    private function __construct(array $productData) {
        //\Services::dump($productData);
        $this->barcode = $productData["Barcode"];
        $this->name = $productData["Name"];
        $this->remarks = $productData["Remark"];
    }

    /**
     * @param $barcode
     * @param $productData
     * @return mixed
     * @throws Exception
     */
    private static function AddProductByProductData($barcode, $productData) {
        $res = @self::$products[$barcode];

        if (!empty($res))
            throw new Exception("Product {0} already exists on this array!", $res, $barcode);

        if (count($productData) == 0)
            throw new Exception("No data found on this Product {0}, Product {0} not exists!", null, $barcode);

        self::$products[$barcode] = new Products($productData);
        return self::$products[$barcode];
    }

    /**
     * @return array
     * @throws Exception
     * @throws \Exception
     */
    private function getProductOrders() {
        $ordersArray = array();
        $productBarcode = $this->GetBarcode();
        Order::LoopAll(function(Order $order) use (&$ordersArray, $productBarcode) {
            $products = $order->GetOrderProducts();
            if (@array_key_exists($productBarcode, $products))
                array_push($ordersArray, $order->GetId());
        });

        return $ordersArray;
    }

    /**
     * @param string $barcode
     * @return Products
     * @throws Exception
     * @throws \Exception
     */
    public static function &GetByBarcode(string $barcode) {
        if (empty($barcode))
            throw new Exception("Illegal Barcode {0}!", null, $barcode);

        $res = @self::$products[$barcode];

        if (empty($res)) {
            $ProductData = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $barcode)->getOne(self::TABLE_NAME);

            if (empty($ProductData))
                throw new Exception("No data found on product with barcode {0}!", null, $barcode);

            $res = self::AddProductByProductData($barcode, $ProductData);
        }

        //$logText = "נשלף האובייקט של המוצר {Product}";
        //BugOrderSystem::GetLog()->Write($logText, ELogLevel::DEBUG(), array("Product" => $barcode));

        return $res;
    }

    /**
     * @param string $barcode
     * @param string $name
     * @param string|null $remarks
     * @return Products
     * @throws Exception
     * @throws \Exception
     */
    public static function &Add(string $barcode, string $name, string $remarks = null) {
        if (empty($barcode))
            throw new Exception("Illegal Barcode {0}!", null, $barcode);

        if (empty($name))
            throw new Exception("Illegal Product Name!", null);

        try {
            $productObject = &self::GetByBarcode($barcode);
        } catch(\Throwable $e) {
            //if error need to add in the DB
            $productData = array("Barcode" => $barcode, "Name" => \Services::StripString($name), "Remark" => $remarks);
            $success = BugOrderSystem::GetDB()->insert(self::TABLE_NAME, $productData);
            if (!$success)
                throw new Exception("Unable to Add new Product!", $productData);

            $logText = "נוסף מוצר חדש {productBarcode} {productName}";
            BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("productBarcode" => $productData["Barcode"] , "productName" => $productData["Name"]));

            $productObject = &self::GetByBarcode($barcode);
        }

        return $productObject;
    }

    /**
     * @param string $barcode
     * @return bool
     * @throws Exception
     */
    public static function IsExist(string $barcode) {
        if (!empty($barcode)) {
            BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $barcode)->getOne(self::TABLE_NAME);
            if (BugOrderSystem::GetDB()->count > 0)
                return True;
        }

        return False;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function Remove() {
        $productsOrders = $this->getProductOrders();
        if (count($productsOrders) > 0) {
            throw new Exception("לא ניתן למחוק את {0} כיוון שהוא משוייך להזמנה אחת או יותר!", $productsOrders, $this);
        }

        $success = BugOrderSystem::GetDB()->where($this->barcode, self::TABLE_KEY_COLUMN)->delete(self::TABLE_NAME, 1);
        if(!$success)
            throw new Exception("לא ניתן למחוק את {0}", $this, $this);

        unset(self::$products[$this->barcode]);
    }

    /**
     * @return string
     */
    public function GetBarcode() {
        return $this->barcode;
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
    public function GetRemark() {
        return $this->remarks;
    }

    /**
     * @param string $name
     * @param bool $update
     * @throws Exception
     * @throws \Exception
     */
    public function SetName(string $name, bool $update = true) {
        $this->name = $name;
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
            "Name" => $this->name,
            "Barcode" => $this->barcode,
            "Remark" => $this->remarks
        );
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->barcode)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new Exception("לא ניתן לעדכן את {0}", $updateArray, $this);

        $logText = "{product} עודכן!";
        BugOrderSystem::GetLog()->Write($logText, ELogLevel::INFO(), array("product" => $this, "ProductArray" => $updateArray));
    }

    /**
     * @return string
     */
    public function __toString() {
        return "המוצר '{$this->name}' ({$this->barcode})";
    }
}