<?php
/**
 * Created by PhpStorm.
 * User: YogevAgranov
 * Date: 11/09/2017
 * Time: 15:15
 */

namespace BugOrderSystem;

use Log\ELogLevel;

class OrderProducts {
    private $orderId;
    /**
     * @var Products
     */
    private $product;
    private $remarks;
    /**
     * @var EProductStatus
     */
    private $status;
    private $quantity;

    /**
     * OrderProducts constructor.
     * @param int $orderId
     * @param Products $product
     * @param int $quantity
     * @param EProductStatus $status
     * @param string|null $remarks
     * @throws Exception
     * @throws \Exception
     */
    public function __construct(int $orderId, Products $product, int $quantity, EProductStatus $status, string $remarks = null) {
        $this->orderId = $orderId;
        $this->product = $product;
        $this->SetQuantity($quantity, false);
        $this->status = $status;
        $this->remarks = $remarks;
    }

    /**
     * @return string
     */
    public function GetProductName() {
        return $this->product->GetName();
    }

    public function GetProductRemark() {
        return $this->product->GetRemark();
    }

    /**
     * @return string
     */
    public function GetProductBarcode() {
        return $this->product->GetBarcode();
    }

    /**
     * @return string
     */
    public function GetRemarks() {
        return $this->remarks;
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
     * @param EProductStatus $newStatus
     */
    public function ChangeStatus(EProductStatus $newStatus) {
        //TODO: need to rewrite!
    }

    /**
     * @param $remarks
     * @param bool $update
     * @throws Exception
     * @throws \Exception
     */
    public function SetRemarks($remarks, bool $update = true) {
        $this->remarks = $remarks;
        if ($update)
            $this->Update();
    }

    /**
     * @param int $quantity
     * @param bool $update
     * @throws Exception
     * @throws \Exception
     */
    public function SetQuantity(int $quantity, bool $update = true) {
        if($quantity < 1 || $quantity > Constant::PRODUCT_MAX_QUANTITY)
            throw new Exception("\"כמות לא חוקית של פריטים! ניתן לשנות את הכמות בין 1 ל-{0}!", $quantity, Constant::PRODUCT_MAX_QUANTITY);

        $this->quantity = $quantity;
        if ($update)
            $this->Update();
    }

    /**
     *
     */
    public function Update() {
        //TODO: need to rewrite!
    }

    /**
     * @return string
     */
    public function __toString() {
        return "המוצר {$this->product->GetName()} ({$this->product->GetBarcode()}) בהזמנה {$this->orderId}";
    }
}