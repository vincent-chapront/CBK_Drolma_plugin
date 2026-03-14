<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBK_Drolma_Events_CSS_Manager {

    private $option_name = 'cbk_drolma_events_custom_css';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_css_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_custom_css' ] );
    }

    /**
     * Add CSS settings page to the admin menu
     */
    public function add_css_settings_page() {
        add_submenu_page(
            'edit.php?post_type=cbk_drolma_event',
            'Event Styles',
            'Styles',
            'manage_options',
            'cbk-drolma-event-styles',
            [ $this, 'render_css_settings_page' ]
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'cbk_drolma_events_css_settings',
            $this->option_name,
            [ $this, 'sanitize_css' ]
        );
    }

    /**
     * Sanitize CSS input
     */
    public function sanitize_css( $input ) {
        // Basic sanitization - you may want to add more robust CSS validation
        return wp_strip_all_tags( $input );
    }

    /**
     * Render the CSS settings page
     */
    public function render_css_settings_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Get current CSS
        $current_css = get_option( $this->option_name, '' );

        // Handle form submission
        if ( isset( $_POST['cbk_drolma_save_css'] ) && check_admin_referer( 'cbk_drolma_css_nonce' ) ) {
            $new_css = isset( $_POST['cbk_drolma_custom_css'] ) ? $_POST['cbk_drolma_custom_css'] : '';
            update_option( $this->option_name, $this->sanitize_css( $new_css ) );
            echo '<div class="notice notice-success is-dismissible"><p>CSS saved successfully!</p></div>';
            $current_css = get_option( $this->option_name, '' );
        }

        ?>
        <div class="wrap">
            <h1>Event Styles</h1>
            <p>Add custom CSS to style your event pages. Changes will apply to all single event pages.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'cbk_drolma_css_nonce' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cbk_drolma_custom_css">Custom CSS</label>
                        </th>
                        <td>
                            <textarea 
                                id="cbk_drolma_custom_css" 
                                name="cbk_drolma_custom_css" 
                                rows="20" 
                                class="large-text code"
                                spellcheck="false"
                                style="font-family: monospace; font-size: 13px;"
                            ><?php echo esc_textarea( $current_css ); ?></textarea>
                            <p class="description">
                                Enter your custom CSS here. Example:<br>
                                <code>.cbk-drolma-event-single { background-color: #f5f5f5; }</code>
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input 
                        type="submit" 
                        name="cbk_drolma_save_css" 
                        id="submit" 
                        class="button button-primary" 
                        value="Save CSS"
                    >
                </p>
            </form>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>CSS Classes Reference</h2>
                <p>Here are some useful classes you can target in your custom CSS:</p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><code>.cbk-drolma-event-single</code> - Main event page container</li>
                    <li><code>.cbk-drolma-event-category</code> - Event category heading</li>
                    <li><code>.cbk-drolma-event-hosts</code> - Hosts section</li>
                    <li><code>.cbk-drolma-event-image</code> - Event image container</li>
                    <li><code>.cbk-drolma-event-content</code> - Event content area</li>
                    <li><code>.cbk-drolma-event-meta</code> - Event metadata section</li>
                    <li><code>.cbk-drolma-event-btn</code> - External link button</li>
                </ul>
            </div>
        </div>

        <style>
            .code {
                tab-size: 4;
            }
        </style>
        <?php
    }

    /**
     * Enqueue custom CSS on frontend
     */
    public function enqueue_custom_css() {
        // Only load on single event pages
        if ( ! is_singular( 'cbk_drolma_event' ) ) {
            return;
        }

        $custom_css = get_option( $this->option_name, '' );
        
        if ( ! empty( $custom_css ) ) {
            wp_add_inline_style( 'wp-block-library', $custom_css );
            
            // If wp-block-library isn't enqueued, add a fallback
            if ( ! wp_style_is( 'wp-block-library', 'enqueued' ) ) {
                wp_register_style( 'cbk-drolma-events-custom', false );
                wp_enqueue_style( 'cbk-drolma-events-custom' );
                wp_add_inline_style( 'cbk-drolma-events-custom', $custom_css );
            }
        }
    }
}