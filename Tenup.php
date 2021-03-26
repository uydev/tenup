<?php
/**
 * @package tenup
 */
include 'PrimaryCategoryFilter.php';
/**
 * Plugin Name: TenUp
 * Plugin URI: https://github.com/uydev/tenup
 * Description: Custom Plugin for Tenup Test
 * Version: 1.0.0
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

if (!defined('ABSPATH')) {
    die;
}

/* Fire our meta box setup function on the post editor screen. */
add_action('load-post.php', 'tenup_primary_category_meta_boxes_setup');
add_action('load-post-new.php', 'tenup_primary_category_meta_boxes_setup');

/* Meta box setup function. */
function tenup_primary_category_meta_boxes_setup()
{

    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action('add_meta_boxes', 'tenup_add_primary_category_meta_boxes');

    /* Save post meta on the 'save_post' hook. */
    add_action('save_post', 'tenup_primary_category_meta', 10, 2);
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function tenup_add_primary_category_meta_boxes()
{
    add_meta_box(
        'tenup-primary-category',
        esc_html__('Primary Category', 'example'),
        'tenup_primary_category_meta_box',
        'post',
        'side',
        'default'
    );
}

function tenup_primary_category_meta_box($post)
{ ?>
    <?php wp_nonce_field(basename(__FILE__), 'tenup_primary_category_nonce'); ?>
    <input class="widefat" type="text" name="tenup_primary_category" id="tenup_primary_category"
           value="<?php echo esc_attr(get_post_meta($post->ID, 'tenup_primary_category', true)); ?>" size="30"/>
    <?php
}

/* Save the meta box’s post metadata. */
function tenup_primary_category_meta($post_id, $post)
{

    /* Verify the nonce before proceeding. */
    if (!isset($_POST['tenup_primary_category_nonce']) || !wp_verify_nonce($_POST['tenup_primary_category_nonce'], basename(__FILE__)))
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object($post->post_type);

    /* Check if the current user has permission to edit the post. */
    if (!current_user_can($post_type->cap->edit_post, $post_id))
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value = (isset($_POST['tenup_primary_category']) ? sanitize_html_class($_POST['tenup_primary_category']) : ’);

    /* Get the meta key. */
    $meta_key = 'tenup_primary_category';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta($post_id, $meta_key, true);


    /* If a new meta value was added and there was no previous value, add it. */
    if ($new_meta_value && ’ == $meta_value)
        add_post_meta($post_id, $meta_key, $new_meta_value, true);

    /* If the new meta value does not match the old value, update it. */
    elseif ($new_meta_value && $new_meta_value != $meta_value)
        update_post_meta($post_id, $meta_key, $new_meta_value);

    /* If there is no new meta value but an old value exists, delete it. */
    elseif (’ == $new_meta_value && $meta_value)
        delete_post_meta($post_id, $meta_key, $meta_value);
}

//Initialize Filter
new PrimaryCategoryFilter();
