<?php

function ec_coupons($args = []) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons';
    $defaults = [
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


function ec_coupons_logs($args = []) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons_logs';
    $defaults = [
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
function ec_delete_by($date) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons';

    $sql = $wpdb->prepare("DELETE FROM $table WHERE DATE(expiry_date)='$date'");
    return $wpdb->query( $sql );
}

// delete coupons log
function ec_delete_coupon_log($id) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons_log';

    return $wpdb->delete(
        $table,
        ['id' => $id],
        ['%d']
    );
}
function ec_delete_log_by($date) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons_logs';

    $sql = $wpdb->prepare("DELETE FROM $table WHERE DATE(created_at)='$date'");
    return $wpdb->query( $sql );
}