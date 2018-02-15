<?php
/**
 * Plugin Name: Good Ol' Wine Custom Post Type Plugin
 * Description: Custom Post Type Plugin
 * Version: 0.1
 * Author: Anariel Design
 * Author URI: http://www.anarieldesign.com
 */
add_action( 'init', 'anariel_wineproducts_post_types'); 
function anariel_wineproducts_post_types(){
	$labels=array(
		'name' => __( 'Wine Products', 'anariel' ),
		'singular_name' => __( 'Wine Products', 'anariel' )
	);

	$args=array(
		'labels' => $labels,
		'label' => __('Wine Products', 'anariel'),
		'singular_label' => __('Wine Products', 'anariel'),
		'public' => true,
		'show_ui' => true, 
		'_builtin' => false, 
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array('slug' => 'products-category'), 
		'supports' => array('title','editor','excerpt','revisions','thumbnail','comments'),
		'taxonomies' => array('wineproducts_cat', 'post_tag'),
		'menu_icon' => get_template_directory_uri('template_directory').'/images/wineicon.png'
	);

	if(function_exists('register_post_type')):
		register_post_type('wineproducts', $args);
	endif;
}



//Custom Post Type columns
add_filter("manage_edit-wineproducts_columns", "anariel_wineproducts_columns");
add_action("manage_posts_custom_column",  "anariel_wineproducts_custom_columns");
function anariel_wineproducts_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => _x("Wine Products Title", "Wine Products title column", 'anariel'),
			"author" => _x("Author", "Wine Products author column", 'anariel'),
			"wineproducts_cats" => _x("Wine Products Categories", "Wine Products categories column", 'anariel'),
			"date" => _x("Date", "Wine Products date column", 'anariel')
		);

		return $columns;
}

function anariel_wineproducts_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "author":
				the_author();
				break;
			case "wineproducts_cats":
				echo get_the_term_list( $post->ID, 'wineproducts_cat', '', ', ', '' ); 
				break;
		}
}



//Custom taxonomies
add_action('init', 'anariel_wineproducts_taxonomies', 0);

function anariel_wineproducts_taxonomies(){

	$labels = array(
		'name' => _x( 'Wine Products Categories', 'taxonomy general name', 'anariel' ),
		'singular_name' => _x( 'Wine Products Category', 'taxonomy singular name', 'anariel' ),
		'search_items' =>  __( 'Search Wine Products', 'anariel' ),
		'all_items' => __( 'All Wine Products Categories', 'anariel' ),
		'parent_item' => __( 'Parent Wine Products Category', 'anariel' ),
		'parent_item_colon' => __( 'Parent Wine Products Category:', 'anariel' ),
		'edit_item' => __( 'Edit Wine Products Category', 'anariel' ), 
		'update_item' => __( 'Update Wine Products Category', 'anariel' ),
		'add_new_item' => __( 'Add New Wine Products Category', 'anariel' ),
		'new_item_name' => __( 'New Wine Products Category Name', 'anariel' )
	); 	
	
	register_taxonomy('wineproducts_cat',array('wineproducts'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'wineproducts_categories' )

	));
	
	 // Initialize New Taxonomy Labels
	  $labels = array(
		'name' => _x( 'Wine Products Tags', 'taxonomy general name','anariel' ),
		'singular_name' => _x( 'Wine Products Tag', 'taxonomy singular name','anariel' ),
		'search_items' =>  __( 'Search Types','anariel' ),
		'all_items' => __( 'All Tags','anariel' ),
		'parent_item' => __( 'Parent Tag','anariel' ),
		'parent_item_colon' => __( 'Parent Tag:','anariel' ),
		'edit_item' => __( 'Edit Tags','anariel' ),
		'update_item' => __( 'Update Tag','anariel' ),
		'add_new_item' => __( 'Add New Tag','anariel' ),
		'new_item_name' => __( 'New Tag Name','anariel' ),
	  );
		// Custom taxonomy for Project Tags
		register_taxonomy('wineproducts_tag',array('project'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'wineproducts_tag' ),
	  ));
	  
	  	add_action('admin_init','wineproducts_meta_init');

	function wineproducts_meta_init()
	{
		// add a meta box for WordPress 'project' type
		add_meta_box('wineproducts_meta', 'Project Infos', 'wineproducts_meta_setup', 'project', 'side', 'low');

		// add a callback function to save any data a user enters in
		add_action('save_post','wineproducts_meta_save');
	}

	function wineproducts_meta_setup()
	{
		global $post;

		?>
			<div class="wineproducts_meta_control">
				<label>URL</label>
				<p>
					<input type="text" name="_url" value="<?php echo get_post_meta($post->ID,'_url',TRUE); ?>" style="width: 100%;" />
				</p>
			</div>
		<?php

		// create for validation
		echo '<input type="hidden" name="meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	}

	function wineproducts_meta_save($post_id)
	{
		// check nonce
		if (!isset($_POST['meta_noncename']) || !wp_verify_nonce($_POST['meta_noncename'], __FILE__)) {
		return $post_id;
		}

		// check capabilities
		if ('post' == $_POST['post_type']) {
		if (!current_user_can('edit_post', $post_id)) {
		return $post_id;
		}
		} elseif (!current_user_can('edit_page', $post_id)) {
		return $post_id;
		}

		// exit on autosave
		if (defined('DOING_AUTOSAVE') == DOING_AUTOSAVE) {
		return $post_id;
		}

		if(isset($_POST['_url']))
		{
			update_post_meta($post_id, '_url', $_POST['_url']);
		} else
		{
			delete_post_meta($post_id, '_url');
		}
	}
}




add_action( 'init', 'anariel_timeline_post_types'); 
function anariel_timeline_post_types(){
	$labels=array(
		'name' => __( 'Timeline', 'anariel' ),
		'singular_name' => __( 'Timeline', 'anariel' )
	);

	$args=array(
		'labels' => $labels,
		'label' => __('Timeline', 'anariel'),
		'singular_label' => __('Timeline', 'anariel'),
		'public' => true,
		'show_ui' => true, 
		'_builtin' => false, 
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array('slug' => 'timeline-category'), 
		'supports' => array('title','editor','excerpt','revisions','thumbnail','comments'),
		'taxonomies' => array('timeline_cat', 'post_tag'),
		'menu_icon' => get_template_directory_uri('template_directory').'/images/wineicon.png'
	);

	if(function_exists('register_post_type')):
		register_post_type('timeline', $args);
	endif;
}



//Custom Post Type columns
add_filter("manage_edit-timeline_columns", "anariel_timeline_columns");
add_action("manage_posts_custom_column",  "anariel_timeline_custom_columns");
function anariel_timeline_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => _x("Timeline Title", "Timeline title column", 'anariel'),
			"author" => _x("Author", "Timeline author column", 'anariel'),
			"timeline_cats" => _x("Timeline Categories", "Timeline categories column", 'anariel'),
			"date" => _x("Date", "Timeline date column", 'anariel')
		);

		return $columns;
}

function anariel_timeline_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "author":
				the_author();
				break;
			case "timeline_cats":
				echo get_the_term_list( $post->ID, 'timeline_cat', '', ', ', '' ); 
				break;
		}
}



//Custom taxonomies
add_action('init', 'anariel_timeline_taxonomies', 0);

function anariel_timeline_taxonomies(){

	$labels = array(
		'name' => _x( 'Timeline Categories', 'taxonomy general name', 'anariel' ),
		'singular_name' => _x( 'Timeline Category', 'taxonomy singular name', 'anariel' ),
		'search_items' =>  __( 'Search Timeline', 'anariel' ),
		'all_items' => __( 'All Timeline Categories', 'anariel' ),
		'parent_item' => __( 'Parent Timeline Category', 'anariel' ),
		'parent_item_colon' => __( 'Parent Timeline Category:', 'anariel' ),
		'edit_item' => __( 'Edit Timeline Category', 'anariel' ), 
		'update_item' => __( 'Update Timeline Category', 'anariel' ),
		'add_new_item' => __( 'Add New Timeline Category', 'anariel' ),
		'new_item_name' => __( 'New Timeline Category Name', 'anariel' )
	); 	
	
	register_taxonomy('timeline_cat',array('timeline'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'timeline_categories' )

	));
	
	 // Initialize New Taxonomy Labels
	  $labels = array(
		'name' => _x( 'Timeline Tags', 'taxonomy general name','anariel' ),
		'singular_name' => _x( 'Timeline Tag', 'taxonomy singular name','anariel' ),
		'search_items' =>  __( 'Search Types','anariel' ),
		'all_items' => __( 'All Tags','anariel' ),
		'parent_item' => __( 'Parent Tag','anariel' ),
		'parent_item_colon' => __( 'Parent Tag:','anariel' ),
		'edit_item' => __( 'Edit Tags','anariel' ),
		'update_item' => __( 'Update Tag','anariel' ),
		'add_new_item' => __( 'Add New Tag','anariel' ),
		'new_item_name' => __( 'New Tag Name','anariel' ),
	  );
		// Custom taxonomy for Project Tags
		register_taxonomy('timeline_tag',array('project'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'timeline_tag' ),
	  ));
	  
	  	add_action('admin_init','timeline_meta_init');

	function timeline_meta_init()
	{
		// add a meta box for WordPress 'project' type
		add_meta_box('timeline_meta', 'Project Infos', 'timeline_meta_setup', 'project', 'side', 'low');

		// add a callback function to save any data a user enters in
		add_action('save_post','timeline_meta_save');
	}

	function timeline_meta_setup()
	{
		global $post;

		?>
			<div class="timeline_meta_control">
				<label>URL</label>
				<p>
					<input type="text" name="_url" value="<?php echo get_post_meta($post->ID,'_url',TRUE); ?>" style="width: 100%;" />
				</p>
			</div>
		<?php

		// create for validation
		echo '<input type="hidden" name="meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	}

	function timeline_meta_save($post_id)
	{
		// check nonce
		if (!isset($_POST['meta_noncename']) || !wp_verify_nonce($_POST['meta_noncename'], __FILE__)) {
		return $post_id;
		}

		// check capabilities
		if ('post' == $_POST['post_type']) {
		if (!current_user_can('edit_post', $post_id)) {
		return $post_id;
		}
		} elseif (!current_user_can('edit_page', $post_id)) {
		return $post_id;
		}

		// exit on autosave
		if (defined('DOING_AUTOSAVE') == DOING_AUTOSAVE) {
		return $post_id;
		}

		if(isset($_POST['_url']))
		{
			update_post_meta($post_id, '_url', $_POST['_url']);
		} else
		{
			delete_post_meta($post_id, '_url');
		}
	}
}

add_action( 'init', 'anariel_rooms_post_types'); 
function anariel_rooms_post_types(){
	$labels=array(
		'name' => __( 'Tasting Rooms', 'anariel' ),
		'singular_name' => __( 'Rooms', 'anariel' )
	);

	$args=array(
		'labels' => $labels,
		'label' => __('Rooms', 'anariel'),
		'singular_label' => __('Rooms', 'anariel'),
		'public' => true,
		'show_ui' => true, 
		'_builtin' => false, 
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array('slug' => 'rooms-category'), 
		'supports' => array('title','editor','excerpt','revisions','thumbnail','comments'),
		'taxonomies' => array('rooms_cat', 'post_tag'),
		'menu_icon' => get_template_directory_uri('template_directory').'/images/wineicon.png'
	);

	if(function_exists('register_post_type')):
		register_post_type('rooms', $args);
	endif;
}



//Custom Post Type columns
add_filter("manage_edit-rooms_columns", "anariel_rooms_columns");
add_action("manage_posts_custom_column",  "anariel_rooms_custom_columns");
function anariel_rooms_columns($columns){
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => _x("Rooms Title", "Rooms title column", 'anariel'),
			"author" => _x("Author", "Rooms author column", 'anariel'),
			"rooms_cats" => _x("Rooms Categories", "Rooms categories column", 'anariel'),
			"date" => _x("Date", "Rooms date column", 'anariel')
		);

		return $columns;
}

function anariel_rooms_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "author":
				the_author();
				break;
			case "rooms_cats":
				echo get_the_term_list( $post->ID, 'rooms_cat', '', ', ', '' ); 
				break;
		}
}



//Custom taxonomies
add_action('init', 'anariel_rooms_taxonomies', 0);

function anariel_rooms_taxonomies(){

	$labels = array(
		'name' => _x( 'Rooms Categories', 'taxonomy general name', 'anariel' ),
		'singular_name' => _x( 'Rooms Category', 'taxonomy singular name', 'anariel' ),
		'search_items' =>  __( 'Search Rooms', 'anariel' ),
		'all_items' => __( 'All Rooms Categories', 'anariel' ),
		'parent_item' => __( 'Parent Rooms Category', 'anariel' ),
		'parent_item_colon' => __( 'Parent Rooms Category:', 'anariel' ),
		'edit_item' => __( 'Edit Rooms Category', 'anariel' ), 
		'update_item' => __( 'Update Rooms Category', 'anariel' ),
		'add_new_item' => __( 'Add New Rooms Category', 'anariel' ),
		'new_item_name' => __( 'New Rooms Category Name', 'anariel' )
	); 	
	
	register_taxonomy('rooms_cat',array('rooms'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'rooms_categories' )

	));
	
	 // Initialize New Taxonomy Labels
	  $labels = array(
		'name' => _x( 'Rooms Tags', 'taxonomy general name','anariel' ),
		'singular_name' => _x( 'Rooms Tag', 'taxonomy singular name','anariel' ),
		'search_items' =>  __( 'Search Types','anariel' ),
		'all_items' => __( 'All Tags','anariel' ),
		'parent_item' => __( 'Parent Tag','anariel' ),
		'parent_item_colon' => __( 'Parent Tag:','anariel' ),
		'edit_item' => __( 'Edit Tags','anariel' ),
		'update_item' => __( 'Update Tag','anariel' ),
		'add_new_item' => __( 'Add New Tag','anariel' ),
		'new_item_name' => __( 'New Tag Name','anariel' ),
	  );
		// Custom taxonomy for Project Tags
		register_taxonomy('rooms_tag',array('project'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'rooms_tag' ),
	  ));
	  
	  	add_action('admin_init','rooms_meta_init');

	function rooms_meta_init()
	{
		// add a meta box for WordPress 'project' type
		add_meta_box('rooms_meta', 'Project Infos', 'rooms_meta_setup', 'project', 'side', 'low');

		// add a callback function to save any data a user enters in
		add_action('save_post','rooms_meta_save');
	}

	function rooms_meta_setup()
	{
		global $post;

		?>
			<div class="rooms_meta_control">
				<label>URL</label>
				<p>
					<input type="text" name="_url" value="<?php echo get_post_meta($post->ID,'_url',TRUE); ?>" style="width: 100%;" />
				</p>
			</div>
		<?php

		// create for validation
		echo '<input type="hidden" name="meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	}

	function rooms_meta_save($post_id)
	{
		// check nonce
		if (!isset($_POST['meta_noncename']) || !wp_verify_nonce($_POST['meta_noncename'], __FILE__)) {
		return $post_id;
		}

		// check capabilities
		if ('post' == $_POST['post_type']) {
		if (!current_user_can('edit_post', $post_id)) {
		return $post_id;
		}
		} elseif (!current_user_can('edit_page', $post_id)) {
		return $post_id;
		}

		// exit on autosave
		if (defined('DOING_AUTOSAVE') == DOING_AUTOSAVE) {
		return $post_id;
		}

		if(isset($_POST['_url']))
		{
			update_post_meta($post_id, '_url', $_POST['_url']);
		} else
		{
			delete_post_meta($post_id, '_url');
		}
	}
}