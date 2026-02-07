<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBK_Drolma_Events_Manager {

    public function __construct() {
        add_action( 'init', [ $this, 'register_post_types' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_cbk_drolma_event', [ $this, 'save_event_meta' ] );
        add_action( 'admin_menu', [ $this, 'add_events_menu' ] );
        // Load template loader
        require_once __DIR__ . '/cbk-drolma-events-template-loader.php';
        new CBK_Drolma_Events_Template_Loader();

        // Enforce only one category per event
        add_action( 'admin_enqueue_scripts', [ $this, 'limit_event_category_selection' ] );
    }

    public function register_post_types() {
        register_post_type( 'cbk_drolma_event', [
            'labels' => [
                'name'          => 'CBK Drolma Events',
                'singular_name' => 'CBK Drolma Event',
            ],
            'public'       => true,
            'show_ui'      => true,
            'show_in_menu' => false,
            'supports'     => [ 'title','editor' ],
            'rewrite'      => [ 'slug' => 'e' ],  // Empty slug for root-level URLs
        ] );

        // Register hierarchical taxonomy for event categories
        register_taxonomy(
            'cbk_drolma_event_category',
            'cbk_drolma_event',
            [
                'labels' => [
                    'name' => 'Event Categories',
                    'singular_name' => 'Event Category',
                    'parent_item' => 'Parent Category',
                    'parent_item_colon' => 'Parent Category:',
                    'all_items' => 'All Categories',
                    'edit_item' => 'Edit Category',
                    'view_item' => 'View Category',
                    'update_item' => 'Update Category',
                    'add_new_item' => 'Add New Category',
                    'new_item_name' => 'New Category Name',
                    'menu_name' => 'Event Categories',
                ],
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => [ 'slug' => 'event-category' ],
            ]
        );

        // Register Host post type
        register_post_type( 'cbk_drolma_host', [
            'labels' => [
                'name' => 'Hosts',
                'singular_name' => 'Host',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'menu_icon' => 'dashicons-businessman',
        ] );
    }

    public function add_events_menu() {
        add_menu_page(
            'CBK Drolma Events',
            'CBK Drolma Events',
            'edit_posts',
            'edit.php?post_type=cbk_drolma_event',
            '',
            'dashicons-welcome-learn-more',
            20
        );
        // Hosts submenu
        add_submenu_page(
            'edit.php?post_type=cbk_drolma_event',
            'Hosts',
            'Hosts',
            'edit_posts',
            'edit.php?post_type=cbk_drolma_host'
        );
        // Categories submenu
        add_submenu_page(
            'edit.php?post_type=cbk_drolma_event',
            'Event Categories',
            'Categories',
            'manage_categories',
            'edit-tags.php?taxonomy=cbk_drolma_event_category&post_type=cbk_drolma_event'
        );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'cbk_drolma_event_details_box',
            'Details',
            [ $this, 'render_details_box' ],
            'cbk_drolma_event',
            'normal',
            'default'
        );

        // Add meta box for selecting hosts on event edit screen
        add_meta_box(
            'cbk_drolma_event_hosts_box',
            'Hosts',
            [ $this, 'render_hosts_box' ],
            'cbk_drolma_event',
            'side',
            'default'
        );

        // Add meta box for event image
        add_meta_box(
            'cbk_drolma_event_image_box',
            'Event Image',
            [ $this, 'render_event_image_box' ],
            'cbk_drolma_event',
            'side',
            'default'
        );
    }

    public function render_details_box( $post ) {
        $start_date = get_post_meta( $post->ID, '_cbk_drolma_event_start_date', true );
        $start_time = get_post_meta( $post->ID, '_cbk_drolma_event_start_time', true );
        $end_date = get_post_meta( $post->ID, '_cbk_drolma_event_end_date', true );
        $end_time = get_post_meta( $post->ID, '_cbk_drolma_event_end_time', true );
        $location = get_post_meta( $post->ID, '_cbk_drolma_event_location', true );
        $external_url = get_post_meta( $post->ID, '_cbk_drolma_event_external_url', true );
        $button_text = get_post_meta( $post->ID, '_cbk_drolma_event_button_text', true );
        ?>
    
        <style>
            .em-datetime-field { margin-bottom: 15px; }
        </style>
        <div class="em-datetime-field">
            <label for="cbk_drolma_event_start_date">Start Date:</label>
            <input type="date" id="cbk_drolma_event_start_date" name="cbk_drolma_event_start_date" 
                value="<?php echo esc_attr($start_date); ?>">
            <input type="time" id="cbk_drolma_event_start_time" name="cbk_drolma_event_start_time" 
                value="<?php echo esc_attr($start_time); ?>">
        </div>

        <div class="em-datetime-field">
            <label for="cbk_drolma_event_end_date">End Date:</label>
            <input type="date" id="cbk_drolma_event_end_date" name="cbk_drolma_event_end_date" 
                value="<?php echo esc_attr($end_date); ?>">
            <input type="time" id="cbk_drolma_event_end_time" name="cbk_drolma_event_end_time" 
                value="<?php echo esc_attr($end_time); ?>">
        </div>

        <div class="em-datetime-field">
            <label for="cbk_drolma_event_location">Location:</label>
            <input type="text" id="cbk_drolma_event_location" name="cbk_drolma_event_location" 
                value="<?php echo esc_attr($location); ?>">
        </div>

        <div class="em-datetime-field">
            <label for="cbk_drolma_event_external_url">External URL:</label>
            <input type="text" id="cbk_drolma_event_external_url" name="cbk_drolma_event_external_url" 
                value="<?php echo esc_attr($external_url); ?>">
        </div>

        <div class="em-datetime-field">
            <label for="cbk_drolma_event_button_text">Button text:</label>
            <input type="text" id="cbk_drolma_event_button_text" name="cbk_drolma_event_button_text" 
                value="<?php echo esc_attr($button_text); ?>">
        </div>
        <?php
    }

    public function render_hosts_box( $post ) {
        $selected = get_post_meta( $post->ID, '_cbk_drolma_event_hosts', true );
        if ( ! is_array($selected) ) $selected = [];
        $hosts = get_posts([
            'post_type' => 'cbk_drolma_host',
            'numberposts' => -1,
            'post_status' => 'publish',
        ]);
        echo '<select name="cbk_drolma_event_hosts[]" multiple style="width:100%; min-height:100px;">';
        foreach ( $hosts as $host ) {
            $is_selected = in_array( $host->ID, $selected ) ? 'selected' : '';
            echo '<option value="' . esc_attr($host->ID) . '" ' . $is_selected . '>' . esc_html($host->post_title) . '</option>';
        }
        echo '</select>';
        echo '<p style="font-size:smaller;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple hosts.</p>';
    }

    public function render_event_image_box( $post ) {
        $image_id = get_post_meta( $post->ID, '_cbk_drolma_event_image_id', true );
        $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
        echo '<div id="cbk-drolma-event-image-preview">';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" style="max-width:100%;height:auto;" />';
        }
        echo '</div>';
        echo '<input type="hidden" id="cbk_drolma_event_image_id" name="cbk_drolma_event_image_id" value="' . esc_attr($image_id) . '" />';
        echo '<button type="button" class="button" id="cbk-drolma-event-image-upload">Select Image</button>';
        echo '<button type="button" class="button" id="cbk-drolma-event-image-remove" style="margin-left:10px;">Remove</button>';
        ?>
        <script>
        jQuery(document).ready(function($){
            var frame;
            $('#cbk-drolma-event-image-upload').on('click', function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({ title: 'Select or Upload Event Image', button: { text: 'Use this image' }, multiple: false });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#cbk_drolma_event_image_id').val(attachment.id);
                    $('#cbk-drolma-event-image-preview').html('<img src="'+attachment.url+'" style="max-width:100%;height:auto;" />');
                });
                frame.open();
            });
            $('#cbk-drolma-event-image-remove').on('click', function(){
                $('#cbk_drolma_event_image_id').val('');
                $('#cbk-drolma-event-image-preview').html('');
            });
        });
        </script>
        <?php
    }

    public function save_event_meta( $post_id ) {
        if ( isset( $_POST['cbk_drolma_event_start_date'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_start_date',
                sanitize_text_field( $_POST['cbk_drolma_event_start_date'] )
            );
        }
        if ( isset( $_POST['cbk_drolma_event_start_time'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_start_time',
                sanitize_text_field( $_POST['cbk_drolma_event_start_time'] )
            );
        }
        if ( isset( $_POST['cbk_drolma_event_end_date'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_end_date',
                sanitize_text_field( $_POST['cbk_drolma_event_end_date'] )
            );
        }
        if ( isset( $_POST['cbk_drolma_event_end_time'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_end_time',
                sanitize_text_field( $_POST['cbk_drolma_event_end_time'] )
            );
        }
        if ( isset( $_POST['cbk_drolma_event_location'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_location',
                sanitize_text_field( $_POST['cbk_drolma_event_location'] )
            );
        }
        if ( isset( $_POST['cbk_drolma_event_external_url'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_external_url',
                sanitize_text_field( $_POST['cbk_drolma_event_external_url'] )
            );
        }
        if ( isset( $_POST['cbk_drolma_event_button_text'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_button_text',
                sanitize_text_field( $_POST['cbk_drolma_event_button_text'] )
            );
        }
        // Save hosts
        if ( isset($_POST['cbk_drolma_event_hosts']) ) {
            $host_ids = array_map('intval', (array)$_POST['cbk_drolma_event_hosts']);
            update_post_meta( $post_id, '_cbk_drolma_event_hosts', $host_ids );
        } else {
            delete_post_meta( $post_id, '_cbk_drolma_event_hosts' );
        }
        // Save event image
        if ( isset($_POST['cbk_drolma_event_image_id']) ) {
            update_post_meta( $post_id, '_cbk_drolma_event_image_id', intval($_POST['cbk_drolma_event_image_id']) );
        } else {
            delete_post_meta( $post_id, '_cbk_drolma_event_image_id' );
        }
        $clean = [];
    }

    public function limit_event_category_selection() {
        global $typenow;
        if ( $typenow === 'cbk_drolma_event' ) {
            echo '<script>jQuery(document).ready(function($){
                var catChecks = $("#cbk_drolma_event_categorychecklist input[type=checkbox]");
                catChecks.on("change", function(){
                    if($(this).is(":checked")){
                        catChecks.not(this).prop("checked", false);
                    }
                });
            });</script>';
        }
    }
}
