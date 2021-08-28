<?php
namespace Easy\Coupons\Admin;

class Menu {

    /**
     * Auction settings
     *
     * @var \EasyCoupons
     */
    private $auctionSettings;

    /**
     * Class constructor.
     */
    public function __construct(EasyCoupons $auctionSettings ) {

        $this->auctionSettings = $auctionSettings;
        add_action( 'admin_menu', [$this, 'admin_menu'] );
    }

    public function admin_menu() {

        $capabilities = 'manage_options';
        $slug         = 'easy-coupons';
        $icon         = 'dashicons-admin-multisite';

        $hook = add_menu_page( __( 'Easy Coupons', 'easycoupons' ), __( 'Easy Coupons', 'easycoupons' ), $capabilities, $slug, [$this->auctionSettings, 'menu_page'], $icon );
        add_action( 'load-' . $hook, [$this, 'menu_script'] );
    }

    public function menu_script() {
        add_action( 'admin_enqueue_scripts', [$this, 'menu_enqueue_scripts'] );
    }

    public function menu_enqueue_scripts() {
        wp_enqueue_style( 'main' );
    }

}