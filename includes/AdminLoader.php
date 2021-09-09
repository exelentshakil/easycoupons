<?php
namespace Easy\Coupons;

class AdminLoader {

    /**
     * Class constructor.
     */
    public function __construct() {

        $easyCoupons = new Admin\Coupon();
        new Admin\Menu( $easyCoupons );
    }
}