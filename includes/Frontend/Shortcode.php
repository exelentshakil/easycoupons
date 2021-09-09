<?php
namespace Easy\Coupons\Frontend;

use Easy\Coupons\Traits\Features;

class Shortcode {
    use Features;

    /**
     * Class constructor.
     */
    public function __construct() {
        add_shortcode( 'easy-coupon', [$this, 'render_shortcode'] );
    }

    public function render_shortcode( $atts, $content = '' ) {

        $atts = shortcode_atts( [
            'id' => '',
        ], $atts, 'easy-coupon' );

        $vid_id    = $atts['id'];
        $vid_title = get_the_title($vid_id);
        $vid_url   = get_post_meta($vid_id, 'video', true);
        $vid_poster= get_the_post_thumbnail_url($vid_id, 'large');

        //Check if already unlocked
        $unlocked_list = urldecode($_COOKIE['unlocked_vids']);
        $unlocked_list = stripslashes($unlocked_list);
        $unlocked_list = json_decode($unlocked_list,true);

        //Render html data
        $output = "<div id='easy-coupon-{$vid_id}' class='easy-coupon'>";
        $output .= "<h2 class='easy-coupon-title'>{$vid_title}</h2>";
        $output .= "<div class='vidcontainer'>";
        if(!null == $unlocked_list && in_array($vid_id,$unlocked_list)){
            $output .= "<iframe class='responsive-iframe' src='{$vid_url}'></iframe>";
        }else{
            $output .= "<img class='responsive-iframe' src='{$vid_poster}'/>";
            $output .= "<span data-easy-coupon data-easy-coupon-id='{$vid_id}' class='unlock'>Unlock Video</span>";
        }
        $output .= "</div>";
        $output .= "</div>";

        return $output;
    }

}