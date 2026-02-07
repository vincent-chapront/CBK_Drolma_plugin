<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBK_Drolma_Courses_Manager {

    public function __construct() {
        add_action( 'init', [ $this, 'register_post_types' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_cbk_drolma_course', [ $this, 'save_course_meta' ] );
        add_action( 'admin_menu', [ $this, 'add_courses_menu' ] );
    }

    public function register_post_types() {
        register_post_type( 'cbk_drolma_course', [
            'labels' => [
                'name'          => 'CBK Drolma Courses',
                'singular_name' => 'CBK Drolma Course',
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'supports'     => [ 'title' ],
        ] );
    }

    public function add_courses_menu() {
        add_menu_page(
            'CBK Drolma Courses',
            'CBK Drolma Courses',
            'edit_posts',
            'edit.php?post_type=cbk_drolma_course',
            '',
            'dashicons-welcome-learn-more',
            20
        );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'cbk_drolma_course_details_box',
            'Details',
            [ $this, 'render_details_box' ],
            'cbk_drolma_course',
            'normal',
            'default'
        );
        add_meta_box(
            'cbk_drolma_course_lessons_box',
            'Lessons',
            [ $this, 'render_lessons_box' ],
            'cbk_drolma_course'
        );
    }
public function render_details_box( $post ) {
    $short_desc = get_post_meta( $post->ID, '_cbk_drolma_course_short_description', true );
    ?>
    <p>
        <label for="cbk_drolma_course_short_description">Short Description</label><br>
        <input type="text" 
               name="cbk_drolma_course_short_description" 
               id="cbk_drolma_course_short_description" 
               value="<?php echo esc_attr( $short_desc ); ?>" 
               style="width:100%;" />
    </p>
    <?php
}
    public function render_lessons_box( $post ) {
        $lessons = get_post_meta( $post->ID, '_cbk_drolma_course_lessons', true );
        if ( ! is_array( $lessons ) ) {
            $lessons = [];
        }
        ?>
        <table id="cbk-drolma-course-lessons-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name (optional)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $lessons as $index => $lesson ): ?>
                <tr>
                    <td>
                        <input type="date"
                               name="cbk_drolma_course_lessons[<?php echo $index; ?>][date]"
                               value="<?php echo esc_attr( $lesson['date'] ); ?>" />
                    </td>
                    <td>
                        <input type="text"
                               name="cbk_drolma_course_lessons[<?php echo $index; ?>][name]"
                               value="<?php echo esc_attr( $lesson['name'] ); ?>" />
                    </td>
                    <td>
                        <button class="button cbk-drolma-course-remove">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p>
            <button class="button" id="cbk-drolma-course-add-lesson">Add Lesson</button>
        </p>

        <script>
        (function($){
            $('#cbk-drolma-course-add-lesson').on('click', function(e){
                e.preventDefault();
                var index = $('#cbk-drolma-course-lessons-table tbody tr').length;
                $('#cbk-drolma-course-lessons-table tbody').append(
                    '<tr>' +
                    '<td><input type="date" name="cbk_drolma_course_lessons['+index+'][date]" /></td>' +
                    '<td><input type="text" name="cbk_drolma_course_lessons['+index+'][name]" /></td>' +
                    '<td><button class="button cbk-drolma-course-remove">Remove</button></td>' +
                    '</tr>'
                );
            });

            $(document).on('click', '.cbk-drolma-course-remove', function(e){
                e.preventDefault();
                $(this).closest('tr').remove();
            });
        })(jQuery);
        </script>
        <?php
    }

    public function save_course_meta( $post_id ) {
        if ( empty( $_POST['cbk_drolma_course_lessons'] ) ) return;
        if ( isset( $_POST['cbk_drolma_course_short_description'] ) ) {
            update_post_meta(
                $post_id,
                '_cbk_drolma_course_short_description',
                sanitize_text_field( $_POST['cbk_drolma_course_short_description'] )
            );
        }
        $clean = [];
        foreach ( $_POST['cbk_drolma_course_lessons'] as $lesson ) {
            if ( empty( $lesson['date'] ) ) continue;

            $clean[] = [
                'date' => sanitize_text_field( $lesson['date'] ),
                'name' => sanitize_text_field( $lesson['name'] ?? '' ),
            ];
        }

        update_post_meta( $post_id, '_cbk_drolma_course_lessons', $clean );
    }
}
