<?php

use Elementor\Widget_Base;

defined('ABSPATH') || exit;


class kickpro_widget_breadcrumb extends Widget_Base {

	public function get_name() {
		return 'kickpro-breadcrumb';
	}


	public function get_title() {
		return __( 'Kickpro - Breadcrumb', 'kickpro' );
	}


	public function get_icon() {
		return ' eicon-chevron-double-right';
	}


	public function get_categories() {
		return array( 'basic' );
	}

	public function get_keywords() {
		return array( 'awaiken', 'breadcrumbs', 'crumbs', 'list' );
	}


    protected function is_dynamic_content(): bool {
        return false;
    }
	
	protected function register_controls() {
		$this->start_controls_section(
			'breadcrumbs_section',
			[
				'label' => __( 'Settings', 'kickpro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'breadcrumbs_control',
			[
				'label'   => __( 'This does nothing', 'kickpro' ),
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		do_action('kickpro_action_get_breadcrumb');
	}

}
