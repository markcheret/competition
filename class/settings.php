<?php
/**
 * Includes the Settings class to handle all Plugin settings.
 *
 * @filesource
 * @author Stefan Herndler
 * @since 1.5.0 14.09.14 10:43
 */


/**
 * The class loads all Settings from each WordPress Settings container.
 * It a Setting is not defined yet the default value will be used.
 * Each Setting will be validated and sanitized when loaded from the container.
 *
 * @author Stefan Herndler
 * @since 1.5.0
 */
class MCI_Competition_Settings {

    /**
     * Settings key to store all tags.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @var string
     */
    const C_STR_TAGS = "tags";

	/**
	 * Stores a singleton reference of this class.
	 *
	 * @author Stefan Herndler
	 * @since  1.5.0
	 * @var MCI_Competition_Settings
	 */
	private static $a_obj_Instance = null;

	/**
	 * Contains all Settings Container names.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @var array
	 */
	private $a_arr_Container = array("competition_plugin_settings_container");

	/**
	 * Contains all Default Settings for each Settings Container.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @var array
	 */
	private $a_arr_Default = array(
		"competition_plugin_settings_container" => array(
			self::C_STR_TAGS => '',
		)
	);

	/**
	 * Contains all Settings from each Settings container as soon as this class is initialized.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @var array
	 */
	private $a_arr_Settings = array();

	/**
	 * Class Constructor. Loads all Settings from each WordPress Settings container.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	private function __construct() {
		$this->loadAll();
	}

	/**
	 * Returns a singleton of this class.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @return MCI_Competition_Settings
	 */
	public static function instance() {
		// no instance defined yet, load it
		if (self::$a_obj_Instance === null) {
			self::$a_obj_Instance = new self();
		}
		// return a singleton of this class
		return self::$a_obj_Instance;
	}

	/**
	 * Returns the name of a specified Settings Container.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @param int $p_int_Index Settings Container Array Key Index.
	 * @return string Settings Container name.
	 */
	public function getContainer($p_int_Index) {
		return $this->a_arr_Container[$p_int_Index];
	}

    /**
     * Returns the default values of a specific Settings Container.
     *
     * @author Stefan Herndler
     * @since 1.5.6
     * @param int $p_int_Index Settings Container Aray Key Index.
     * @return array
     */
    public function getDefaults($p_int_Index) {
        return $this->a_arr_Default[$this->a_arr_Container[$p_int_Index]];
    }

	/**
	 * Loads all Settings from each Settings container.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	private function loadAll() {
		// clear current settings
		$this->a_arr_Settings = array();
		for ($i = 0; $i < count($this->a_arr_Container); $i++) {
			// load settings
			$this->a_arr_Settings = array_merge($this->a_arr_Settings, $this->Load($i));
		}
	}

	/**
	 * Loads all Settings from specified Settings Container.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @param int $p_int_Index Settings Container Array Key Index.
	 * @return array Settings loaded from Container of Default Settings if Settings Container is empty (first usage).
	 */
	private function Load($p_int_Index) {
		// load all settings from container
		$l_arr_Options = get_option($this->getContainer($p_int_Index));
		// load all default settings
		$l_arr_Default = $this->a_arr_Default[$this->getContainer($p_int_Index)];

		// no settings found, set them to their default value
		if (empty($l_arr_Options)) {
			return $l_arr_Default;
		}
		// iterate through all available settings ( = default values)
		foreach($l_arr_Default as $l_str_Key => $l_str_Value) {
			// available setting not found in the container
			if (!array_key_exists($l_str_Key, $l_arr_Options)) {
				// define the setting with its default value
				$l_arr_Options[$l_str_Key] = $l_str_Value;
			}
		}
		// iterate through each setting in the container
		foreach($l_arr_Options as $l_str_Key => $l_str_Value) {
			// remove all whitespace at the beginning and end of a setting
			//$l_str_Value = trim($l_str_Value);
			// write the sanitized value back to the setting container
			$l_arr_Options[$l_str_Key] = $l_str_Value;
		}
		// return settings loaded from Container
		return $l_arr_Options;
	}

	/**
	 * Updates a whole Settings container.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @param int $p_int_Index Index of the Settings container.
	 * @param array $p_arr_newValues new Settings.
	 * @return bool
	 */
	public function saveOptions($p_int_Index, $p_arr_newValues) {
		if (update_option($this->getContainer($p_int_Index), $p_arr_newValues)) {
			$this->loadAll();
			return true;
		}
		return false;
	}

	/**
	 * Returns the value of specified Settings name.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @param string $p_str_Key Settings Array Key name.
	 * @return mixed Value of the Setting on Success or Null in Settings name is invalid.
	 */
	public function get($p_str_Key) {
		return array_key_exists($p_str_Key, $this->a_arr_Settings) ? $this->a_arr_Settings[$p_str_Key] : null;
	}

	/**
	 * Deletes each Settings Container and loads the default values for each Settings Container.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public function ClearAll() {
		// iterate through each Settings Container
		for ($i = 0; $i < count($this->a_arr_Container); $i++) {
			// delete the settings container
			delete_option($this->getContainer($i));
		}
		// set settings back to the default values
		$this->a_arr_Settings = $this->a_arr_Default;
	}

	/**
	 * Register all Settings Container for the Plugin Settings Page in the Dashboard.
	 * Settings Container Label will be the same as the Settings Container Name.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public function RegisterSettings() {
		// register all settings
		for ($i = 0; $i < count($this->a_arr_Container); $i++) {
			register_setting($this->getContainer($i), $this->getContainer($i));
		}
	}
}