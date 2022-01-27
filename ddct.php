<?php
/**
 * Plugin Name: Diet Dr Code Test
 * Plugin URI:
 * Description:
 * Author: David Ellenburg
 * Version: 1.0.0
 * Author URI: http://www.ellenburgweb.com
 * License: GPL2
 **/


define( 'ddct_version', '1.0.0' );
define( 'installed_version', get_option( 'ddct_version' ) );

if ( ! class_exists( 'ddct_plugin' ) ) {
	class ddct_plugin {
		private $plugin_name= 'ddct';
		private $plugin_version= ddct_version;

		public function __construct() {
			add_action( 'init', array( $this, 'recipe_custom_post_type' ) );
			add_action( 'add_meta_boxes', array( $this, 'meta_box_for_recipes' ) );
			add_action( 'save_post', array( $this, 'team_member_save_meta_boxes_data' ), 10, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'search_recipes_js' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'search_recipes_css' ), 10, 2 );
			add_shortcode( 'recipe_search_page', array( $this, 'search_recipes_search_page' ));

			add_action( 'rest_api_init', array( $this, 'edit_rest_api' ) );
		}

		public function recipe_custom_post_type() {

			/*
			 * The $labels describes how the post type appears.
			 */
			$labels = array(
				'name'          => 'Recipes', // Plural name
				'singular_name' => 'Recipe'   // Singular name
			);

			/*
			 * The $supports parameter describes what the post type supports
			 */
			$supports = array(
				'title',        // Post title
				'editor',       // Post content
				'excerpt',      // Allows short description
				'author',       // Allows showing and choosing author
				'thumbnail',    // Allows feature images
				'comments',     // Enables comments
				'trackbacks',   // Supports trackbacks
				'revisions',    // Shows autosaved version of the posts
				'custom-fields', // Supports by custom fields
				'rating'
			);

			/*
			 * The $args parameter holds important parameters for the custom post type
			 */
			$args = array(
				'labels'              => $labels,
				'description'         => 'Catalog of Recipes',// Description
				'supports'            => $supports,
				'taxonomies'          => array( 'category', 'post_tag' ),// Allowed taxonomies
				'hierarchical'        => false,// Allows hierarchical categorization, if set to false, the Custom Post Type will behave like Post, else it will behave like Page
				'public'              => true,// Makes the post type public
				'show_ui'             => true,// Displays an interface for this post type
				'show_in_menu'        => true,// Displays in the Admin Menu (the left panel)
				'show_in_nav_menus'   => true,// Displays in Appearance -> Menus
				'show_in_admin_bar'   => true,// Displays in the black admin bar
				'menu_position'       => 5,// The position number in the left menu
				'menu_icon'           => true,// The URL for the icon used for this post type
				'can_export'          => true,// Allows content export using Tools -> Export
				'has_archive'         => true,// Enables post type archive (by month, date, or year)
				'exclude_from_search' => false,// Excludes posts of this type in the front-end search result page if set to true, include them if set to false
				'publicly_queryable'  => true,// Allows queries to be performed on the front-end part if set to true
				'capability_type'     => 'post',// Allows read, edit, delete like “Post”
				'show_in_rest'        => true
			);

			register_post_type( 'recipes', $args );
		}

		public function meta_box_for_recipes() {
			add_meta_box( 'recipe_rating_meta_box', 'Recipe Rating', array(
				$this,
				'recipe_rating_meta_box_html_output'
			), 'recipes', 'normal', 'low' );

			add_meta_box( 'recipe_cook_time_meta_box', 'Cook Time', array(
				$this,
				'recipe_cook_time_meta_box_html_output'
			), 'recipes', 'normal', 'low' );

			add_meta_box( 'recipe_short_description_meta_box_html_output', 'Short Description', array(
				$this,
				'recipe_short_description_meta_box_html_output'
			), 'recipes', 'normal', 'low' );
		}

		public function recipe_rating_meta_box_html_output( $post ) {
			wp_nonce_field( basename( __FILE__ ), 'recipe_rating_meta_box_nonce' ); //used later for security
			$rating  = get_post_meta( $post->ID, 'recipe_rating', true );
			$$rating = 'checked';
			echo '
			<input type="radio" name="recipe_rating" value="one" ' . $one . '/>
			<label for="recipe_rating">1</label>
			
			<input type="radio" name="recipe_rating" value="two" ' . $two . '/>
			<label for="recipe_rating">2</label>
			
			<input type="radio" name="recipe_rating" value="three" ' . $three . '/>
			<label for="recipe_rating">3</label>
			
			<input type="radio" name="recipe_rating" value="four" ' . $four . '/>
			<label for="recipe_rating">4</label>
			
			<input type="radio" name="recipe_rating" value="five" ' . $five . '/>
			<label for="recipe_rating">5</label>
			</p>
			';
		}

		public function recipe_cook_time_meta_box_html_output( $post ) {
			wp_nonce_field( basename( __FILE__ ), 'recipe_cook_time_meta_box_nonce' ); //used later for security
			$cookTime  = get_post_meta( $post->ID, 'recipe_cook_time', true );
			echo '
			<input type="text" name="recipe_cook_time" value="'.$cookTime.'"/>
			';
		}

		public function recipe_short_description_meta_box_html_output( $post ) {
			wp_nonce_field( basename( __FILE__ ), 'recipe_short_description_meta_box_nonce' ); //used later for security
			$shortDescription  = get_post_meta( $post->ID, 'recipe_short_description', true );
			echo '
			<textarea name="recipe_short_description" style="width: 100%">'.$shortDescription.'</textarea>
			';
		}

		public function team_member_save_meta_boxes_data( $post_id ) {
			// check for correct user capabilities - stop internal xss from customers
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			//region Rating
			// check for nonce to top xss
			if ( ! isset( $_POST['recipe_rating_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['recipe_rating_meta_box_nonce'], basename( __FILE__ ) ) ) {
				return;
			}

			// update fields
			if ( isset( $_REQUEST['recipe_rating'] ) ) {
				update_post_meta( $post_id, 'recipe_rating', sanitize_text_field( $_POST['recipe_rating'] ) );
			}
			//endregion

			//region Cook Time
			// check for nonce to top xss
			if ( ! isset( $_POST['recipe_cook_time_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['recipe_cook_time_meta_box_nonce'], basename( __FILE__ ) ) ) {
				return;
			}

			// update fields
			if ( isset( $_REQUEST['recipe_cook_time'] ) ) {
				update_post_meta( $post_id, 'recipe_cook_time', sanitize_text_field( $_POST['recipe_cook_time'] ) );
			}
			//endregion

			//region Short Description
			// check for nonce to top xss
			if ( ! isset( $_POST['recipe_short_description_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['recipe_short_description_meta_box_nonce'], basename( __FILE__ ) ) ) {
				return;
			}

			// update fields
			if ( isset( $_REQUEST['recipe_short_description'] ) ) {
				update_post_meta( $post_id, 'recipe_short_description', sanitize_text_field( $_POST['recipe_short_description'] ) );
			}
			//endregion
		}

		public function edit_rest_api(){
			//echo "HERE";
			register_meta('post','recipe_rating', array(
				'type' => 'string',
				//'description' => 'event location',
				'single' => true,
				'show_in_rest' => true
			));

			register_meta('post','recipe_cook_time', array(
				'type' => 'string',
				//'description' => 'event location',
				'single' => true,
				'show_in_rest' => true
			));

			register_meta('post','recipe_short_description', array(
				'type' => 'string',
				//'description' => 'event location',
				'single' => true,
				'show_in_rest' => true
			));
		}

		public function search_recipes_js(){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'public/js/search_recipes.js', array(), $this->plugin_version, false );
		}

		public function search_recipes_css(){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'public/css/search_recipes.css', array(), $this->plugin_version, false );
		}

		public function search_recipes_search_page(){
			return file_get_contents(__DIR__. '/public/recipe_search_page.html');
		}
	}

	$startPlugin = new ddct_plugin();
}
