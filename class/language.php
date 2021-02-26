<?php
/**
 * 
 * @filesource
 * @author Stefan Herndler
 * @since 1.5.0 14.09.14 17:47
 */

/**
 *
 * @author Stefan Herndler
 * @since 1.0.1
 */
class MCI_Competition_Language {

	/**
	 * Register WordPress Hook.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public static function registerHooks() {
		add_action('plugins_loaded', array("MCI_Competition_Language", "loadTextDomain"));
	}

	/**
	 * Loads the text domain for current WordPress language if exists. Otherwise fallback "en_GB" will be loaded.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public static function loadTextDomain() {
		// language file with localization exists
		if (self::load(apply_filters('plugin_locale', get_locale()))) {
			return;
		}
		// fallback to english
		self::load("en_GB");
	}

	/**
	 * Loads a specific text domain.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.1
	 * @param string $p_str_LanguageCode Language Code to load a specific text domain.
	 * @return bool
	 */
	private static function load($p_str_LanguageCode) {
		return load_textdomain(MCI_Competition_Config::C_STR_PLUGIN_NAME,
			dirname(__FILE__) . "/../languages/" . $p_str_LanguageCode . '.mo');
	}
}