<?php

function ec_coupons($args = []) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons';
    $defaults = [
        'offset'  => 0,
        'orderby' => 'id',
        'order'   => 'ASC'
    ];

    $args = wp_parse_args($args, $defaults);

    $sql = $wpdb->prepare(
        "SELECT * FROM {$table} ORDER BY {$args['orderby']} {$args['order']}"
    );

    $coupons = $wpdb->get_results($sql);

    return $coupons;
}

function ec_delete_coupon($id) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons';

    return $wpdb->delete(
      $table,
      ['id' => $id],
      ['%d']
    );
}


function unlock_video(){}

function check_coupon($coupon){}

function coupon_used($code, $is_expired = false ){}

function log_coupon($status, $coupon, $vid_id){}


function generate_code(){
    $bytes = random_bytes(2);
    return bin2hex($bytes);
}