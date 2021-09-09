<?php
namespace Easy\Coupons;

class Admin {

    /**
     * Class constructor.
     */
    public function __construct() {

        $easyCoupons = new Admin\Coupon();
        new Admin\Menu( $easyCoupons );
    }
}