<?php
namespace Easy\Coupons\Api;

class EasyCouponsApi {

    /** @var string $base the route base */
    protected $base = '/easy-coupons';

    /**
     * Register the routes for this class
     *
     * GET/POST /coupons
     * GET /coupons/count
     * GET/PUT/DELETE /coupons/<id>
     *
     * @since 2.1
     * @param array $routes
     * @return array
     */
    public function register_routes() {
        # GET/POST /products
        $routes[$this->base] = array(
            array( array( $this, 'get_coupons' ), WC_API_Server::READABLE ),
            array( array( $this, 'create_coupons' ), WC_API_SERVER::CREATABLE | WC_API_Server::ACCEPT_DATA ),
            array( array( $this, 'update_coupons' ), WC_API_SERVER::EDITABLE | WC_API_Server::ACCEPT_DATA ),
        );
    }

    // TODO
    public function get_coupons() {}
    public function create_coupons() {}
    public function update_coupons() {}


}