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

        // Load CSS manager
        require_once __DIR__ . '/cbk-drolma-events-css-manager.php';
        new CBK_Drolma_Events_CSS_Manager();

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
            'rewrite'      => [ 'slug' => 'evenement', 'with_front' => false ],
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

        // Add meta box for event program_items (icon + text)
        add_meta_box(
            'cbk_drolma_event_program_items_box',
            'Programme',
            [ $this, 'render_event_program_items_box' ],
            'cbk_drolma_event',
            'normal',
            'default'
        );

        // Add meta box for event prices (single textarea)
        add_meta_box(
            'cbk_drolma_event_prices_box',
            'Tarif',
            [ $this, 'render_event_prices_box' ],
            'cbk_drolma_event',
            'normal',
            'default'
        );
    }

    public function render_details_box( $post ) {
        $start_date = get_post_meta( $post->ID, '_cbk_drolma_event_start_date', true );
        $start_time = get_post_meta( $post->ID, '_cbk_drolma_event_start_time', true );
        $end_date = get_post_meta( $post->ID, '_cbk_drolma_event_end_date', true );
        $end_time = get_post_meta( $post->ID, '_cbk_drolma_event_end_time', true );
        $location = get_post_meta( $post->ID, '_cbk_drolma_event_location', true );
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

    public function render_event_program_items_box( $post ) {
        $items = get_post_meta( $post->ID, '_cbk_drolma_event_program_items', true );
        if (!is_array($items)) $items = [];
        $icon_options = [
            'fa-universal-access' => '<div class="dashicons-before dashicons-universal-access-alt"></div>',
            'fa-minus'  => '<div class="dashicons-before dashicons-minus"></div>',
            'fa-coffee'  => '<div class="dashicons-before dashicons-coffee"></div>',
        ];
        ?>
        <div id="cbk-drolma-event-program-items-list">
            <?php foreach ($items as $i => $item): $icon = isset($item['icon']) ? (string)$item['icon'] : 'fa-minus'; ?>
                <div class="cbk-drolma-event-program-item" style="margin-bottom:10px;display:flex;gap:10px;align-items:center;">
                    <div style="display:flex;gap:8px;align-items:center;">
                        <?php foreach ($icon_options as $icon_val => $icon_html): ?>
                            <label style="cursor:pointer;">
                                <input type="radio" name="cbk_drolma_event_program_items[<?php echo $i; ?>][icon]" value="<?php echo esc_attr($icon_val); ?>" <?php echo checked($icon, $icon_val, false); ?>><?php echo $icon_html; ?></input >
                                
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <input type="text" name="cbk_drolma_event_program_items[<?php echo $i; ?>][text]" value="<?php echo esc_attr($item['text'] ?? ''); ?>" placeholder="Item text" style="width:60%;" />
                    <button type="button" class="button cbk-drolma-remove-item">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button" id="cbk-drolma-add-item">Add Item</button>
        <script>
        jQuery(document).ready(function($){
            var itemIndex = <?php echo count($items); ?>;
            var iconOptions = function(index) {
                return '<label style="cursor:pointer;">'+
                    '<input type="radio" name="cbk_drolma_event_program_items['+index+'][icon]" value="fa-universal-access" checked />'+
                    '<div class="dashicons-before dashicons-universal-access-alt"></div>'+
                '</label>'+
                '<label style="cursor:pointer;">'+
                    '<input type="radio" name="cbk_drolma_event_program_items['+index+'][icon]" value="fa-minus" />'+
                    '<div class="dashicons-before dashicons-minus"></div>'+
                '</label>'+
                '<label style="cursor:pointer;">'+
                    '<input type="radio" name="cbk_drolma_event_program_items['+index+'][icon]" value="fa-coffee" />'+
                    '<div class="dashicons-before dashicons-coffee"></div>'+
                '</label>';
            };
            $('#cbk-drolma-add-item').on('click', function(){
                var html = '<div class="cbk-drolma-event-program-item" style="margin-bottom:10px;display:flex;gap:10px;align-items:center;">'+
                    '<div style="display:flex;gap:8px;align-items:center;">'+iconOptions(itemIndex)+'</div>'+
                    '<input type="text" name="cbk_drolma_event_program_items['+itemIndex+'][text]" value="" placeholder="Item text" style="width:60%;" />'+
                    '<button type="button" class="button cbk-drolma-remove-item">Remove</button>'+
                '</div>';
                $('#cbk-drolma-event-program-items-list').append(html);
                itemIndex++;
            });
            $(document).on('click', '.cbk-drolma-remove-item', function(){
                $(this).closest('.cbk-drolma-event-program-item').remove();
            });
        });
        </script>
        <p style="font-size:smaller;">Choose an icon and enter text for each item. Uses <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a> icons.</p>
        <?php
    }

    public function render_event_prices_box( $post ) {
        $prices = get_post_meta( $post->ID, '_cbk_drolma_event_prices', true );
        $external_url = get_post_meta( $post->ID, '_cbk_drolma_event_external_url', true );
        $button_text = get_post_meta( $post->ID, '_cbk_drolma_event_button_text', true );
        ?>

        <div class="em-prices-field">
            <label for="cbk_drolma_event_external_url">External URL:</label>
            <input type="text" id="cbk_drolma_event_external_url" name="cbk_drolma_event_external_url" 
                value="<?php echo esc_attr($external_url); ?>">
        </div>

        <div class="em-prices-field">
            <label for="cbk_drolma_event_button_text">Button text:</label>
            <input type="text" id="cbk_drolma_event_button_text" name="cbk_drolma_event_button_text" 
                value="<?php echo esc_attr($button_text); ?>">
        </div>

        <div class="em-prices-field">
            <textarea id="cbk_drolma_event_prices" name="cbk_drolma_event_prices" rows="5" style="width:100%;"><?php echo esc_textarea($prices); ?></textarea>
        </div>
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
        // Save event items (icon + text list)
        if (isset($_POST['cbk_drolma_event_program_items']) && is_array($_POST['cbk_drolma_event_program_items'])) {
            $clean_items = [];
            foreach ($_POST['cbk_drolma_event_program_items'] as $item) {
                $icon = isset($item['icon']) ? sanitize_text_field($item['icon']) : 'fa-minus';
                $text = isset($item['text']) ? sanitize_text_field($item['text']) : '';
                if ($text !== '') {
                    $clean_items[] = [ 'icon' => $icon, 'text' => $text ];
                }
            }
            update_post_meta($post_id, '_cbk_drolma_event_program_items', $clean_items);
        } else {
            delete_post_meta($post_id, '_cbk_drolma_event_program_items');
        }
        // Save event prices (single textarea)
        if (isset($_POST['cbk_drolma_event_prices'])) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_event_prices',
                sanitize_textarea_field($_POST['cbk_drolma_event_prices'])
            );
        } else {
            delete_post_meta($post_id, '_cbk_drolma_event_prices');
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