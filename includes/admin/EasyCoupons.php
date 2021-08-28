<?php
namespace Easy\Coupons\Admin;

class EasyCoupons {

    public function init( $page ) {

        $page = isset( $_GET['action'] ) ? $_GET['action'] : '';

        switch ( $page ) {
        case 'settings':
            $template = __DIR__ . '/views/settings.php';
            break;
        default:
            $template = __DIR__ . '/views/dashboard.php';
            break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }

    }

    public function menu_page() {
        wp_enqueue_script( 'main' );
        include __DIR__ . '/views/dashboard.php';
    }

}