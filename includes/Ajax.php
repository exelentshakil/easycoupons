<?php
namespace Easy\Coupons;

class Ajax {
    /**
     * Class constructor.
     */
    public function __construct() {

        // delete coupon
        add_action( 'wp_ajax_delete_coupon', [$this, 'delete_coupon'] );
        add_action( 'wp_ajax_delete_coupon_by_expiry_date', [$this, 'delete_coupon_by_expiry_date'] );

        // easy videos
        add_action( 'wp_ajax_unlock_a_vid', [$this,'easyvid_unlock_func'] );
        add_action( 'wp_ajax_nopriv_unlock_a_vid', [$this,'easyvid_unlock_func'] );
    }

    public function delete_coupon() {
        if ( wp_verify_nonce($_REQUEST['']))
        if( ! current_user_can( 'administrator' ) ) {
            wp_die('You don\'t have permission' );
        }

        $id = isset( $_REQUEST['id'] ) ? intval($_REQUEST['id']) : 0;

        if ( ec_delete_coupon($id)) {
            wp_send_json_success();
        }
        wp_send_json_error();

        exit;

    }
    public function delete_coupon_by_expiry_date() {

        if( ! current_user_can( 'administrator' ) ) {
            wp_die('You don\'t have permission' );
        }
        $date = isset( $_REQUEST['expire_date'] ) ? sanitize_text_field($_REQUEST['expire_date']) : 0;

        $delete =  ec_delete_by($date);

        if ( $delete ) {
            wp_send_json_success();
        }
        wp_send_json_error();

        exit;

    }

    function easyvid_unlock_func(){
        $vid_id = $_REQUEST['vid_id'];
        $coupon = $_REQUEST['coupon'];

        $status = $this->check_coupon($coupon);

        if(1 === $status){
            $this->set_unlocked($vid_id);

            echo get_post_meta($vid_id, 'video', true);
        }elseif(2 === $status){
            echo "code_used";
        }else{
            echo "code_invalid";
        }

        $this->log_entry($status, $coupon, $vid_id);
        die();
    }

    function set_unlocked($vid_id){
        if(isset($_COOKIE['unlocked_vids'])) {
            $prev_value = urldecode($_COOKIE['unlocked_vids']);
            $prev_value = stripslashes($prev_value);
            $prev_value = json_decode($prev_value,true);
            if(!in_array($vid_id, $prev_value)){
                array_push($prev_value, $vid_id);
            }
            $new_value = json_encode($prev_value);
            setcookie('unlocked_vids', $new_value, time() + (86400 * 30), "/");
        }else{
            $init_value = array($vid_id);
            $init_value = json_encode($init_value);
            setcookie('unlocked_vids', $init_value, time() + (86400 * 30), "/");
        }
    }

    function check_coupon($coupon){
        global $wpdb;
        $table = $wpdb->prefix . 'easycoupons';

        if(($coupon == "ADMN" ) && is_user_logged_in() && current_user_can( 'administrator' )){
            return 1;
        }

        $log_sts = 0;

        $has_coupon = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE coupon = '$coupon'"));

        if($has_coupon){
            $status = $has_coupon->is_used;
            $expiry_date = $has_coupon->expiry_date;
            $now = date("Y-m-d H:i:s");

            if($now >= $expiry_date){
                $log_sts = 4;
                $this->coupon_use($coupon, true);
            }elseif(0 === absint($status)){
                $log_sts = 1;
                $this->coupon_use($coupon);
            }else{
                $log_sts = 2;
            }
        }else{
            $log_sts = 3;
        }

        return $log_sts;
    }

    function coupon_use($code, $is_expired = false ){
        global $wpdb;
        $table = $wpdb->prefix . 'easycoupons';

        if($code != "ADMN"){
            $status = 1;
            if($is_expired){
                $status = 2;
            }
            $wpdb->update( $table, array( 'is_used' => $status ),array('coupon'=>$code));
        }
    }

    function log_entry($status, $coupon, $vid_id){
        global $wpdb;
        $table_name = $wpdb->prefix . 'easycoupons_logs';

        $vid_title = get_the_title($vid_id);

        $item  = array(
            'coupon' => $coupon,
            'status' => $status,
            'video_id' => $vid_id,
            'video_title' => $vid_title,
            'created_at' => date('Y-m-d H:i:s'),
        );

        $format = array('%s','%d','%d','%s','%s');


        $wpdb->insert($table_name, $item, $format);

    }

}