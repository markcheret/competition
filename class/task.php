<?php
/**
 * Created by Stefan Herndler.
 * User: Stefan
 * Date: 15.08.14 14:03
 * Version: 1.0.0
 * Since: 0.0.1
 */


/**
 *
 * @author Stefan Herndler
 * @since 1.0.0
 */
class MCI_Competition_Task {

    /**
     * Opening tag for public stats.
     *
     * @author Stefan Herndler
     * @since 1.5.0
     * @var string
     */
    const C_STR_START = "[[competition ";

    /**
     * Separator character for public stats.
     *
     * @author Stefan Herndler
     * @since 1.5.0
     * @var string
     */
    const C_STR_SEPARATOR = "=";

    /**
     * Closing tag for public stats.
     *
     * @author Stefan Herndler
     * @since 1.5.0
     * @var string
     */
    const C_STR_END = "]]";

	/**
	 * @constructor
	 * @since 1.0.0
	 */
	public function __construct() {

	}

	/**
	 * register WordPress hooks for replacing short codes in public pages
	 * @since 1.0.0
	 */
	public function RegisterHooks() {
		// register WordPress hooks for public page content
		add_filter('the_content', array($this, "exec"));
		add_filter('the_excerpt', array($this, "exec"));
		add_filter('widget_title', array($this, "exec"));
		add_filter('widget_text', array($this, "exec"));
	}

	/**
	 * replace all short codes in specific content with the defined graph
	 * @since 1.0.0
	 * @param string $p_str_Content
	 * @return string
	 */
	public function exec($p_str_Content) {
		// initialize starting position
		$l_int_StartingPos = 0;

		// loop through content until no short code found
		do {
			// get next starting position or false if not found
			$l_int_StartingPos = strpos($p_str_Content, self::C_STR_START, $l_int_StartingPos);
			// no starting short code found
			if ($l_int_StartingPos === false) {
				break;
			}

			// get next ending position or false if not found after the starting position
			$l_int_EndingPos = strpos($p_str_Content, self::C_STR_END, $l_int_StartingPos);
			// no ending short code found
			if ($l_int_EndingPos === false) {
				$l_int_StartingPos++;
				continue;
			}
			// get length of the short code
			$l_int_Length = $l_int_EndingPos - $l_int_StartingPos;
			// get the content inside the short code
			$l_str_ShortCode = substr($p_str_Content, $l_int_StartingPos + strlen(self::C_STR_START), $l_int_Length - strlen(self::C_STR_START));
			// get user-defined commands in short code
			$l_arr_Commands = explode(" ", $l_str_ShortCode);
			// command is invalid
			if (empty($l_arr_Commands)) {
				$l_int_StartingPos++;
				continue;
			}
			// initialize the available commands
            $l_str_Stat = null; // total, daily
			$l_str_Tag = null; // RSS feed tag, similar plugins/themes (start with: plugins- or themes-
			$l_str_Plugins = null; // different plugins
            $l_str_Themes = null; // different themes
			$l_str_Own = null; // own plugin/theme

			// iterate through each command
			foreach($l_arr_Commands as $l_str_Command) {
				// split command to get the name and value
				$l_arr_ShortCode = explode(self::C_STR_SEPARATOR, $l_str_Command);
				// check if command is okay
				if (empty($l_arr_ShortCode) || count($l_arr_ShortCode) != 2) {
					continue;
				}
				// change command value to lowercase and remove whitespace
				$l_arr_ShortCode[1] = strtolower(trim($l_arr_ShortCode[1]));
				// check command key
				switch(strtolower(trim($l_arr_ShortCode[0]))) {
					case "stat":
                        $l_str_Stat = $l_arr_ShortCode[1];
						break;
					case "tag":
                        $l_str_Tag = $l_arr_ShortCode[1];
						break;
                    case "plugins":
                        $l_str_Plugins = $l_arr_ShortCode[1];
                        break;
                    case "themes":
                        $l_str_Themes = $l_arr_ShortCode[1];
                        break;
					case "own":
                        $l_str_Own = $l_arr_ShortCode[1];
						break;
				}
			}
			// check if required commands are set or if multiple tags set
			if (empty($l_str_Stat) || (empty($l_str_Tag) && empty($l_str_Plugins) && empty($l_str_Themes)) ||
                (!empty($l_str_Tag) && !empty($l_str_Plugins)) || (!empty($l_str_Tag) && !empty($l_str_Themes)) ||
                (!empty($l_str_Plugins) && !empty($l_str_Themes))) {
				$l_int_StartingPos++;
				continue;
			}
			// initialize new statistic
			$l_obj_Statistic = new MCI_Competition_Stats();
			// check if statistic initialized
			if (empty($l_obj_Statistic)) {
				$l_int_StartingPos++;
				continue; // internal error
			}

            $l_bool_Loaded = false;
            if (!empty($l_str_Plugins)) {
                $l_bool_Loaded = $l_obj_Statistic->appendSpecificItems($l_str_Plugins, MCI_Competition_Stats::C_STR_PLUGINS);
            } else if (!empty($l_str_Themes)) {
                $l_bool_Loaded = $l_obj_Statistic->appendSpecificItems($l_str_Themes, MCI_Competition_Stats::C_STR_THEMES);
            } else if (!empty($l_str_Tag)) {
                $l_arr_Tag = explode("-", $l_str_Tag);
                if (!empty($l_arr_Tag) && count($l_arr_Tag) == 2) {
                    if (strpos($l_arr_Tag[0], "plugins") === 0) {
                        $l_bool_Loaded = $l_obj_Statistic->load($l_arr_Tag[1], MCI_Competition_Stats::C_STR_PLUGINS);
                    } else if (strpos($l_arr_Tag[0], "themes") === 0) {
                        $l_bool_Loaded = $l_obj_Statistic->load($l_arr_Tag[1], MCI_Competition_Stats::C_STR_THEMES);
                    }
                }
            }
			// try to load rss feed
			if (!$l_bool_Loaded) {
				$l_int_StartingPos++;
				continue;
			}
            // initialize graph to display
            $l_str_Graph = "";
			// switch graph to output depending on task
			switch ($l_str_Stat) {
				case "total":
					// get graph for 'downloads total'
					$l_str_Graph = $l_obj_Statistic->downloads_total($l_str_Own);
					break;
				case "daily":
					// get graph for 'downloads per day'
					$l_str_Graph = $l_obj_Statistic->downloads_per_day();
					break;
			}
			// replace short code with graph
			$p_str_Content = substr_replace($p_str_Content, $l_str_Graph, $l_int_StartingPos, $l_int_Length + strlen(self::C_STR_END));
			// move starting pos to ending pos
			$l_int_StartingPos++; // += $l_int_Length;
		} while(true);

		// return the content with replaced short codes
		return $p_str_Content;
	}
}