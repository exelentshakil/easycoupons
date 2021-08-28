<?php
namespace Easy\Coupons\Admin;

class EasyCoupons {

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
        // add custom Easy Video post type
        add_action('init', [$this, 'create_easyvideo_cpt']);

        // add custom meta box
        add_action( 'add_meta_boxes', [$this, 'add_meta_boxes'] );
        add_action( 'save_post', [$this, 'save_fields'] );
    }

    public function init( $page ) {

        $page = isset( $_GET['action'] ) ? $_GET['action'] : '';

        switch ( $page ) {
        case 'settings':
            $template = __DIR__ . '/views/settings.php';
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
            'name' => _x( 'Easy Videos', 'Post Type General Name', 'easy-coupons' ),
            'singular_name' => _x( 'Easy Video', 'Post Type Singular Name', 'easy-coupons' ),
            'menu_name' => _x( 'Easy Videos', 'Admin Menu text', 'easy-coupons' ),
            'name_admin_bar' => _x( 'Easy Video', 'Add New on Toolbar', 'easy-coupons' ),
            'archives' => __( 'Easy Video Archives', 'easy-coupons' ),
            'attributes' => __( 'Easy Video Attributes', 'easy-coupons' ),
            'parent_item_colon' => __( 'Parent Easy Video:', 'easy-coupons' ),
            'all_items' => __( 'All Easy Videos', 'easy-coupons' ),
            'add_new_item' => __( 'Add New Easy Video', 'easy-coupons' ),
            'add_new' => __( 'Add New', 'easy-coupons' ),
            'new_item' => __( 'New Easy Video', 'easy-coupons' ),
            'edit_item' => __( 'Edit Easy Video', 'easy-coupons' ),
            'update_item' => __( 'Update Easy Video', 'easy-coupons' ),
            'view_item' => __( 'View Easy Video', 'easy-coupons' ),
            'view_items' => __( 'View Easy Videos', 'easy-coupons' ),
            'search_items' => __( 'Search Easy Video', 'easy-coupons' ),
            'not_found' => __( 'Not found', 'easy-coupons' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'easy-coupons' ),
            'featured_image' => __( 'Featured Image', 'easy-coupons' ),
            'set_featured_image' => __( 'Set featured image', 'easy-coupons' ),
            'remove_featured_image' => __( 'Remove featured image', 'easy-coupons' ),
            'use_featured_image' => __( 'Use as featured image', 'easy-coupons' ),
            'insert_into_item' => __( 'Insert into Easy Video', 'easy-coupons' ),
            'uploaded_to_this_item' => __( 'Uploaded to this Easy Video', 'easy-coupons' ),
            'items_list' => __( 'Easy Videos list', 'easy-coupons' ),
            'items_list_navigation' => __( 'Easy Videos list navigation', 'easy-coupons' ),
            'filter_items_list' => __( 'Filter Easy Videos list', 'easy-coupons' ),
        );
        $args = array(
            'label' => __( 'Easy Video', 'easy-coupons' ),
            'description' => __( '', 'easy-coupons' ),
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
            __( 'Locked Video', 'easy-coupons' ),
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

}