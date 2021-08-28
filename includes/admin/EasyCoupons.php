<?php
namespace Easy\Coupons\Admin;

class EasyCoupons {

    private $database_table;

    /**
     * @var array
     */
    private $screens = ['easy-video'];

    /**
     * @var array
     */
    private $fields = [
        [
            'label'   => 'Video Url',
            'id'      => 'video',
            'type'    => 'url',
            'default' => 'https://www.youtube.com/embed/<video-id>',
        ],
    ];

    public function __construct()
    {
        global $wpdb;
        $this->database_table = $wpdb->prefix . 'easycoupons';

        // add custom Easy Video post type
        add_action('init', [$this, 'create_easyvideo_cpt']);

        // add custom meta box
        add_action( 'add_meta_boxes', [$this, 'add_meta_boxes'] );
        add_action( 'save_post', [$this, 'save_fields'] );

        add_action( 'submitpost_box', [$this, 'callback__submitpost_box'] );
    }

    public function coupons() {
        include __DIR__ . '/views/coupons.php';
    }

    public function generate_coupons() {
        global $wpdb;
        $table_name = $this->database_table; // do not forget about tables prefix

        $message = '';
        $notice = '';

        // this is default $item which will be used for new records
        $default = array(
            // 'id' => 0,
            'coupon' => '',
            'expiry_date' => '',
            'is_used' => 0,
            'created_by' => get_current_user_id(),
            'created_at' => date('Y-m-d H:i:s'),
        );

        // here we are verifying does this request is post back and have correct nonce
        if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {

            $generated = 0;
            $target = $_REQUEST['code_count'];
            $expire = $_REQUEST['expire_date'];

            while ($generated < $target) {
                $item = $default;

                $item['coupon'] = $this->generate_code();
                $date = new \DateTime($expire);
                $item['expiry_date'] = date('Y-m-d H:i:s', $date->getTimestamp());

                $result = $wpdb->insert($table_name, $item);
                if ($result) {
                    $generated++;
                }

                if ($generated == $target) {
                    $message = $target.' coupon code generated!';
                }
            }
        } else {
            // if this is not post back we load item to edit or give new one to create
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'easycoupons');
                }
            }
        }

        // here we adding our custom meta box
        add_meta_box('coupons_form_meta_box', 'Bulk Coupon Code Generator', [$this, 'easy_coupons_form_meta_box_handler'], 'new-coupon', 'normal', 'default');

        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php _e('Easy Coupons', 'easycoupons')?>
                <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=coupons');?>"><?php _e('Back to list', 'easycoupons')?></a>
            </h2>

            <?php if (!empty($notice)): ?>
                <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
                <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php endif;?>

            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
                <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <?php /* And here we call our custom meta box */ ?>
                            <?php do_meta_boxes('new-coupon', 'normal', $item); ?>
                            <input type="submit" value="<?php _e('Save', 'easycoupons')?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * This function renders our custom meta box
     * $item is row
     *
     * @param $item
     */
    function easy_coupons_form_meta_box_handler($item)
    {
        ?>

        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
            <tbody>
            <tr class="form-">
                <th valign="top" scope="row">
                    <label for="code_count"><?php _e('Number of Coupon', 'easycoupons')?></label>
                </th>
                <td>
                    <input id="code_count" name="code_count" type="number" min="1" max="100" value="1"
                           class="small-text" required>
                </td>
            </tr>
            <tr class="form-">
                <th valign="top" scope="row">
                    <label for="expire_date"><?php _e('Expiry Date', 'easycoupons')?></label>
                </th>
                <td>
                    <input id="expire_date" name="expire_date" type="date" class="regular-text" min="<?php echo date('Y-m-d'); ?>" required>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * This function generates coupon codes.
     *
     */
    private function generate_code(){
        $bytes = random_bytes(2);
        // var_dump(bin2hex($bytes));
        return bin2hex($bytes);
    }

    public function init( $page ) {

        $page = isset( $_GET['action'] ) ? $_GET['action'] : '';

        switch ( $page ) {
        case 'settings':
            $template = __DIR__ . '/views/generate-coupons.php';
            break;
        default:
            $template = __DIR__ . '/views/dashboard.php';
            break;
        }

        if ( file_exists( $template ) ) {
            include $template;
        }

    }

    public function menu_page() {
        wp_enqueue_script( 'main' );
        include __DIR__ . '/views/dashboard.php';
    }

    // Register Custom Post Type Easy Video
    function create_easyvideo_cpt() {

        $labels = array(
            'name' => _x( 'Easy Videos', 'Post Type General Name', 'easycoupons' ),
            'singular_name' => _x( 'Easy Video', 'Post Type Singular Name', 'easycoupons' ),
            'menu_name' => _x( 'Easy Videos', 'Admin Menu text', 'easycoupons' ),
            'name_admin_bar' => _x( 'Easy Video', 'Add New on Toolbar', 'easycoupons' ),
            'archives' => __( 'Easy Video Archives', 'easycoupons' ),
            'attributes' => __( 'Easy Video Attributes', 'easycoupons' ),
            'parent_item_colon' => __( 'Parent Easy Video:', 'easycoupons' ),
            'all_items' => __( 'All Easy Videos', 'easycoupons' ),
            'add_new_item' => __( 'Add New Easy Video', 'easycoupons' ),
            'add_new' => __( 'Add New', 'easycoupons' ),
            'new_item' => __( 'New Easy Video', 'easycoupons' ),
            'edit_item' => __( 'Edit Easy Video', 'easycoupons' ),
            'update_item' => __( 'Update Easy Video', 'easycoupons' ),
            'view_item' => __( 'View Easy Video', 'easycoupons' ),
            'view_items' => __( 'View Easy Videos', 'easycoupons' ),
            'search_items' => __( 'Search Easy Video', 'easycoupons' ),
            'not_found' => __( 'Not found', 'easycoupons' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'easycoupons' ),
            'featured_image' => __( 'Featured Image', 'easycoupons' ),
            'set_featured_image' => __( 'Set featured image', 'easycoupons' ),
            'remove_featured_image' => __( 'Remove featured image', 'easycoupons' ),
            'use_featured_image' => __( 'Use as featured image', 'easycoupons' ),
            'insert_into_item' => __( 'Insert into Easy Video', 'easycoupons' ),
            'uploaded_to_this_item' => __( 'Uploaded to this Easy Video', 'easycoupons' ),
            'items_list' => __( 'Easy Videos list', 'easycoupons' ),
            'items_list_navigation' => __( 'Easy Videos list navigation', 'easycoupons' ),
            'filter_items_list' => __( 'Filter Easy Videos list', 'easycoupons' ),
        );
        $args = array(
            'label' => __( 'Easy Video', 'easycoupons' ),
            'description' => __( '', 'easycoupons' ),
            'labels' => $labels,
            'menu_icon' => 'dashicons-video-alt3',
            'supports' => array('title','thumbnail'),
            'taxonomies' => array(),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 100,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'exclude_from_search' => true,
            'show_in_rest' => false,
            'publicly_queryable' => false,
            'capability_type' => 'post',
        );

        register_post_type( 'easy-video', $args );

    }

    public function add_meta_boxes() {
        add_meta_box(
            'LockedVideo',
            __( 'Locked Video', 'easycoupons' ),
            [$this, 'meta_box_callback'],
            'easy-video',
            'normal',
            'default'
        );
    }

    /**
     * @param $post
     */
    public function meta_box_callback( $post ) {
        wp_nonce_field( 'easy_coupons_nonce', 'easy_coupons_nonce' );
        $this->field_generator( $post );
    }

    /**
     * @param $post
     */
    public function field_generator( $post ) {
        $output = '';
        foreach ( $this->fields as $field ) {
            $label      = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
            $meta_value = get_post_meta( $post->ID, $field['id'], true );
            if ( empty( $meta_value ) ) {
                if ( isset( $field['default'] ) ) {
                    $meta_value = $field['default'];
                }
            }
            switch ( $field['type'] ) {
                default:
                    $input = sprintf(
                        '<input %s id="%s" name="%s" type="%s" value="%s">', 'color' !== $field['type'] ? 'style="width: 100%"' : '',
                        $field['id'],
                        $field['id'],
                        $field['type'],
                        $meta_value
                    );
            }
            $output .= $this->format_rows( $label, $input );
        }
        echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
    }

    /**
     * @param $label
     * @param $input
     */
    public function format_rows( $label, $input ) {
        return '<div style="margin-top: 10px;"><strong>' . $label . '</strong></div><div>' . $input . '</div>';
    }

    /**
     * @param $post_id
     * @return mixed
     */
    public function save_fields( $post_id ) {
        if ( ! isset( $_POST['easy_coupons_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['easy_coupons_nonce'];
        if ( ! wp_verify_nonce( $nonce, 'easy_coupons_nonce' ) ) {
            return $post_id;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        foreach ( $this->fields as $field ) {
            if ( isset( $_POST[$field['id']] ) ) {
                switch ( $field['type'] ) {
                    case 'url':
                        $_POST[$field['id']] = esc_url_raw( $_POST[$field['id']] );
                        break;
                    case 'text':
                        $_POST[$field['id']] = sanitize_text_field( $_POST[$field['id']] );
                        break;
                }
                update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
            } else if ( 'checkbox' === $field['type'] ) {
                update_post_meta( $post_id, $field['id'], '0' );
            }
        }
    }

    /**
     * @param $post
     */
    public function callback__submitpost_box( $post ) {
        if ( 'easy-video' === $post->post_type && 'publish' === $post->post_status ) {
            echo '<div class="sc-box">
			<div class="postbox-header"><h2 class="hndle ui-sortable-handle">Video Shortcode</h2></div>
			<div class="inside">
			<input type="text" value="[easy-coupon id=', esc_attr( $post->ID ), ']" readonly>
			<p>Use this shortcode to render a locked video</p>
			</div>
			</div>';
        }
    }

}