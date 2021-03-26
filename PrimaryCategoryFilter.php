<?php

/**
 * Author: Uner YILMAZ
 * Author URI: https://github.com/uydev/tenup
 * License: GPLv2 or later
 * Text Domain: TenUp
 */

/*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

/**
 * Class PrimaryCategoryFilter
 * This class will enable the PrimaryCategoryFilter
 * to display and function on the All Posts page
 * in admin
 */
class PrimaryCategoryFilter
{

    /**
     * PrimaryCategoryFilter constructor.
     */
    public function __construct()
    {
        add_action('init', array(&$this, 'init'));
    }

    /**
     * Initialize Plugin
     */
    public function init()
    {
        add_action('parse_query', array(&$this, 'admin_posts_filter'));
        add_action('restrict_manage_posts', array(&$this, 'admin_posts_filter_restrict_manage_posts'));
    }

    /**
     * @param $query
     */
    public function admin_posts_filter($query)
    {
        global $pagenow;
        if (is_admin() && $pagenow == 'edit.php' && isset($_GET['primary_category']) && $_GET['primary_category'] != '') {
            $query->query_vars['meta_key'] = $_GET['primary_category'];
            if (isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '')
                $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
        }
    }

    /**
     * Display dropdown of custom fields
     * and filter by primary category
     * upon selection and by inputting into value field
     */
    public function admin_posts_filter_restrict_manage_posts()
    {
        global $wpdb;
        $sql = 'SELECT DISTINCT meta_key FROM ' . $wpdb->postmeta . ' ORDER BY 1';
        $fields = $wpdb->get_results($sql, ARRAY_N);
        ?>
        <select name="primary_category">
            <option value=""><?php _e('Filter By Custom Fields', ''); ?></option>
            <?php
            $current = isset($_GET['primary_category']) ? $_GET['primary_category'] : '';
            $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE']) ? $_GET['ADMIN_FILTER_FIELD_VALUE'] : '';
            foreach ($fields as $field) {
                if (substr($field[0], 0, 1) != "_") {
                    printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $field[0],
                        $field[0] == $current ? ' selected="selected"' : '',
                        $field[0]
                    );
                }
            }
            ?>
        </select> <?php _e('Value:', ''); ?><input type="TEXT" name="ADMIN_FILTER_FIELD_VALUE"
                                                   value="<?php echo $current_v; ?>" />
        <?php
    }
}
