<?php
namespace Easy\Coupons;

class Installer {

    public function run() {
        $this->update_version();
        $this->create_tables();
        $this->seed_tables();
    }

    public function update_version() {

        $installed = get_option( 'easycoupons_installed' );

        if ( $installed ) {
            update_option( 'easycoupons_installed', time() );
        }

        update_option( 'easycoupon_version', EASY_COUPONS_VERSION );
    }

    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Create coupons table
        $table_name     = $wpdb->prefix . 'easycoupons';
        $table_name_log = $wpdb->prefix . 'easycoupons_logs';

        $sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					coupon varchar(55) NOT NULL,
					expiry_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					is_used smallint(2) NOT NULL,
					created_by int NOT NULL,
					created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					PRIMARY KEY (id),
					UNIQUE (coupon)
					) $charset_collate;";

        $sql .= "CREATE TABLE $table_name_log (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					coupon varchar(55) NOT NULL,
					status smallint(2) NOT NULL,
					video_id mediumint(2) NOT NULL,
					video_title text DEFAULT NULL,
					created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					PRIMARY KEY (id)
					) $charset_collate;";

        if ( ! function_exists('dbDelta') ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $sql );
    }

    // add some dummy data to test
    public function seed_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'easycoupons'; // do not forget about tables prefix
        $wpdb->insert( $table_name, [
            'coupon'  => 'coupon1',
            'expiry_date' => date('Y-m-d H:i:s'),
            'is_used'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ] );

        $wpdb->insert( $table_name, [
            'coupon'  => 'coupon2',
            'expiry_date' => date('Y-m-d H:i:s'),
            'is_used'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ] );

        $wpdb->insert( $table_name, [
            'coupon'  => 'coupon3',
            'expiry_date' => date('Y-m-d H:i:s'),
            'is_used'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ] );

        $wpdb->insert( $table_name, [
            'coupon'  => 'coupon4',
            'expiry_date' => date('Y-m-d H:i:s'),
            'is_used'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ] );
    }

}