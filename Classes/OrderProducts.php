<?php
/**
 * Created by PhpStorm.
 * User: YogevAgranov
 * Date: 11/09/2017
 * Time: 15:15
 */

namespace BugOrderSystem;

class OrderProducts {

    const TABLE_KEY_COLUMN = "ProductId";
    const TABLE_NAME = "orderproducts";
    const MAX_QUANTITY = 100;

    private static $orderProduct = array();

    private $id;
    private $productName;
    private $productBarcode;
    private $remarks;
    private $timestamp;
    private $quantity;
    private $status;

    /**
     * OrderProducts constructor.
     * @param int $OrderId
     * @param string $ProductName
     * @param string $ProductBarcode
     * @param string|null $remarks
     * @param int $Quantity
     * @throws Exception
     * @throws \Exception
     */
    public function __construct(int $OrderId, string $ProductName, string $ProductBarcode, string $remarks = null, $Quantity = 1) {
        $res = BugOrderSystem::GetDB()->where("OrderId", $OrderId)->where("ProductBarcode", $ProductBarcode)->getOne(self::TABLE_NAME, self::TABLE_KEY_COLUMN);
        if (BugOrderSystem::GetDB()->count == 0) {
            $orderProductArray = array(
                "OrderId" => $OrderId,
                "ProductName" => $ProductName,
                "ProductBarcode" => $ProductBarcode,
                "Remarks" => $remarks,
                "Quantity" => $Quantity,
                "Status" => EProductStatus::Created[0]
            );

            $success = BugOrderSystem::GetDB()->insert(self::TABLE_NAME, $orderProductArray);
            $productInfoId = BugOrderSystem::GetDB()->getInsertId();

            if (!$success)
                throw new Exception("לא ניתן לייצר הזמנה כרגע.", $orderProductArray);
        }
        else {
            $productInfoId = $res[self::TABLE_KEY_COLUMN];
        }

        $OrderInfoData = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $productInfoId)->getOne(self::TABLE_NAME);
        $this->id = $productInfoId;
        $this->productName = $OrderInfoData["ProductName"];
        $this->productBarcode = $OrderInfoData["ProductBarcode"];
        $this->remarks = $OrderInfoData["Remarks"];
        $this->timestamp = new \DateTime($OrderInfoData["Timestamp"]);
        $this->quantity = $OrderInfoData["Quantity"];
        $this->status = EProductStatus::search($OrderInfoData["Status"]);
    }

    /**
     * @return int
     */
    public function GetId() {
        return $this->id;
    }

    /**
     * @return int
     */
    public function GetQuantity() {
        return $this->quantity;
    }

    /**
     * @return EProductStatus
     */
    public function GetStatus() {
        return $this->status;
    }

    /**
     * @param $status
     * @return EProductStatus
     * @throws \Exception
     * Todo: change method input type to EProductStatus Enum
     */
    public function ChangeStatus($status) {
        $info = array("Status" => $status);
        BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $info);
        return $this->status;
    }


    /**
     * @param int $Quantity
     * @throws Exception
     */
    public function ChangeQuantity(int $Quantity) {
        if ($Quantity < 1 || $Quantity > self::MAX_QUANTITY)
            throw new Exception("כמות לא חוקית של פריטים! ניתן לשנות את הכמות בין 1 ל-{0}!", $Quantity, self::MAX_QUANTITY);

        $this->quantity = $Quantity;
        $this->Update();
    }

    /**
     * @param string $Remarks
     */
    public function ChangeRemarks(string $Remarks) {
        $this->remarks = $Remarks;
        $this->Update();
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->id.": ".$this->productName." (".$this->productBarcode.")";
    }

    /**
     * @return string
     */
    public function getProductName() {
        return $this->productName;
    }

    /**
     * @return string
     */
    public function GetProductBarcode() {
        return $this->productBarcode;
    }

    /**
     * @return string
     */
    public function GetRemarks() {
        return $this->remarks;
    }

    /**
     * @return \DateTime
     */
    public function GetTimestamp() {
        return $this->timestamp;
    }

    /**
     * @throws Exception
     */
    private function OldUpdate() {
        $updateArray = array(
            "ProductName" => $this->productName,
            "ProductBarcode" => $this->productBarcode,
            "Quantity" => $this->quantity,
            "Remarks" => $this->remarks,
            "Status" => $this->status
        );
        $success = BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $updateArray, 1);
        if (!$success)
            throw new Exception("לא ניתן לעדכן את {0}", $updateArray, $this);
    }

    public function ProductUpdate(array $data){
        BugOrderSystem::GetDB()->where(self::TABLE_KEY_COLUMN, $this->id)->update(self::TABLE_NAME, $data);
    }

}