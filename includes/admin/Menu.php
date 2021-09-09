<?php
namespace Easy\Coupons\Admin;

class Menu {

    /**
     * Auction settings
     *
     * @var \EasyCoupons
     */
    private $easyCoupons;

    /**
     * Class constructor.
     */
    public function __construct(Coupon $easyCoupons ) {

        $this->easyCoupons = $easyCoupons;
        add_action( 'admin_menu', [$this, 'admin_menu'] );
    }

    public function admin_menu() {

        $capabilities = 'manage_options';
        $slug         = 'easy-coupons';
        $icon         = 'dashicons-embed-video';

        $hook = add_menu_page( __( 'Easy Coupons', 'easycoupons' ), __( 'Easy Coupons', 'easycoupons' ), $capabilities, $slug, [$this->easyCoupons, 'menu_page'], $icon );
        add_submenu_page( $slug, __( 'Easy Coupons', 'easycoupons' ), __( 'Easy Coupons', 'easycoupons' ), $capabilities, $slug, [$this->easyCoupons, 'menu_page'], $icon );
        add_submenu_page( $slug, __( 'All Coupons', 'easycoupons' ), __( 'All Coupons', 'easycoupons' ), $capabilities, 'coupons', [$this->easyCoupons, 'coupons'] );
        add_submenu_page( $slug, __( 'Coupons Log', 'easycoupons' ), __( 'Coupons Log', 'easycoupons' ), $capabilities, 'coupons-log', [$this->easyCoupons, 'coupons_log'] );
        add_submenu_page( $slug, __( 'Generate Coupons', 'easycoupons' ), __( 'Generate Coupons', 'easycoupons' ), $capabilities, 'generate-coupons', [$this->easyCoupons, 'generate_coupons'] );

        add_action( 'load-' . $hook, [$this, 'menu_script'] );
    }

    public function menu_script() {
        add_action( 'admin_enqueue_scripts', [$this, 'menu_enqueue_scripts'] );
    }

    public function menu_enqueue_scripts() {
        wp_enqueue_style( 'main' );
        wp_enqueue_script( 'main' );
        wp_enqueue_style( 'datatable' );
        wp_enqueue_script( 'datatable' );
        wp_enqueue_script( 'swal' );
    }

}