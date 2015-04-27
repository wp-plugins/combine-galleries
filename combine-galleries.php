<?php

/*
  Plugin Name: Combine Galleries
  Plugin URI:
  Description: Automatically build a single gallery page from all your WordPress post galleries using a simple shortcode. Great for creating a main gallery page.
  Version: 1.0.2
  Author: John Upchurch (jackfilms)
  Author URI: http://boxsmash.com
  License: GPLv3 (http://www.gnu.org/licenses/gpl.html)
 */

function combine_galleries_activate($atts) {
    /*
     * Activate plugin...
     */

// Nothing to do.
}

function combine_galleries_deactivate() {
    /*
     * Deactivate plugin...
     */

// Nothing to do.
}

function combine_galleries_handler($atts) {

    global $wpdb;
    global $post;
    global $_wp_additional_image_sizes;

    $result = "";

    extract(
            shortcode_atts(
                    array(
        'link' => '',
        'size' => '',
        'columns' => '',
        'type' => 'post',
        'category' => '',
        'category_join' => 'and',
        'tag' => '',
        'tag_join' => 'or',
        'taxonomy_join' => 'and',
        'orderby' => 'post_date',
        'order' => 'DESC',
        'limitposts' => '0',
        'limitpostimages' => '0',
        'itemtag' => '',
        'icontag' => '',
        'captiontag' => '',
        'debug' => "false",
                    ), $atts, 'combine-galleries'
            )
    );

    static $query_gallery_id = 0;
    $query_gallery_id++;

    $delimiter = ',';
    $other_delimiters = array(
        ".",
        "|",
        ":"
    );

    $link = trim($link);
    $size = trim($size);
    $columns = trim($columns);
    $type = array_map('trim', explode($delimiter, str_replace($other_delimiters, $delimiter, strtolower($type))));
    $category = array_map('trim', explode($delimiter, str_replace($other_delimiters, $delimiter, strtolower($category))));
    $tag = array_map('trim', explode($delimiter, str_replace($other_delimiters, $delimiter, strtolower($tag))));

    $orderby = strtolower(trim($orderby));
    if (empty($orderby) or ! in_array($orderby, array("id", "post_author", "post_date", "post_title", "comment_count"))) {
        $orderby = "post_date";
    }

    $order = strtolower(trim($order));
    if (empty($order) or ! in_array($order, array("asc", "desc"))) {
        $order = "DESC";
    }

    $limitposts = trim($limitposts);
    $limitpostimages = trim($limitpostimages);

    $sql = "SELECT $wpdb->posts.post_content FROM $wpdb->posts";

    if ($category[0] != '' or $tag[0] != '') {

        $taxonomy_join = trim(strtolower($taxonomy_join));
        if (!in_array($taxonomy_join, array("or", "and"))) {
            $taxonomy_join = "and";
        }

        $category_join = trim(strtolower($category_join));
        if (!in_array($category_join, array("or", "and"))) {
            $category_join = "and";
        }
        if ($category_join == "or") {
            $cat_count = 1;
        } else {
            $cat_count = count($category);
        }

        $tag_join = trim(strtolower($tag_join));
        if (!in_array($tag_join, array("or", "and"))) {
            $tag_join = "or";
        }
        if ($tag_join == "or") {
            $tag_count = 1;
        } else {
            $tag_count = count($tag);
        }

        $sql .= "
            JOIN (
            SELECT ID FROM (
            SELECT $wpdb->term_relationships.object_id as id, ";

        if (empty($category) or empty($category["0"])) {
            $sql .= "0 as cat_count, ";
        } else {
            $sql .= "SUM(CASE WHEN $wpdb->term_taxonomy.taxonomy = 'category' AND lower($wpdb->terms.name) IN ('" . implode("', '", $category) . "') THEN 1 ELSE 0 END) AS cat_count, ";
        }

        if (empty($tag) or empty($tag["0"])) {
            $sql .= "0 as tag_count";
        } else {
            $sql .= "SUM(CASE WHEN $wpdb->term_taxonomy.taxonomy = 'post_tag' AND lower($wpdb->terms.name) IN ('" . implode("', '", $tag) . "') THEN 1 ELSE 0 END) AS tag_count";
        }

        $sql .= "
            FROM $wpdb->term_taxonomy
            JOIN $wpdb->terms
            ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
            JOIN $wpdb->term_relationships
            ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
            GROUP BY id
            ) taxonomy
            WHERE ";

        if (empty($category) or empty($category["0"])) {
            $sql .= "(tag_count >= $tag_count))";
        } else if (empty($tag) or empty($tag["0"])) {
            $sql .= "(cat_count >= $cat_count)";
        } else {
            $sql .= "((cat_count >= $cat_count) $taxonomy_join (tag_count >= $tag_count))";
        }
        $sql .= "  ) terms
                ON terms.id = $wpdb->posts.id";
    }

    $sql .= "
                WHERE $wpdb->posts.post_type IN ('" . implode("',  '", $type) . "' )
                AND $wpdb->posts.post_status = 'publish'
                AND $wpdb->posts.post_content like '%[gallery%'
                ORDER BY $wpdb->posts.$orderby $order ";

    if (intval($limitposts) > 0) {
        $sql .= "LIMIT $limitposts";
    }

    if (strtolower($debug) == "true") {
        if (!empty($wpdb->prefix)) {
            $result .= str_replace($wpdb->prefix, "", $sql) . "<br>";
        } else {
            $result .= "$sql<br>";
        }
    }

    $ids = "";
    $pageposts = $wpdb->get_results($sql, OBJECT);
    if ($pageposts) {
        foreach ($pageposts as $post) {
            $startpos = strpos($post->post_content, "[gallery");
            if (is_numeric($startpos)) {
                $endpos = strpos(substr($post->post_content, $startpos), "]");
                if (is_numeric($endpos)) {
                    $shortcode = substr($post->post_content, $startpos, $endpos) . " dummy=\"\"]";
                    $atts = shortcode_parse_atts($shortcode);
                    if (intval($limitpostimages) > 0) {
                        $ids_array = explode(",", $atts["ids"], intval($limitpostimages) + 1);
                        if (count($ids_array) > intval($limitpostimages)) {
                            array_pop($ids_array);
                        }
                        $atts["ids"] = implode(",", $ids_array);
                    }
                    if (!empty($ids))
                        $ids .= ",";
                    $ids .= $atts["ids"];
                }
            }
        }
    }
    if (!empty($ids)) {
        $gallery_shortcode = "[gallery ";
        if (!empty($link)) {
            $gallery_shortcode .= "link=\"$link\" ";
        }
        if (!empty($size)) {
            $gallery_shortcode .= "size=\"$size\" ";
        }
        if (is_numeric($columns)) {
            $gallery_shortcode .= "columns=\"$columns\" ";
        }
        if (!empty($itemtag)) {
            $gallery_shortcode .= "itemtag=\"$itemtag\" ";
        }
        if (!empty($icontag)) {
            $gallery_shortcode .= "icontag=\"$icontag\" ";
        }
        if (!empty($captiontag)) {
            $gallery_shortcode .= "captiontag=\"$captiontag\" ";
        }

        $gallery_shortcode .= "ids = \"$ids\"]";

        if (strtolower($debug) == "true") {
            $result .= "$gallery_shortcode<br>";
        }
        $result .= do_shortcode($gallery_shortcode);
    }

    return $result;
}

register_activation_hook(__FILE__, 'combine_galleries_activate');
register_deactivation_hook(__FILE__, 'combine_galleries_deactivate');
add_shortcode('combine-galleries', 'combine_galleries_handler');
