<?php
/**
 * Includes the Plugin Class to display all Settings.
 *
 * @filesource
 * @author Stefan Herndler
 * @since 1.5.0 14.09.14 14:47
 */

/**
 * Displays and handles all Settings of the Plugin.
 *
 * @author Stefan Herndler
 * @since 1.5.0
 */
class MCI_Competition_Layout_Settings extends MCI_Competition_LayoutEngine {

	/**
	 * Returns a Priority index. Lower numbers have a higher Priority.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @return int
	 */
	public function getPriority() {
		return 12;
	}

	/**
	 * Returns the unique slug of the sub page.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @return string
	 */
	protected function getSubPageSlug() {
		return "-" . MCI_Competition_Config::C_STR_PLUGIN_NAME;
	}

	/**
	 * Returns the title of the sub page.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 * @return string
	 */
	protected function getSubPageTitle() {
		return MCI_Competition_Config::C_STR_PLUGIN_PUBLIC_NAME;
	}

	/**
	 * Returns an array of all registered sections for the sub page.
	 *
	 * @author Stefan Herndler
	 * @since  1.5.0
	 * @return array
	 */
	protected function getSections() {
        $l_arr_Tabs = array();
        $l_arr_Tabs[] = $this->addSection("settings", __("Settings", MCI_Competition_Config::C_STR_PLUGIN_NAME), 0, true);
        $l_arr_Tabs[] = $this->addSection("stats", __("Stats", MCI_Competition_Config::C_STR_PLUGIN_NAME), 1, true);
        $l_arr_Tabs[] = $this->addSection("example", __("Example", MCI_Competition_Config::C_STR_PLUGIN_NAME), null, false);
		return $l_arr_Tabs;
	}

	/**
	 * Returns an array of all registered meta boxes for each section of the sub page.
	 *
	 * @author Stefan Herndler
	 * @since  1.5.0
	 * @return array
	 */
	protected function getMetaBoxes() {
        $l_arr_MetaBoxes = array();
        $l_arr_MetaBoxes[] = $this->addMetaBox("settings", "tags", __("Enter your tags here", MCI_Competition_Config::C_STR_PLUGIN_NAME), "displayTags");

        // iterate through each tag defined in settings and draw a meta box for it
        foreach(explode("\r\n", MCI_Competition_Settings::instance()->get(MCI_Competition_Settings::C_STR_TAGS)) as $l_str_Tag) {
            $l_arr_MetaBoxes[] = $this->addMetaBox("stats", "stat-" . $l_str_Tag, $l_str_Tag, "drawTag", array("tag" => $l_str_Tag));
        }

        $l_arr_MetaBoxes[] = $this->addMetaBox("example", "example", __("How to use the Plugin on public posts/pages", MCI_Competition_Config::C_STR_PLUGIN_NAME), "example");

		return $l_arr_MetaBoxes;
	}

	/**
	 * Displays a text area to input all tags.
	 *
	 * @author Stefan Herndler
	 * @since 1.5.0
	 */
	public function displayTags() {
		// load template file
		$l_obj_Template = new MCI_Competition_Template(MCI_Competition_Template::C_STR_DASHBOARD, "settings-tags");
		// replace all placeholders
		$l_obj_Template->replace(
			array(
				"label-tags" => $this->addLabel(MCI_Competition_Settings::C_STR_TAGS, __("Tags", MCI_Competition_Config::C_STR_PLUGIN_NAME)),
				"tags" => $this->addTextArea(MCI_Competition_Settings::C_STR_TAGS)
			)
		);
		// display template with replaced placeholders
		echo $l_obj_Template->getContent();
	}

    /**
     * Displays a text area to input all tags.
     *
     * @author Stefan Herndler
     * @since 1.5.0
     * @param string $p_str_Post
     * @param array $p_arr_Args
     */
    public function drawTag($p_str_Post, $p_arr_Args) {
        // read Tag from arguments
        $l_str_Tag = $p_arr_Args['args']['tag'];

        // check if Tag is set
        if (empty($l_str_Tag) || !is_string($l_str_Tag)) {
            return;
        }

        // initialize new statistic
        $l_obj_Statistic = new MCI_Competition_Stats();
        // load rss feed with no limit
        if ($l_obj_Statistic->load($l_str_Tag, MCI_Competition_Stats::C_STR_PLUGINS)) {
            // draw downloads per day
            echo $l_obj_Statistic->downloads_per_day();
            // draw total downloads
            echo $l_obj_Statistic->downloads_total();
        }
    }

    /**
     *
     * @author Stefan Herndler
     * @since 1.0.1
     */
    public function example() {
        global $g_obj_MCI_Competition;

        // define example string
        $l_str_Example_1 = MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "daily" . " tag" . MCI_Competition_Task::C_STR_SEPARATOR . "plugins-footnotes" . MCI_Competition_Task::C_STR_END;
        $l_str_Example_2 = MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "total" . " tag" . MCI_Competition_Task::C_STR_SEPARATOR . "plugins-footnotes" . " own" .MCI_Competition_Task::C_STR_SEPARATOR . "footnotes" . MCI_Competition_Task::C_STR_END;

        // load template file
        $l_obj_Template = new MCI_Competition_Template(MCI_Competition_Template::C_STR_DASHBOARD, "example");
        // replace all placeholders
        $l_obj_Template->replace(
            array(
                "label-start" => __("Start your short code with:", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "start" => MCI_Competition_Task::C_STR_START,

                "label-syntax" => __("Append settings you want to define from the following list as:", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "syntax" => __("key", MCI_Competition_Config::C_STR_PLUGIN_NAME) . MCI_Competition_Task::C_STR_SEPARATOR . __("value", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "label-separate" => __("and separate them with a", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "separate" => __("whitespace", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "stat" => __("required, defines the stat to be displayed. Possible values are:",MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "total" => __("total amount of downloads for each plugin/theme (uses pie chart)",MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "daily" => __("downloads per day for each plugin/theme (uses line chart)", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "tag" => __("tag to get the stat of similar plugins/themes. The prefix defines if its a plugin or theme tag:",MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "tag-plugin" => __("collects plugins for specified tag",MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "tag-theme" => __("collects themes for specified tag",MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "plugins" => __("collects stats of multiple plugins (separated with a comma, no whitespace!)",MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "themes" => __("collects stats of multiple themes (separated with a comma, no whitespace!)",MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "own" => __("optional, name of your own plugin/theme (available for stat=total)",MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "label-close" => __("Close the short code with:", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "close" => MCI_Competition_Task::C_STR_END,

                "label-examples" => __("List of different examples about how to use the short code:", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "example-1" => MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "daily" . " tag" . MCI_Competition_Task::C_STR_SEPARATOR . "plugins-footnotes" . MCI_Competition_Task::C_STR_END,
                "desc-example-1" => __("get the downloads per day for all plugins with the tag 'footnotes'", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "example-2" => MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "total" . " tag" . MCI_Competition_Task::C_STR_SEPARATOR . "plugins-identity" . MCI_Competition_Task::C_STR_END,
                "desc-example-2" => __("get the total amount of downloads for all plugins with the tag 'identity'", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "example-3" => MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "total" . " tag" . MCI_Competition_Task::C_STR_SEPARATOR . "themes-fizz" . MCI_Competition_Task::C_STR_END,
                "desc-example-3" => __("get the total amount of downloads for all themes with the tag 'fizz'", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "example-4" => MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "daily" . " plugins" . MCI_Competition_Task::C_STR_SEPARATOR . "footnotes,identity,google-keyword-suggest" . MCI_Competition_Task::C_STR_END,
                "desc-example-4" => __("get the downloads per day for the following plugins: 'footnotes, identity and google-keyword-suggest'", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "example-5" => MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "total" . " themes" . MCI_Competition_Task::C_STR_SEPARATOR . "fizz,flat,serene" . MCI_Competition_Task::C_STR_END,
                "desc-example-5" => __("get the total amount of downloads for the following themes: 'fizz, flat and serene'", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "example-6" => MCI_Competition_Task::C_STR_START . "stat" . MCI_Competition_Task::C_STR_SEPARATOR . "daily" . " tag" . MCI_Competition_Task::C_STR_SEPARATOR . "plugins-footnotes" . " own" .MCI_Competition_Task::C_STR_SEPARATOR . "footnotes" . MCI_Competition_Task::C_STR_END,
                "desc-example-6" => __("get the downloads per day for all plugins with the tag 'footnotes' where the plugin named 'footnotes' is marked as your own plugin", MCI_Competition_Config::C_STR_PLUGIN_NAME),

                "label-example-1" => __("Graph for the example short code:", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "example-short-code-1" => $l_str_Example_1,
                "desc-example-short-code-1" => __("get the downloads per day for all plugins with the tag 'footnotes'", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "graph-example-1" => $g_obj_MCI_Competition->a_obj_Task->exec($l_str_Example_1),

                "label-example-2" => __("Graph for the example short code:", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "example-short-code-2" => $l_str_Example_2,
                "desc-example-short-code-2" => __("get the total amount of downloads for all plugins with the tag 'footnotes' where the plugin named 'footnotes' is marked as your own plugin (slice is pre-selected)", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "graph-example-2" => $g_obj_MCI_Competition->a_obj_Task->exec($l_str_Example_2)
            )
        );
        // display template with replaced placeholders
        echo $l_obj_Template->getContent();
    }
}