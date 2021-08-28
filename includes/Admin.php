<?php
namespace Easy\Coupons;

class Admin {

    /**
     * Class constructor.
     */
    public function __construct() {

        $easyCoupons = new Admin\EasyCoupons();
        new Admin\Menu( $easyCoupons );
    }
}