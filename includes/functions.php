<?php

/**
 * Fetch all coupons from easycoupons table
 * @param array $args
 * @return array|object|null
 */
function ec_coupons($args = []) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons';
    $defaults = [
        'orderby' => 'id',
        'order'   => 'ASC'
    ];

    $args = wp_parse_args($args, $defaults);

    $sql = "SELECT * FROM {$table} ORDER BY {$args['orderby']} {$args['order']}";

    $coupons = $wpdb->get_results($sql);

    return $coupons;
}


/**
 * Delete a single coupon by id
 *
 * @param $id
 * @return bool|int
 */
function ec_delete_coupon($id) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons';

    return $wpdb->delete(
        $table,
        ['id' => $id],
        ['%d']
    );
}

/**
 * Delete all coupon by expire date
 *
 * @param $date
 * @return bool|int
 */
function ec_delete_by($date) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons';

    $sql = "DELETE FROM $table WHERE DATE(expiry_date)='$date'";
    return $wpdb->query( $sql );
}


/**
 * Fetch all coupons log from easycoupons_logs table
 *
 * @param array $args
 * @return array|object|null
 */
function ec_coupons_logs($args = []) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons_logs';
    $defaults = [
        'orderby' => 'id',
        'order'   => 'ASC'
    ];

    $args = wp_parse_args($args, $defaults);

    $sql = "SELECT * FROM {$table} ORDER BY {$args['orderby']} {$args['order']}";

    $coupons = $wpdb->get_results($sql);

    return $coupons;
}

/**
 * Delete coupon log from easycoupons_logs table by id
 *
 * @param $id
 * @return bool|int
 */
function ec_delete_coupon_log($id) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons_log';

    return $wpdb->delete(
        $table,
        ['id' => $id],
        ['%d']
    );
}

/**
 * Delete all coupons log by Date
 *
 * @param $date
 * @return bool|int
 */
function ec_delete_log_by($date) {
    global $wpdb;

    $table = $wpdb->prefix . 'easycoupons_logs';

    $sql = "DELETE FROM $table WHERE DATE(created_at)='$date'";
    return $wpdb->query( $sql );
}