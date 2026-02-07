<?php
if ( ! defined( 'ABSPATH' ) ) exit;
    
class CBK_Drolma_Courses_Calendar_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'cbk_drolma_course_calendar'; }

    public function get_title() { return 'CBK Drolma Courses Calendar'; }

    public function get_icon() { return 'eicon-calendar'; }

    public function get_categories() { return ['general']; }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Calendar Settings',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $courses = get_posts([
            'post_type' => 'cbk_drolma_course',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ]);

        $course_options = [];
        foreach ($courses as $course) {
            $course_options[$course->ID] = $course->post_title;
        }

        $this->add_control(
            'selected_course',
            [
                'label' => 'Select Course',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $course_options,
                'default' => !empty($course_options) ? array_key_first($course_options) : '',
            ]
        );

        $this->add_control(
            'start_date',
            [
                'label' => 'Start Date',
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false,
                ],
                'default' => date('Y-m-01'),
            ]
        );

        $this->add_control(
            'end_date',
            [
                'label' => 'End Date',
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false,
                ],
                'default' => date('Y-m-t', strtotime('+6 months')),
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => 'Number of Columns',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'step' => 1,
                'default' => 3,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'month_style_section',
            [
                'label' => 'Month Header Style',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'month_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-courses-calendar-month-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'month_typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-courses-calendar-month-title',
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
                    '{{WRAPPER}} .cbk-drolma-courses-calendar-lesson.past-lesson' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'lesson_color',
            [
                'label' => 'Lesson Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-courses-calendar-lesson' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'lesson_date_typography',
                'label' => 'Lesson Date Typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-courses-calendar-lesson-date',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'lesson_name_typography',
                'label' => 'Lesson Name Typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-courses-calendar-lesson-name',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $course_id = $settings['selected_course'];
        $start_date = $settings['start_date'];
        $end_date = $settings['end_date'];
        $columns = $settings['columns'];

        if (!$course_id) {
            echo '<p>Please select a course in the widget settings.</p>';
            return;
        }

        $course = get_post($course_id);
        if (!$course) {
            echo '<p>Course not found.</p>';
            return;
        }

        $lessons = get_post_meta($course_id, '_cbk_drolma_course_lessons', true);

        if (!is_array($lessons)) {
            $lessons = [];
        }

        $lessons_by_month = [];
        
        foreach ($lessons as $lesson) {
            if (!empty($lesson['date'])) {
                $lesson_date = $lesson['date'];
                
                if ($lesson_date >= $start_date && $lesson_date <= $end_date) {
                    $month_key = date('Y-m', strtotime($lesson_date));
                    
                    if (!isset($lessons_by_month[$month_key])) {
                        $lessons_by_month[$month_key] = [];
                    }
                    
                    $lessons_by_month[$month_key][] = $lesson;
                }
            }
        }

        foreach ($lessons_by_month as $month_key => &$month_lessons) {
            usort($month_lessons, function($a, $b) {
                return strcmp($a['date'], $b['date']);
            });
        }

        ksort($lessons_by_month);

        if (empty($lessons_by_month)) {
            echo '<p>No lessons found in the selected date range.</p>';
            return;
        }
        ?>
        <div>
            <div style="display: grid; grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr); gap: 20px;">

                <?php foreach ($lessons_by_month as $month_key => &$month_lessons) : ?>
                    <div style="background: #ffffff; overflow: hidden;">
                        <h2 class="cbk-drolma-courses-calendar-month-title">
                            <?php echo esc_html(date_i18n('F Y', strtotime($month_key . '-01'))); ?>
                        </h2>
                        <?php foreach ($month_lessons as $month_lesson) : ?>
                            <?php
                                // Get current date and lesson date as timestamps for comparison
                                $current_date = strtotime(date('Y-m-d'));
                                $lesson_date = strtotime($month_lesson['date']);
                                $span_date_class="cbk-drolma-courses-calendar-lesson";
                                if( $lesson_date < $current_date){
                                    $span_date_class = $span_date_class.' past-lesson';
                                }
                            ?>
                            <div class="<?php echo $span_date_class; ?>">
                                <span class="cbk-drolma-courses-calendar-lesson-date">
                                    <?php echo esc_html(date_i18n('j F', $lesson_date)); ?> : 
                                </span>
                                <?php if (!empty($month_lesson['name'])) : ?>
                                    <span class="cbk-drolma-courses-calendar-lesson-name"><?php echo esc_html($month_lesson['name']); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <style>
                @media (max-width: 1024px) {
                    .calendar-grid {
                        grid-template-columns: repeat(2, 1fr) !important;
                        gap: 20px;
                    }
                }
                
                @media (max-width: 768px) {
                    .calendar-grid {
                        grid-template-columns: 1fr !important;
                        gap: 20px;
                    }
                }
            </style>
        </div>
        <?php
    }
}