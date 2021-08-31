<?php

use \PHPUnit\Framework\TestCase;

final class EasyCouponsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        global $wpdb;


        $table_name = $wpdb->prefix . 'easycoupons'; // do not forget about tables prefix
        $log_table = $wpdb->prefix . 'easycoupons_logs'; // do not forget about tables prefix

       $wpdb->insert( $table_name, [
            'coupon'  => bin2hex(random_bytes(2)),
            'expiry_date' => date('Y-m-d H:i:s'),
            'is_used'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ] );

        $wpdb->insert( $table_name, [
            'coupon'  => bin2hex(random_bytes(2)),
            'expiry_date' => date('Y-m-d H:i:s'),
            'is_used'   => 0,
            'created_at' => date('Y-m-d H:i:s')
        ] );
        $data = $wpdb->get_results("SELECT * FROM {$table_name}");

        $wpdb->insert($log_table, [
            'coupon' => $data[0]->coupon,
            'video_id' => 1,
            'video_title' => 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')

        ]);

        $wpdb->insert($log_table, [
            'coupon' => $data[1]->coupon,
            'video_id' => 2,
            'video_title' => 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')

        ]);

    }
    public function test_db_connection() {
        global $wpdb;

        $table = $wpdb->prefix . 'easycoupons';

        $response = $wpdb->get_results("SELECT * FROM {$table}");

        $this->assertTrue(count($response) > 0);

    }

    public function test_ec_coupons_fetching_all_coupons_porperly() {
        global $wpdb;

        $table = $wpdb->prefix . 'easycoupons';

        $args = [
            'orderby' => 'id',
            'order'   => 'ASC'
        ];

        $response = $wpdb->get_results("SELECT * FROM {$table} ORDER BY {$args['orderby']} {$args['order']}");

        $this->assertTrue( count($response) > 0);
    }

    public function test_ec_delete_coupon_by_id_working_fine() {
        global $wpdb;

        $table = $wpdb->prefix . 'easycoupons';
        $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}easycoupons");

        $response = $wpdb->delete(
            $table,
            ['id' => $data[0]->id],
            ['%d']
        );

        $this->assertTrue($response === 1);

    }


    public function test_ec_delete_by_date_working_fine() {
        global $wpdb;

        $table = $wpdb->prefix . 'easycoupons';

        $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}easycoupons");
        $date = date('Y-m-d', strtotime( $data[0]->expiry_date));

        $response = $wpdb->query( "DELETE FROM $table WHERE DATE(expiry_date)='$date'" );

        $this->assertTrue( $response > 0);
    }



    public function test_ec_coupons_logs_showing_all_logs_data() {
        global $wpdb;

        $table = $wpdb->prefix . 'easycoupons_logs';

        $args = [
            'orderby' => 'id',
            'order'   => 'ASC'
        ];

        $response = $wpdb->get_results("SELECT * FROM {$table} ORDER BY {$args['orderby']} {$args['order']}");

        $this->assertTrue( count($response) > 0);
    }

    public function test_ec_delete_coupon_log_by_id_working_fine() {
        global $wpdb;

        $table = $wpdb->prefix . 'easycoupons_logs';

        $data = $wpdb->get_results("SELECT * FROM {$table}");

        $response = $wpdb->delete(
            $table,
            ['id' => $data[0]->id],
            ['%d']
        );

        $this->assertTrue($response === 1);
    }

    public function test_ec_delete_log_by_date_working_fine() {
        global $wpdb;

        $table = $wpdb->prefix . 'easycoupons_logs';

        $data = $wpdb->get_results("SELECT * FROM {$table}");
        $date = date('Y-m-d', strtotime($data[0]->created_at));

        $response = $wpdb->query( "DELETE FROM $table WHERE DATE(created_at)='$date'");

        $this->assertTrue( $response > 0);
    }
}