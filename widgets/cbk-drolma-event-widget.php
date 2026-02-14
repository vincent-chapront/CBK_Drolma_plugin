<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBK_Drolma_Event_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'cbk_drolma_event'; }
    public function get_title() { return 'CBK Drolma Event'; }
    public function get_icon() { return 'eicon-calendar'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'elementor'),
        ]);
        $this->add_control('title', [
            'label' => __('Title', 'elementor'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => '',
            'label_block' => true,
        ]);
        $this->add_control('pre_title', [
            'label' => __('Pre-title', 'elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'label_block' => true,
        ]);
        $this->add_control('start_date', [
            'label' => __('Start Date', 'elementor'),
            'type' => \Elementor\Controls_Manager::DATE_TIME,
            'picker_options' => [ 'enableTime' => false ],
        ]);
        $this->add_control('start_time', [
            'label' => __('Start Time', 'elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'HH:MM',
            'description' => __('Enter the start time (e.g., 09:30)', 'elementor'),
            'label_block' => true,
        ]);
        $this->add_control('end_date', [
            'label' => __('End Date', 'elementor'),
            'type' => \Elementor\Controls_Manager::DATE_TIME,
            'picker_options' => [ 'enableTime' => false ],
        ]);
        $this->add_control('end_time', [
            'label' => __('End Time', 'elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'HH:MM',
            'description' => __('Enter the end time (e.g., 18:00)', 'elementor'),
            'label_block' => true,
        ]);
        $this->add_control('location', [
            'label' => __('Location', 'elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'label_block' => true,
        ]);
        $this->add_control('description', [
            'label' => __('Description', 'elementor'),
            'type' => \Elementor\Controls_Manager::WYSIWYG,
            'default' => '',
        ]);
        $this->add_control('image', [
            'label' => __('Image', 'elementor'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [ 'url' => '' ],
        ]);
        $this->add_control('image_position', [
            'label' => __('Image Position', 'elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'left' => __('Left', 'elementor'),
                'right' => __('Right', 'elementor'),
            ],
            'default' => 'left',
            'label_block' => true,
        ]);
        $this->add_control('external_url', [
            'label' => __('External URL', 'elementor'),
            'type' => \Elementor\Controls_Manager::URL,
            'placeholder' => 'https://',
        ]);
        $this->add_control('external_url_title', [
            'label' => __('External URL Title', 'elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
            'label_block' => true,
        ]);
        $this->end_controls_section();

        // Style controls
        // Title
        $this->start_controls_section('style_title', [
            'label' => __('Title', 'elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('title_color', [
            'label' => __('Color', 'elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-title' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-title',
        ]);
        $this->end_controls_section();

        // Pre-title
        $this->start_controls_section('style_pretitle', [
            'label' => __('Pre-title', 'elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('pretitle_color', [
            'label' => __('Color', 'elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-pretitle' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'pretitle_typography',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-pretitle',
        ]);
        $this->end_controls_section();

        // Date
        $this->start_controls_section('style_date', [
            'label' => __('Date', 'elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('date_color', [
            'label' => __('Color', 'elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-date' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'date_typography',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-date',
        ]);
        $this->end_controls_section();

        // Location
        $this->start_controls_section('style_location', [
            'label' => __('Location', 'elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('location_color', [
            'label' => __('Color', 'elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-location' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'location_typography',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-location',
        ]);
        $this->end_controls_section();

        // Description
        $this->start_controls_section('style_description', [
            'label' => __('Description', 'elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('description_color', [
            'label' => __('Color', 'elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-description' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'description_typography',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-description',
        ]);
        $this->end_controls_section();

        // Link
        $this->start_controls_section('style_link', [
            'label' => __('Link', 'elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('link_color', [
            'label' => __('Color', 'elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-link a' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_control('link_bg_color', [
            'label' => __('Background Color', 'elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-link a' => 'background-color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'link_typography',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-link a',
        ]);
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'link_border',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-link a',
        ]);
        $this->add_control('link_border_radius', [
            'label' => __('Border Radius', 'elementor'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => [ 'min' => 0, 'max' => 50 ],
                '%' => [ 'min' => 0, 'max' => 50 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-link a' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'link_box_shadow',
            'selector' => '{{WRAPPER}} .cbk-drolma-event-widget-link a',
        ]);
        $this->add_control('link_padding', [
            'label' => __('Padding', 'elementor'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .cbk-drolma-event-widget-link a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $pre_title = $settings['pre_title'];
        $title = $settings['title'];
        if($title=="")
        {
            $title="Default title";
        }
        $start_date = $settings['start_date'];
        $start_time = $settings['start_time'];
        $end_date = $settings['end_date'];
        $end_time = $settings['end_time'];
        $location = $settings['location'];
        if($location=="")
        {
            $location="Default location";
        }
        $description = $settings['description'];
        if($description=="")
        {
            $description="Default description";
        }
        $image_url = $settings['image']['url'];
        $external_url = $settings['external_url']['url'];
        $external_url_title = $settings['external_url_title'];
        if($external_url_title=="")
        {
            $external_url_title="Information et inscription";
        }
        $image_position = isset($settings['image_position']) ? $settings['image_position'] : 'left';

        // // Date/time formatting
        $date_str = '';
        if ($start_date && $end_date && $start_date !== $end_date) {
            $date_str = 'Du ' . date_i18n('l d F Y', strtotime($start_date));
            if ($start_time) $date_str .= ' à ' . date_i18n('H\hi', strtotime($start_time));
            $date_str .= ' au ' . date_i18n('l d F Y', strtotime($end_date));
            if ($end_time) $date_str .= ' à ' . date_i18n('H\hi', strtotime($end_time));
        } else {
            $date_str = 'Le ' . date_i18n('l d F Y', strtotime($start_date));
            if ($start_time && $end_time && $start_time !== $end_time) {
                $date_str .= ' de ' . date_i18n('H\hi', strtotime($start_time)) . ' à ' . date_i18n('H\hi', strtotime($end_time));
            } elseif ($start_time) {
                $date_str .= ' à ' . date_i18n('H\hi', strtotime($start_time));
            }
        }
        ?>
        <div class="cbk-drolma-event-widget" style="display: flex; flex-wrap: wrap; gap: 2em; align-items: flex-start; flex-direction: <?php echo ($image_position === 'right') ? 'row-reverse' : 'row'; ?>;">
            <div class="cbk-drolma-event-widget-image" style="flex:1 1 250px;max-width:350px;">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" style="width:100%;height:auto;object-fit:cover;" />
            </div>
            <div class="cbk-drolma-event-widget-info" style="flex:2 1 350px;min-width:250px;display:flex;flex-direction:column;align-items:center;text-align:center;">
                <?php if ($pre_title): ?><h3 class="cbk-drolma-event-widget-pretitle" ><?php echo esc_html($pre_title); ?></h3><?php endif; ?>
                <h3 class="cbk-drolma-event-widget-title" style="margin:0 0 0.5em 0;"> <?php echo nl2br(esc_html($title)); ?> </h3>
                <h4 class="cbk-drolma-event-widget-date" style="margin-bottom:0.5em;"><strong><?php echo esc_html($date_str); ?></strong></h4>
                <h4 class="cbk-drolma-event-widget-location" style="margin-bottom:0.5em;"><?php echo esc_html($location); ?></h4>
                <div class="cbk-drolma-event-widget-description" style="margin-bottom:0.5em; text-align: left;"> <?php echo $description; ?> </div>
                <?php if ($external_url): ?>
                    <div class="cbk-drolma-event-widget-link"><a href="<?php echo esc_url($external_url); ?>" target="_blank" rel="noopener" class="elementor-button elementor-size-md"><i aria-hidden="true" class="far fa-calendar-check" style="margin-right:10px;"></i><span><?php echo esc_html($external_url_title); ?></span></a></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
