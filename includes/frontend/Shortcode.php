<?php
namespace Easy\Coupons\Frontend;

use Easy\Coupons\Traits\Features;

class Shortcode {
    use Features;

    /**
     * Class constructor.
     */
    public function __construct() {
        add_shortcode( 'easy-coupons', [$this, 'render_shortcode'] );
    }

    public function render_shortcode( $atts, $content = '' ) {

        $states = array_keys( $this->all_states );

        ob_start();
        include __DIR__ . '/views/add_new_form.php';

        return ob_get_clean();
    }

}