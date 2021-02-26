<?php
/**
 * Includes the Convert Class.
 *
 * @filesource
 * @author Stefan Herndler
 * @since 1.5.0 12.09.14 10:56
 */


/**
 * Converts data types and Footnotes specific values.
 *
 * @author Stefan Herndler
 * @since 1.5.0
 */
class MCI_Competition_Convert {

	/**
	 * Converts a string depending on its value to a boolean.
	 *
	 * @author Stefan Herndler
	 * @since 1.0-beta
	 * @param string $p_str_Value String to be converted to boolean.
	 * @return bool Boolean representing the string.
	 */
	public static function toBool($p_str_Value) {
		// convert string to lower-case to make it easier
		$p_str_Value = strtolower($p_str_Value);
		// check if string seems to contain a "true" value
		switch ($p_str_Value) {
			case "checked":
			case "yes":
			case "true":
			case "on":
			case "1":
				return true;
		}
		// nothing found that says "true", so we return false
		return false;
	}

	/**
	 * Displays a Variable.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @param mixed $p_mixed_Value
	 */
	public static function debug($p_mixed_Value) {
		if (empty($p_mixed_Value)) {
			var_dump($p_mixed_Value);

		} else if (is_array($p_mixed_Value)) {
			printf("<pre>");
			print_r($p_mixed_Value);
			printf("</pre>");

		} else if (is_object($p_mixed_Value)) {
			printf("<pre>");
			print_r($p_mixed_Value);
			printf("</pre>");

		} else if (is_numeric($p_mixed_Value) || is_int($p_mixed_Value)) {
			var_dump($p_mixed_Value);

		} else if (is_date($p_mixed_Value)) {
			var_dump($p_mixed_Value);

		} else {
			var_dump($p_mixed_Value);
		}
		echo "<br/>";
	}
}