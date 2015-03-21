<?php

// Replace cpt by post name accronim (3 letters)
// Replace Post-Type by the required

if ( ! function_exists('cpt_post_type') ) {

// Register Custom Post Type
function cpt_post_type() {

    $labels = array(
        'name'                => _x( 'Post-Type', 'Post Type General Name', '_s' ),
        'singular_name'       => _x( 'Post-Type', 'Post Type Singular Name', '_s' ),
        'menu_name'           => __( 'Post-Types', '_s' ),
        'parent_item_colon'   => __( 'Parent Item:', '_s' ),
        'all_items'           => __( 'All Post-Types', '_s' ),
        'view_item'           => __( 'View Post-Type', '_s' ),
        'add_new_item'        => __( 'Add New Post-Type', '_s' ),
        'add_new'             => __( 'Add New', '_s' ),
        'edit_item'           => __( 'Edit Post-Type', '_s' ),
        'update_item'         => __( 'Update Post-Type', '_s' ),
        'search_items'        => __( 'Search Post-Type', '_s' ),
        'not_found'           => __( 'Not found', '_s' ),
        'not_found_in_trash'  => __( 'Not found in Trash', '_s' ),
    );
    $args = array(
        'label'               => __( 'cpt', '_s' ),
        'description'         => __( 'Custom post type description', '_s' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields' ),
        'taxonomies'          => array( '' ), // Related taxonomies
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'cpt', $args );

}

// Hook into the 'init' action
add_action( 'init', 'cpt_post_type', 0 );

}

// Add the Meta Box
function add_cpt_meta_box() {
    add_meta_box(
        'cpt_meta_box', // $id
        __('Post-Type', '_s'), // $title
        'show_cpt_meta_box', // $callback
        'cpt', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'add_cpt_meta_box');

// Field Array
$prefix = 'cpt_';
$domain_text = '_s';

// Change this array as needed

$cpt_meta_fields = array(
    array(
        'label'=> 'Text Input',
        'desc'  => 'A description for the field.',
        'id'    => $prefix.'text',
        'type'  => 'text'
    ),
    array(
        'label'=> 'Textarea',
        'desc'  => 'A description for the field.',
        'id'    => $prefix.'textarea',
        'type'  => 'textarea'
    ),
    array(
        'label'=> 'Checkbox Input',
        'desc'  => 'A description for the field.',
        'id'    => $prefix.'checkbox',
        'type'  => 'checkbox'
    ),
    array(
        'label'=> 'Select Box',
        'desc'  => 'A description for the field.',
        'id'    => $prefix.'select',
        'type'  => 'select',
        'options' => array (
            'one' => array (
                'label' => 'Option One',
                'value' => 'one'
            ),
            'two' => array (
                'label' => 'Option Two',
                'value' => 'two'
            ),
            'three' => array (
                'label' => 'Option Three',
                'value' => 'three'
            )
        )
    )
);
// The Callback
function show_cpt_meta_box() {
global $cpt_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
     
    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($cpt_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
                switch($field['type']) {
                    // text
                    case 'text':
                        echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                            <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // textarea
                    case 'textarea':
                        echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>
                            <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // checkbox
                    case 'checkbox':
                        echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','/>
                            <label for="'.$field['id'].'">'.$field['desc'].'</label>';
                    break;
                    // select
                    case 'select':
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                        foreach ($field['options'] as $option) {
                            echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                        }
                        echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table
}
// Save the Data
function save_cpt_meta($post_id) {
    global $cpt_meta_fields;
     
    // verify nonce
    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }
     
    // loop through fields and save the data
    foreach ($cpt_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'save_cpt_meta');

// REFERENCE:
// http://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-1-intro-and-basic-fields--wp-23259
// http://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-2-advanced-fields--wp-23293
// http://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-3-extra-fields--wp-23821
// http://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-4-using-the-data--wp-25200
// http://code.tutsplus.com/tutorials/creating-maintainable-wordpress-meta-boxes--cms-22189
// http://code.tutsplus.com/tutorials/creating-maintainable-wordpress-meta-boxes-refactoring--cms-22667