<?php
namespace Easy\Coupons;

class Assets {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );

        add_action( 'wp_enqueue_scripts', [$this, 'common_scripts'] );
        add_action( 'admin_enqueue_scripts', [$this, 'common_scripts'] );

    }

    public function common_scripts() {
        // Default
        wp_enqueue_script( 'wp-util' );
        wp_enqueue_script( 'jquery-ui-mouse' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_script( 'jquery-ui-autocomplete' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-datetimepicker-min' );
        wp_enqueue_script( 'year-select' );
        wp_enqueue_script( 'swal' );


        // custom scripts
        wp_enqueue_style( 'main' );
        wp_enqueue_script( 'main' );

        wp_localize_script( 'main', 'EasyCoupons', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'easy_coupons_nonce' ),
        ] );
    }

    public function enqueue_scripts() {
        $styles  = $this->get_styles();
        $scripts = $this->get_scripts();

        $this->register_styles( $styles );
        $this->register_scripts( $scripts );
    }

    public function register_styles( $styles ) {

        foreach ( $styles as $handle => $style ) {
            $deps    = isset( $style['deps'] ) ? $style['deps'] : [];
            $version = isset( $style['version'] ) ? $style['version'] : '1.0';
            wp_register_style( $handle, $style['src'], $deps, $version );
        }

    }

    public function register_scripts( $scripts ) {

        foreach ( $scripts as $handle => $script ) {
            $deps    = isset( $script['deps'] ) ? $script['deps'] : [];
            $version = isset( $script['version'] ) ? $script['version'] : '1.0';

            wp_register_script( $handle, $script['src'], $deps, $version, true );
        }

    }

    public function get_styles() {

        return [
            'main'         => [
                'src'     => EASY_COUPONS_ASSETS . '/css/main.css',
                'version' => filemtime( EASY_COUPONS_PATH . '/assets/css/main.css' ),
                'deps'    => [],
            ],
        ];

    }

    public function get_scripts() {

        return [
            'main'  => [
                'src'     => EASY_COUPONS_ASSETS . '/js/main.js',
                'version' => filemtime( EASY_COUPONS_PATH . '/assets/js/main.js' ),
                'deps'    => ['jquery'],
            ],
        ];

    }

}