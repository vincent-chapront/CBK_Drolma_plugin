<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBK_Drolma_Pass_Manager {
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_cbk_drolma_pass', [ $this, 'save_pass_meta' ] );
        add_action( 'admin_menu', [ $this, 'add_pass_menu' ] );
        add_filter( 'manage_cbk_drolma_pass_posts_columns', [ $this, 'set_custom_columns' ] );
        add_action( 'manage_cbk_drolma_pass_posts_custom_column', [ $this, 'custom_column_content' ], 10, 2 );
        add_action( 'pre_get_posts', [ $this, 'modify_passes_admin_order' ] );
    }

    public function register_post_type() {
        register_post_type( 'cbk_drolma_pass', [
            'labels' => [
                'name' => 'Meditation Passes',
                'singular_name' => 'Meditation Pass',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'capabilities' => [
                'edit_post' => 'edit_others_posts',
                'read_post' => 'read',
                'delete_post' => 'delete_others_posts',
                'edit_posts' => 'edit_others_posts',
                'edit_others_posts' => 'edit_others_posts',
                'publish_posts' => 'publish_posts',
                'read_private_posts' => 'read_private_posts',
            ],
            'supports' => [], // Remove 'title' and 'editor' support
            'menu_icon' => 'dashicons-tickets-alt',
        ] );
    }

    public function add_pass_menu() {
        add_menu_page(
            'CBK Drolma Meditation Pass',
            'CBK Drolma Meditation Pass',
            'edit_others_posts',
            'edit.php?post_type=cbk_drolma_pass',
            '',
            'dashicons-tickets-alt',
            21
        );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'cbk_drolma_pass_details_box',
            'Pass Details',
            [ $this, 'render_details_box' ],
            'cbk_drolma_pass',
            'normal',
            'default'
        );
        // Remove the default title and editor input with some CSS
        add_action('admin_head', function() {
            $screen = get_current_screen();
            if ($screen && $screen->post_type === 'cbk_drolma_pass') {
                echo '<style>#titlediv, #postdivrich { display:none !important; }</style>';
            }
        });
    }

    public function render_details_box( $post ) {
        $month = get_post_meta( $post->ID, '_cbk_drolma_pass_month', true );
        $year = get_post_meta( $post->ID, '_cbk_drolma_pass_year', true );
        $external_url = get_post_meta( $post->ID, '_cbk_drolma_pass_external_url', true );
        ?>
        <p>
            <label for="cbk_drolma_pass_month">Month:</label>
            <select name="cbk_drolma_pass_month" id="cbk_drolma_pass_month">
                <?php
                $months = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];
                foreach ($months as $num => $name) {
                    echo '<option value="' . $num . '"' . selected($month, $num, false) . '>' . esc_html($name) . '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="cbk_drolma_pass_year">Year:</label>
            <input type="number" name="cbk_drolma_pass_year" id="cbk_drolma_pass_year" value="<?php echo esc_attr($year); ?>" min="2020" max="2100" />
        </p>
        <p>
            <label for="cbk_drolma_pass_external_url">External URL:</label>
            <input type="url" name="cbk_drolma_pass_external_url" id="cbk_drolma_pass_external_url" value="<?php echo esc_attr($external_url); ?>" style="width:100%;" />
        </p>
        <?php
    }

    public function save_pass_meta( $post_id ) {
        if ( isset( $_POST['cbk_drolma_pass_month'] ) ) {
            update_post_meta( $post_id, '_cbk_drolma_pass_month', intval($_POST['cbk_drolma_pass_month']) );
        }
        if ( isset( $_POST['cbk_drolma_pass_year'] ) ) {
            update_post_meta( $post_id, '_cbk_drolma_pass_year', intval($_POST['cbk_drolma_pass_year']) );
        }
        if ( isset( $_POST['cbk_drolma_pass_external_url'] ) ) {
            update_post_meta( $post_id, '_cbk_drolma_pass_external_url', esc_url_raw($_POST['cbk_drolma_pass_external_url']) );
        }
    }

    public function set_custom_columns( $columns ) {
        $new_columns = [];
        $new_columns['cbk_drolma_pass_year'] = 'Year';
        $new_columns['cbk_drolma_pass_month'] = 'Month';
        $new_columns['cbk_drolma_pass_external_url'] = 'External URL';
        // Remove title and add our columns
        return $new_columns;
    }

    public function custom_column_content( $column, $post_id ) {
        if ( $column === 'cbk_drolma_pass_month' ) {
            $month = get_post_meta( $post_id, '_cbk_drolma_pass_month', true );
            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            echo isset($months[$month]) ? esc_html($months[$month]) : '';
        }
        if ( $column === 'cbk_drolma_pass_year' ) {
            echo esc_html( get_post_meta( $post_id, '_cbk_drolma_pass_year', true ) );
        }
        if ( $column === 'cbk_drolma_pass_external_url' ) {
            $url = get_post_meta( $post_id, '_cbk_drolma_pass_external_url', true );
            if ($url) {
                echo '<a href="' . esc_url($url) . '" target="_blank">' . esc_html($url) . '</a>';
            }
        }
    }

    public function modify_passes_admin_order( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }
        if ( $query->get('post_type') === 'cbk_drolma_pass' ) {
            $query->set( 'orderby', [
                'year_clause' => 'ASC',
                'month_clause' => 'ASC',
            ] );
            $query->set( 'meta_query', [
                'year_clause' => [
                    'key' => '_cbk_drolma_pass_year',
                    'type' => 'NUMERIC',
                ],
                'month_clause' => [
                    'key' => '_cbk_drolma_pass_month',
                    'type' => 'NUMERIC',
                ],
            ] );
        }
    }
}

new CBK_Drolma_Pass_Manager();
