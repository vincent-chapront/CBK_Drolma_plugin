<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBK_Drolma_Passes_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'cbk_drolma_passes'; }
    public function get_title() { return 'CBK Drolma Passes'; }
    public function get_icon() { return 'eicon-link'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {
        // Content Tab: Only 'days_before', rest is a copy of Elementor Button except Text/Link
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor' ),
            ]
        );
        $this->add_control(
            'days_before',
            [
                'label' => __( 'Days before month', 'elementor' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 60,
                'step' => 1,
                'default' => 10,
            ]
        );
        $this->add_control(
            'button_size',
            [
                'label' => __( 'Size', 'elementor' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'md',
                'options' => [
                    'xs' => __( 'Extra Small', 'elementor' ),
                    'sm' => __( 'Small', 'elementor' ),
                    'md' => __( 'Medium', 'elementor' ),
                    'lg' => __( 'Large', 'elementor' ),
                    'xl' => __( 'Extra Large', 'elementor' ),
                ],
            ]
        );
        $this->add_control(
            'button_icon',
            [
                'label' => __( 'Icon', 'elementor' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-video',
                    'library' => 'fa-solid',
                ],
            ]
        );
        $this->add_control(
            'icon_align',
            [
                'label' => __( 'Icon Position', 'elementor' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __( 'Before', 'elementor' ),
                    'right' => __( 'After', 'elementor' ),
                ],
                'condition' => [
                    'button_icon[value]!' => '',
                ],
            ]
        );
        $this->add_control(
            'icon_spacing',
            [
                'label' => __( 'Icon Spacing', 'elementor' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-pass-icon' => 'margin-{{icon_align}}: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'button_icon[value]!' => '',
                ],
            ]
        );
        $this->end_controls_section();

        // Style Tab: Copy of Elementor Button style
        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Button', 'elementor' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'alignment',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-passes' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_text_color',
            [
                'label' => __( 'Text Color', 'elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-pass' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_bg_color',
            [
                'label' => __( 'Background Color', 'elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-pass' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_text_color_hover',
            [
                'label' => __( 'Text Color (Hover)', 'elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-pass:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_bg_color_hover',
            [
                'label' => __( 'Background Color (Hover)', 'elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-pass:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .cbk-drolma-pass',
            ]
        );
        $this->add_responsive_control(
            'margin',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-pass' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .cbk-drolma-pass',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .cbk-drolma-pass',
            ]
        );
        $this->add_responsive_control(
            'border_radius',
            [
                'label' => __( 'Border Radius', 'elementor' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .cbk-drolma-pass' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .cbk-drolma-pass',
            ]
        );
        $this->end_controls_section();
    }

    private function render_pass($month,$year,$url)
    {
        $months = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];
            
?>
        <a class="cbk-drolma-pass" href="<?php echo esc_url($url); ?>" style="display: inline-block;">
            <span class="cbk-drolma-pass-icon">
                <i aria-hidden="true" class="fas fa-video"></i>
            </span>
            <span class="cbk-drolma-pass-text">Pass méditation <?php echo $months[intval($month)];?></span>
        </a>
<?php
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $days_before = isset($settings['days_before']) ? intval($settings['days_before']) : 10;
        $today = current_time('Y-m-d');

        $args = [
            'post_type' => 'cbk_drolma_pass',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'meta_value',
            'meta_key' => '_cbk_drolma_pass_year',
            'order' => 'ASC',
        ];
        $passes = get_posts($args);

        echo '<div class="cbk-drolma-passes" style="display: flex; flex-direction: column; gap: 1em; align-items: center;">';
        echo '<style>.cbk-drolma-pass { transition: color 0.2s, background-color 0.2s; }</style>';
        foreach ($passes as $pass) {
            $month = get_post_meta($pass->ID, '_cbk_drolma_pass_month', true);
            $year = get_post_meta($pass->ID, '_cbk_drolma_pass_year', true);
            $url = get_post_meta($pass->ID, '_cbk_drolma_pass_external_url', true);
            if (!$month || !$year || !$url) continue;

            $validity_date = sprintf('%04d-%02d-01', $year, $month);
            $show_date = date('Y-m-d', strtotime($validity_date . ' -' . $days_before . ' days'));

            if ($today >= $show_date) {
                $this->render_pass($month,$year,$url);
            }
        }
        echo '</div>';
    }
}
