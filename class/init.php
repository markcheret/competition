<?php
/**
 * Includes the main Class of the Plugin.
 *
 * @filesource
 * @author Stefan Herndler
 * @since 1.5.0 12.09.14 10:56
 */


/**
 * Entry point of the Plugin. Loads the Dashboard and executes the Task.
 *
 * @author Stefan Herndler
 * @since 1.0.1
 */
class MCI_Competition {

	/**
	 * Reference to the Plugin Task object.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @var null|MCI_Competition_Task
	 */
	public $a_obj_Task = null;

	/**
	 * Executes the Plugin.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public function run() {
		// register language
		MCI_Competition_Language::registerHooks();
		// register general hooks
		MCI_Competition_Hooks::registerHooks();
        // register AJAX hooks
        MCI_Competition_Stats::registerAJAX();
		// initialize the Plugin Dashboard
		$this->initializeDashboard();
		// initialize the Plugin Task
		$this->initializeTask();

        // Register all Public Stylesheets
        add_action('init', array($this, 'registerPublicStyling'));
        // Register all Public Scripts
        add_action('init', array($this, 'registerPublicScripts'));
        // Enqueue all Public Stylesheets
        add_action('wp_enqueue_scripts', array($this, 'registerPublicStyling'));
        // Enqueue all Public Scripts
        add_action('wp_enqueue_scripts', array($this, 'registerPublicScripts'));
	}

	/**
	 * Initializes the Dashboard of the Plugin and loads them.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	private function initializeDashboard() {
		new MCI_Competition_Layout_Init();
	}

	/**
	 * Initializes the Plugin Task and registers the Task hooks.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	private function initializeTask() {
		$this->a_obj_Task = new MCI_Competition_Task();
		$this->a_obj_Task->registerHooks();
	}

    /**
     * Registers and enqueue scripts to the public pages.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     */
    public function registerPublicScripts() {
        // add the jQuery plugin (already registered by WordPress)
        wp_enqueue_script('jquery');
        // add 'highchart' library to the current page
        wp_enqueue_script('competition-highchart', plugins_url('../js/highchart/highcharts.js', __FILE__));
        wp_enqueue_script('competition-highchart-3d', plugins_url('../js/highchart/highcharts-3d.js', __FILE__));
        wp_enqueue_script('competition-highchart-exporting', plugins_url('../js/highchart/modules/exporting.js', __FILE__));
        // add script to load series async
        wp_enqueue_script('competition-async-loader', plugins_url('../js/load-series.js', __FILE__));
    }
    /**
     * Registers and enqueue stylesheets to the public pages.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     */
    public function registerPublicStyling() {
        wp_register_style('mci_competition_css_public', plugins_url('../css/public.css', __FILE__));
        wp_enqueue_style('mci_competition_css_public');
    }
}