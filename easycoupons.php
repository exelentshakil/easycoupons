<?php
/**
 * Plugin Name:       Easy Coupons
 * Plugin URI:        https://www.upwork.com/freelancers/~01e19084859cda495e
 * Description:       A WordPress plugin to lock video with coupon code.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shakil Ahmed
 * Author URI:        https://www.upwork.com/freelancers/~01e19084859cda495e
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easycoupons
 * Domain Path:       /languages
 */

// Don't call this file directly

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EasyCoupons {

    /**
     * Plugin version
     *
     * @var string
     */

    public $version = '1.0';

    /**
     * Class constructor.
     */
    public function __construct() {

        session_start();

        require_once __DIR__ . '/vendor/autoload.php';

        $this->define_constants();

        register_activation_hook( __FILE__, [$this, 'active'] );
        register_deactivation_hook( __FILE__, [$this, 'deactive'] );
        add_action( 'plugins_loaded', [$this, 'plugins_loaded'] );
    }

    public function plugins_loaded() {

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new Easy\Coupons\Ajax();
        }

        if ( is_admin() ) {
            new Easy\Coupons\Admin();
        } else {
            new Easy\Coupons\Frontend();
        }

        new Easy\Coupons\Assets();

    }

    public function define_constants() {
        define( 'EASY_COUPONS_VERSION', $this->version );
        define( 'EASY_COUPONS_FILE', __FILE__ );
        define( 'EASY_COUPONS_PATH', dirname( EASY_COUPONS_FILE ) );
        define( 'EASY_COUPONS_URL', plugins_url( '', EASY_COUPONS_FILE ) );
        define( 'EASY_COUPONS_ASSETS', EASY_COUPONS_URL . '/assets' );
    }

    public function active() {

        $installer = new Easy\Coupons\Installer();
        $installer->run();
    }

    public function deactive() {}

    /**
     * Initialize singleton
     *
     * @return \EasyCoupons
     */
    public static function init() {

        static $instance = false;

        if ( ! $instance ) {
            $instance = new EasyCoupons();
        }

        return $instance;
    }

}

function easycoupons() {
    return EasyCoupons::init();
}

// hook plugin with world
easycoupons();