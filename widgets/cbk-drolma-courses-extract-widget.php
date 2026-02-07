<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBK_Drolma_Courses_Extract_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'cbk_drolma_course_extract'; }
    public function get_title() { return 'CBK Drolma Course Extract'; }
    public function get_icon() { return 'eicon-text'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {

        $courses = get_posts([
            'post_type'   => 'cbk_drolma_course',
            'numberposts' => -1,
        ]);

        $course_options = [];
        foreach ( $courses as $course ) {
            $course_options[ $course->ID ] = $course->post_title;
        }

        $this->start_controls_section( 'content', [ 'label' => 'Content' ] );

        $this->add_control( 'course_id', [
            'label'   => 'Course',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => $course_options,
            'default' => !empty($course_options) ? array_key_first($course_options) : '',
        ]);

        $this->add_control(
            'number_of_past_lesson',
            [
                'label' => 'Number of past lessons',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 1,
            ]
        );

        $this->add_control(
            'number_of_upcoming_lesson',
            [
                'label' => 'Number of upcoming lessons',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 3,
            ]
        );

        $this->end_controls_section();        

        // Style Section
        $this->start_controls_section(
            'title_section',
            [
                'label' => 'Title',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Title Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-course-extract-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-course-extract-title',
            ]
        );
            
        $this->end_controls_section();

        $this->start_controls_section(
            'description_section',
            [
                'label' => 'Description',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => 'Description Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-course-extract-description' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-course-extract-description',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lesson_section',
            [
                'label' => 'Lessons',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'lesson_past_color',
            [
                'label' => 'Past Lesson Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-course-extract-course-widget-lesson.past-lesson' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'lesson_color',
            [
                'label' => 'Lesson Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-course-extract-course-widget-lesson' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'lesson_date_typography',
                'label' => 'Lesson Date Typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-course-extract-course-widget-lesson-date',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'lesson_name_typography',
                'label' => 'Lesson Name Typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-course-extract-course-widget-lesson-name',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $course_id = $settings['course_id' ];
        if ( ! $course_id ) return;

        $post = get_post( $course_id );
        $lessons = get_post_meta( $course_id, '_cbk_drolma_course_lessons', true );
        if ( !is_array( $lessons ) ) $lessons = [];

        $passed_count = $settings['number_of_past_lesson'];
        $upcoming_count = $settings['number_of_upcoming_lesson'];

        $today = current_time('Y-m-d');

        $past_lessons = [];
        $upcoming_lessons = [];

        foreach ($lessons as $lesson) {
            if (!empty($lesson['date'])) {
                if ($lesson['date'] < $today) {
                    $past_lessons[] = $lesson;
                } else {
                    $upcoming_lessons[] = $lesson;
                }
            } else {
                $upcoming_lessons[] = $lesson;
            }
        }

        usort($past_lessons, function($a, $b) {
            if (empty($a['date'])) return 1;
            if (empty($b['date'])) return -1;
            return strcmp($a['date'], $b['date']);
        });

        usort($upcoming_lessons, function($a, $b) {
            if (empty($a['date'])) return 1;
            if (empty($b['date'])) return -1;
            return strcmp($a['date'], $b['date']);
        });

        $past_lessons = array_slice($past_lessons, -1* $passed_count, $passed_count);
        $upcoming_lessons = array_slice($upcoming_lessons, 0, $upcoming_count);
        $short_desc = get_post_meta( $course_id, '_cbk_drolma_course_short_description', true );
        ?>
        <div class="cbk-drolma-course-extract-course">
        <h2 class="cbk-drolma-course-extract-title"><?php echo esc_html( $post->post_title )?></h2>
        <h4 class="cbk-drolma-course-extract-description"><?php echo wpautop( $short_desc )?></h4>
        
        <?php
        foreach ( $past_lessons as $lesson ) {
            echo $this->render_lesson($lesson,'past-lesson');
        }
        foreach ( $upcoming_lessons as $lesson ) {
            echo $this->render_lesson($lesson,'');
        }

        echo '</div>';
    }

    private function render_lesson($lesson, $class)
    {
        ?>
            <div class="cbk-drolma-course-extract-course-widget-lesson <?php echo $class?>">
            <span class="cbk-drolma-course-extract-course-widget-lesson-date">
                <?php echo esc_html(date_i18n('j F Y', strtotime($lesson['date'])))?>
            </span>
            <?php if ( ! empty( $lesson['name'] ) ) : ?>
                <span class="cbk-drolma-course-extract-course-widget-lesson-name"> : <?php echo esc_html( $lesson['name'] )?><span>
            <?php endif; ?>
        </div>
        <?php
    }
}
