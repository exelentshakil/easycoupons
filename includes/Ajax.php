<?php
namespace Easy\Coupons;

class Ajax {
    /**
     * Class constructor.
     */
    public function __construct() {
        add_action( 'wp_ajax_nopriv_update_product_meta', [$this, 'ajax_save_add_product_meta'] );
        add_action( 'wp_ajax_update_product_meta', [$this, 'ajax_save_add_product_meta'] );

        add_action( 'wp_ajax_nopriv_sign_up', [$this, 'sign_up'] );
        add_action( 'wp_ajax_sign_up', [$this, 'sign_up'] );

        add_action( 'wp_ajax_nopriv_sign_in', [$this, 'sign_in'] );
        add_action( 'wp_ajax_sign_in', [$this, 'sign_in'] );

        add_action( 'wp_ajax_nopriv_logout', [$this, 'ris_logout'] );
        add_action( 'wp_ajax_ris_logout', [$this, 'ris_logout'] );

        // delete coupon
        add_action( 'wp_ajax_delete_coupon', [$this, 'delete_coupon'] );
    }

    public function delete_coupon() {

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

    public function ris_logout() {

        wp_clear_auth_cookie();
        wp_logout();
        ob_clean(); // probably overkill for this, but good habit

        unset( $_COOKIE['ris_auction_auth_token'] );
        unset( $_COOKIE['ris_auction_auth_email'] );
        unset( $_COOKIE['ris_auction_auth_username'] );

        setcookie( 'ris_auction_auth_token', '', -1, '/' );
        setcookie( 'ris_auction_auth_email', '', -1, '/' );
        setcookie( 'ris_auction_auth_username', '', -1, '/' );

        wp_send_json_success();
    }

    public function sign_up() {
        $username = isset( $_POST['username'] ) ? $_POST['username'] : '';
        $email    = isset( $_POST['email'] ) ? $_POST['email'] : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : '';

        if ( ! empty( $username ) ) {

            $user_data = array(
                'user_login' => $username,
                'user_email' => $email,
                'user_pass'  => $password,
                'role'       => 'subscriber',
            );

            $user_id = wp_insert_user( $user_data );

            $api_response = wp_remote_post( 'https://wordpress-582935-2005777.cloudwaysapps.com/wp-json/wp/v2/users', array(
                //'method'    => 'PUT',
                'headers' => array(
                ),
                'body'    => array(
                    'username' => $username,
                    'email'    => $email,
                    'password' => $password,
                ),
            ) );

            $body                                  = json_decode( $api_response['body'] );
            $_SESSION['ris_auction_auth_username'] = $body->username;
            $_SESSION['ris_auction_auth_email']    = $body->email;

            setcookie( 'ris_auction_auth_username', $body->username, strtotime( '+1 day' ), '/' );
            setcookie( 'ris_auction_auth_email', $body->email, strtotime( '+1 day' ), '/' );

            wp_send_json_success( $body );
        }

    }

    public function sign_in() {
        $email    = isset( $_POST['email'] ) ? $_POST['email'] : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : '';

        if ( ! empty( $email ) ) {

            $client_error = '';
            $creds        = array(
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => true,
            );

            $user = wp_signon( $creds, false );

            if ( is_wp_error( $user ) ) {
                $client_error = $user->get_error_message();
            }

            $api_response = wp_remote_post( get_bloginfo( 'url' ) . '/wp-json/jwt-auth/v1/token', array(
                //'method'    => 'PUT',
                'headers' => array(
                ),
                'body'    => array(
                    'username' => $email,
                    'password' => $password,
                ),
            ) );

            $api_response = wp_remote_post( 'https://wordpress-582935-2005777.cloudwaysapps.com/wp-json/jwt-auth/v1/token', array(
                //'method'    => 'PUT',
                'headers' => array(
                    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd29yZHByZXNzLTU4MjkzNS0yMDA1Nzc3LmNsb3Vkd2F5c2FwcHMuY29tIiwiaWF0IjoxNjI0NzMwOTkyLCJuYmYiOjE2MjQ3MzA5OTIsImV4cCI6MTYyNTMzNTc5MiwiZGF0YSI6eyJ1c2VyIjp7ImlkIjoiMSJ9fX0.r1wVF7S91fI8uySd0VGs8hAYsTPwNfJ2TqzkvqF3GU0',
                ),
                'body'    => array(
                    'username' => $email,
                    'password' => $password,
                ),
            ) );

            $body                                  = json_decode( $api_response['body'] );
            $_SESSION['ris_auction_auth_username'] = $body->username;
            $_SESSION['ris_auction_auth_email']    = $body->email;

            setcookie( 'ris_auction_auth_token', $body->token, strtotime( '+1 day' ), '/' );
            setcookie( 'ris_auction_auth_username', $body->user_nicename, strtotime( '+1 day' ), '/' );
            setcookie( 'ris_auction_auth_email', $body->user_email, strtotime( '+1 day' ), '/' );

            wp_send_json_success( [$body, $client_error] );
        }

    }

    public function ajax_save_add_product_meta() {
        $token          = isset( $_POST['token'] ) && ! empty( $_POST['token'] ) ? $_POST['token'] : '';
        $product_id     = isset( $_POST['dokan_product_id'] ) ? absint( $_POST['dokan_product_id'] ) : 0;
        $post_title     = isset( $_POST['post_title'] ) ? trim( $_POST['post_title'] ) : '';
        $post_content   = isset( $_POST['post_content'] ) ? trim( $_POST['post_content'] ) : '';
        $post_excerpt   = isset( $_POST['post_excerpt'] ) ? trim( $_POST['post_excerpt'] ) : '';
        $featured_image = isset( $_POST['feat_image_id'] ) ? absint( $_POST['feat_image_id'] ) : '';

        if ( empty( $product_id ) ) {

            $post_data = array(
                'post_type'    => 'product',
                'post_status'  => 'published',
                'post_title'   => $post_title,
                'post_content' => $post_content,
                'post_excerpt' => $post_excerpt,
                'post_author'  => get_current_user_id(),
            );

            $product_id = wp_insert_post( $post_data );

            if ( $product_id ) {

                if ( $featured_image ) {
                    set_post_thumbnail( $product_id, $featured_image );
                }

                $attachment_ids = array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) );
                $images         = [];

                foreach ( $attachment_ids as $id ) {
                    $img      = wp_get_attachment_image_src( $id, 'full' );
                    $images[] = [
                        'src' => $img[0],
                    ];
                }

                $api_response = wp_remote_post( 'https://wordpress-582935-2005777.cloudwaysapps.com/wp-json/wc/v3/products', array(

                    //'method'    => 'PUT',

                    'headers' => array(

                        'Authorization' => 'Bearer ' . $token,

                    ),

                    'body'    => array(
                        'name'              => $post_data['post_title'],
                        'type'              => 'auction',
                        '_regular_price'    => '1000000.00',
                        'description'       => $post_data['post_content'],
                        'short_description' => $post_data['post_excerpt'],
                        'images'            => $images,
                    ),

                ) );

                $body = json_decode( $api_response['body'] );

                if ( wp_remote_retrieve_response_message( $api_response ) === 'OK' ) {

                    echo 'The product ' . $body->name . ' has been updated';
                }

                return wp_send_json_success( $body );
            }

        } else { // id is not empty lets update

            $address        = isset( $_POST['address'] ) ? $_POST['address'] : '';
            $home_details   = isset( $_POST['home_details'] ) ? $_POST['home_details'] : '';
            $contact        = isset( $_POST['contact'] ) ? $_POST['contact'] : '';
            $home_features  = isset( $_POST['home_features'] ) ? $_POST['home_features'] : '';
            $video          = isset( $_POST['video'] ) ? $_POST['video'] : '';
            $attachment_ids = array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) );
            $images         = [];

            foreach ( $attachment_ids as $id ) {
                $img      = wp_get_attachment_image_src( $id, 'full' );
                $images[] = [
                    'src' => $img[0],
                ];
            }

            $post_data = array(
                'ID'           => $product_id,
                'post_type'    => 'product',
                'type'         => 'auction',
                'post_status'  => 'published',
                'post_title'   => $post_title,
                'post_content' => $post_content,
                'post_excerpt' => $post_excerpt,
                'post_author'  => get_current_user_id(),
            );

            $property_data = [
                'address'       => $address,
                'home_details'  => $home_details,
                'contact'       => $contact,
                'home_features' => $home_features,
                'video'         => $video,
            ];

            $api_response = wp_remote_post( 'https://wordpress-582935-2005777.cloudwaysapps.com/wp-json/wc/v3/products/' . $product_id, array(

                //'method'  => 'PUT',

                'headers' => array(
                    'Authorization' => 'Bearer ' . $token,
                ),

                'body'    => array(

                    'name'              => $post_data['post_title'],
                    'type'              => 'auction',
                    '_regular_price'    => '1000000.00',
                    'description'       => $post_data['post_content'],
                    'short_description' => $post_data['post_excerpt'],
                    'images'            => $images,
                    'meta_data'         => [
                        [
                            'key'   => 'property_data',
                            'value' => $property_data,
                        ],

                    ],

                ),

            ) );

            $body = json_decode( $api_response['body'] );

            if ( wp_remote_retrieve_response_message( $api_response ) === 'OK' ) {

                echo 'The product ' . $body->name . ' has been updated';
            }

            return wp_send_json_success( $body );

        }

        exit;

    }

}