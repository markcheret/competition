<?php
/**
 * 
 * @filesource
 * @author Stefan Herndler
 * @since 1.0.1 11.10.14 20:24
 */

/**
 * 
 * @author Stefan Herndler
 * @since 1.0.1
 */
class MCI_Competition_Stats {

    /**
     * Draw statistic of plugins.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @var string
     */
    const C_STR_PLUGINS = "plugins";

    /**
     * Draw statistic of themes.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @var string
     */
    const C_STR_THEMES = "themes";


    /**
     * Contains all Plugin/Theme names and permalink loaded from RSS feed.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @var array
     */
    private $a_arr_Items = array();

    /**
     * Register AJAX hooks to load statistic data async.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     */
    public static function registerAJAX() {
        add_action("wp_ajax_Competition_loadLineSeries", array("MCI_Competition_Stats", "loadLineSeries"));
        add_action("wp_ajax_nopriv_Competition_loadLineSeries", array("MCI_Competition_Stats", "loadLineSeries"));

        add_action("wp_ajax_Competition_loadPieSeries", array("MCI_Competition_Stats", "loadPieSeries"));
        add_action("wp_ajax_nopriv_Competition_loadPieSeries", array("MCI_Competition_Stats", "loadPieSeries"));
    }

    /**
     * Load the RSS feed for a specific tag.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @param string $p_str_Tag Tag to load RSS feed.
     * @param string $p_str_Type Type of the Tag (Plugin or Theme).
     * @param int $p_int_Limit (Maximum number of RSS feeds loaded, default: 0 = unlimited).
     * @return bool
     */
    public function load($p_str_Tag, $p_str_Type, $p_int_Limit = 0) {
        if ($p_str_Type != self::C_STR_PLUGINS && $p_str_Type != self::C_STR_THEMES) {
            return false;
        }
        // require WordPress function for RSS feeds
        require_once(ABSPATH . WPINC . '/feed.php');

        // Get a SimplePie feed object from the specified feed source.
        $l_obj_RSS = fetch_feed("http://wordpress.org/" . $p_str_Type . "/rss/tags/" . $p_str_Tag);
        // error fetching RSS feed
        if (is_wp_error($l_obj_RSS)) {
            return false;
        }
        // get all items from RSS feed
        $l_arr_RSSItems = $l_obj_RSS->get_items(0, $l_obj_RSS->get_item_quantity($p_int_Limit));
        // zero items found in RSS feed
        if (empty($l_arr_RSSItems)) {
            return false;
        }

        // iterate through each item from RSS feed
        /** @var SimplePie $l_obj_Item */
        foreach($l_arr_RSSItems as $l_obj_Item) {
            // split item url to get the internal name ( = last part of the url)
            $l_arr_Url = explode("/", $l_obj_Item->get_permalink());
            if (empty($l_arr_Url)) {
                continue;
            }
            $this->appendItem($l_arr_Url[count($l_arr_Url) - 2], $p_str_Type);
        }
        // check if at least 1 item ( = plugin / theme) loaded from RSS feed
        if (empty($this->a_arr_Items)) {
            return false;
        }
        return true;
    }

    /**
     * Appends specific plugin/themes to the item list.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @param string $p_str_Tags Unique plugin/theme names.
     * @param string $p_str_Type Type of the Tag (Plugin or Theme).
     * @return bool
     */
    public function appendSpecificItems($p_str_Tags, $p_str_Type) {
        if ($p_str_Type != self::C_STR_PLUGINS && $p_str_Type != self::C_STR_THEMES) {
            return false;
        }
        // iterate through each specific plugin/theme name
        foreach(explode(",", $p_str_Tags) as $l_str_Tag) {
            $this->appendItem(trim($l_str_Tag), $p_str_Type);
        }
        // check if at least 1 item ( = plugin / theme) loaded from RSS feed
        if (empty($this->a_arr_Items)) {
            return false;
        }
        return true;
    }

    /**
     * Appends a unique plugin/theme to the list of items.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @param string $p_str_Name Unique plugin/theme name.
     * @param string $p_str_Type Type (plugin or theme).
     * @return bool
     */
    private function appendItem($p_str_Name, $p_str_Type) {
        if (empty($p_str_Name)) {
            return false;
        }
        if ($p_str_Type != self::C_STR_PLUGINS && $p_str_Type != self::C_STR_THEMES) {
            return false;
        }
        // add item (key = unique name from permalink, value = type)
        $this->a_arr_Items[$p_str_Name] = $p_str_Type;
        return true;
    }

    /**
     * Returns the highchart (line) for the downloads per day.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @return string
     */
    public function downloads_per_day() {
        // check if at least 1 item ( = plugin / theme) loaded from RSS feed
        if (empty($this->a_arr_Items)) {
            return "";
        }
        $l_int_UniqueID = date("YmdHis") . rand(100000, 999999);

        // load and display the charts
        $l_obj_TemplateChart = new MCI_Competition_Template(MCI_Competition_Template::C_STR_CHARTS, "line");
        $l_obj_TemplateChart->replace(array(
                "id" => $l_int_UniqueID,
                "title" => __("Downloads", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "sub-title" => __("per day", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "label-y-axis" => __("Downloads", MCI_Competition_Config::C_STR_PLUGIN_NAME),
                "suffix" => __("downloads", MCI_Competition_Config::C_STR_PLUGIN_NAME)
        ));

        $l_str_Scripts = "";
        $l_obj_TemplateLoader = new MCI_Competition_Template(MCI_Competition_Template::C_STR_CHARTS, "line-load");
        // iterate through each item loaded from RSS feed
        foreach($this->a_arr_Items as $l_str_Name => $l_str_Type) {
            $l_obj_TemplateLoader->replace(array(
                "id" => $l_int_UniqueID,
                "name" => $l_str_Name,
                "type" => $l_str_Type
            ));
            $l_str_Scripts .= $l_obj_TemplateLoader->getContent();
            $l_obj_TemplateLoader->reload();
        }

        return $l_obj_TemplateChart->getContent() . $l_str_Scripts;
    }

    /**
     * Returns the highchart (line) for total downloads.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     * @param string|null $p_str_Own Own plugin/theme
     * @return string
     */
    public function downloads_total($p_str_Own = null) {
        // check if at least 1 item ( = plugin / theme) loaded from RSS feed
        if (empty($this->a_arr_Items)) {
            return "";
        }
        $l_int_UniqueID = date("YmdHis") . rand(100000, 999999);

        // load and display the charts
        $l_obj_TemplateChart = new MCI_Competition_Template(MCI_Competition_Template::C_STR_CHARTS, "pie");
        $l_obj_TemplateChart->replace(array(
            "id" => $l_int_UniqueID,
            "title" => __("Total downloads", MCI_Competition_Config::C_STR_PLUGIN_NAME)
        ));

        $l_str_Scripts = "";
        $l_obj_TemplateLoader = new MCI_Competition_Template(MCI_Competition_Template::C_STR_CHARTS, "pie-load");
        // iterate through each item loaded from RSS feed
        foreach($this->a_arr_Items as $l_str_Name => $l_str_Type) {
            $l_obj_TemplateLoader->replace(array(
                "id" => $l_int_UniqueID,
                "name" => $l_str_Name,
                "type" => $l_str_Type,
                "pre-select" => !empty($p_str_Own) && $p_str_Own == $l_str_Name ? "select" : "default"
            ));
            $l_str_Scripts .= $l_obj_TemplateLoader->getContent();
            $l_obj_TemplateLoader->reload();
        }

        return $l_obj_TemplateChart->getContent() . $l_str_Scripts;
    }

    /**
     * Collects download information about a specific plugin/theme and outputs them.
     * Returns information as json array and stops the script.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     */
    public static function loadLineSeries() {
        $l_str_Name = array_key_exists("name", $_POST) ? $_POST["name"] : "";
        $l_str_Type = array_key_exists("type", $_POST) ? $_POST["type"] : "";

        if (empty($l_str_Name) || empty($l_str_Type)) {
            echo json_encode(array("name" => $l_str_Name, "type" => $l_str_Type));
            exit;
        }
        // build URL string to receive meta information
        $l_str_Url = "";
        switch($l_str_Type) {
            case self::C_STR_PLUGINS:
                $l_str_Url = "http://api.wordpress.org/stats/plugin/1.0/downloads.php?slug=" . $l_str_Name;
                break;
            case self::C_STR_THEMES:
                $l_str_Url = "http://api.wordpress.org/stats/theme/1.0/downloads.php?slug=" . $l_str_Name;
                break;
        }
        // request URL and collect data
        $l_arr_Response = wp_remote_get($l_str_Url);
        // check if response is valid
        if (is_wp_error($l_arr_Response)) {
            /** @var WP_Error $l_arr_Response */
            echo json_encode(array("code" => $l_arr_Response->get_error_code(), "url" => $l_str_Url));
            exit;
        }
        // echo download data as json string
        echo json_encode(json_decode($l_arr_Response["body"], true));
        exit;
    }

    /**
     * Collects download information about a specific plugin/theme and outputs them.
     * Returns information as json array and stops the script.
     *
     * @author Stefan Herndler
     * @since 1.0.1
     */
    public static function loadPieSeries() {
        $l_str_Name = array_key_exists("name", $_POST) ? $_POST["name"] : "";
        $l_str_Type = array_key_exists("type", $_POST) ? $_POST["type"] : "";

        if (empty($l_str_Name) || empty($l_str_Type)) {
            echo json_encode(array("name" => $l_str_Name, "type" => $l_str_Type));
            exit;
        }
        // build URL string to receive meta information
        $l_str_Url = "";
        switch($l_str_Type) {
            case self::C_STR_PLUGINS:
                $l_str_Url = "http://api.wordpress.org/plugins/info/1.0/" . $l_str_Name . ".json";
                break;
            case self::C_STR_THEMES:
                $l_str_Url = "http://api.wordpress.org/themes/info/1.0/" . $l_str_Name . ".json";
                break;
        }
        // request URL and collect data
        $l_arr_Response = wp_remote_get($l_str_Url);
        // check if response is valid
        if (is_wp_error($l_arr_Response)) {
            /** @var WP_Error $l_arr_Response */
            echo json_encode(array("code" => $l_arr_Response->get_error_code(), "url" => $l_str_Url));
            exit;
        }
        // get Plugin/Theme properties
        $l_arr_Plugin = json_decode($l_arr_Response["body"], true);
        // check if plugin loaded
        if (empty($l_arr_Plugin) || !array_key_exists("downloaded", $l_arr_Plugin)) {
            echo json_encode($l_arr_Plugin);
            exit;
        }
        // echo download data as json string
        echo json_encode(array("downloads" => intval($l_arr_Plugin["downloaded"])));
        exit;
    }
} 