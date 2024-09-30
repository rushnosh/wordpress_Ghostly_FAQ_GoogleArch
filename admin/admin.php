<?php 


if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly


    class GgeFaqAdmin {

        public function __construct()
        {
            add_action( 'init',array($this, 'my_custom_post_faq'));
            add_action( 'init', array($this, 'add_custom_taxonomies'));
            add_action( 'add_meta_boxes', array($this, 'faq_side_box') );
            add_action( 'save_post', array($this, 'faq_side_box_admin'));
            // Add the custom columns to the faq post type:
            add_filter( 'manage_faq_post_posts_columns', array($this, 'set_custom_faq_columns'));
            add_action( 'manage_faq_post_posts_custom_column' , array($this, 'custom_faq_column'), 10, 2 );

            //Add Category Filter
            add_action( 'restrict_manage_posts', array($this, 'add_gge_admin_filters'), 10, 1 );
        }

        /**
         * Add custom taxonomies
         *
         * Additional custom taxonomies can be defined here
         * https://codex.wordpress.org/Function_Reference/register_taxonomy
         */
        public function add_custom_taxonomies() {
            // Add new "Locations" taxonomy to Posts
            register_taxonomy('faqs', 'faq_post', array(
            // Hierarchical taxonomy (like categories)
            'hierarchical' => true,
            'show_in_rest' => true,
            // This array of options controls the labels displayed in the WordPress Admin UI
            'labels' => array(
                'name' => _x( 'FAQ Categories', 'taxonomy general name' ),
                'singular_name' => _x( 'FAQ Category', 'taxonomy singular name' ),
                'search_items' =>  __( 'Search FAQs' ),
                'all_items' => __( 'All FAQ Categories' ),
                'parent_item' => __( 'Parent FAQ Category' ),
                'parent_item_colon' => __( 'Parent FAQ Category:' ),
                'edit_item' => __( 'Edit FAQ Category' ),
                'update_item' => __( 'Update FAQ Category' ),
                'add_new_item' => __( 'Add New FAQ Category' ),
                'new_item_name' => __( 'New FAQ Category Name' ),
                'menu_name' => __( 'FAQ Categories' ),
            ),
            // Control the slugs used for this taxonomy
            'rewrite' => array(
                'slug' => 'faqs', // This controls the base slug that will display before each term
                'with_front' => false, // Don't display the category base before "/FAQs/"
                'hierarchical' => true // This will allow URL's like "/FAQs/boston/cambridge/"
            ),
            ));
        }

        public function my_custom_post_faq() {
            $labels = array(
                'name'               => _x( 'FAQs', 'post type general name' ),
                'singular_name'      => _x( 'FAQ', 'post type singular name' ),
                'add_new'            => _x( 'Add New', 'FAQ' ),
                'add_new_item'       => __( 'Add New FAQ' ),
                'edit_item'          => __( 'Edit FAQ' ),
                'new_item'           => __( 'New FAQ' ),
                'all_items'          => __( 'All FAQs' ),
                'view_item'          => __( 'View FAQ' ),
                'search_items'       => __( 'Search FAQs' ),
                'not_found'          => __( 'No FAQs found' ),
                'not_found_in_trash' => __( 'No FAQs found in the Trash' ), 
                'menu_name'          => 'FAQs'
            );
            $args = array(
                'labels'        => $labels,
                'description'   => 'Holds our FAQs and FAQ specific data',
                'public'        => true,
                'supports'      => array( 'title', 'editor' ),
                'show_in_rest' => true,
                'has_archive'   => false,
                'taxonomy'      => 'faqs',
                'menu_icon'     => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE4LjEuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgMTUuNjE0IDE1LjYxNCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMTUuNjE0IDE1LjYxNDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIHN0eWxlPSJmaWxsOiMwMzAxMDQ7IiBkPSJNMTQuMTQ0LDMuODA3Yy0wLjE4OC0wLjE3LTAuNDU5LTAuMjI0LTAuNjk4LTAuMTQxYy0wLjAwMywwLjAwMS0wLjM3NiwwLjEyOC0wLjkyNCwwLjEyOA0KCQkJYy0xLjE5NywwLTIuODc2LTAuNTg0LTQuMDc0LTMuMzczQzguMzQxLDAuMTcyLDguMDk5LDAuMDA3LDcuODI4LDBjLTAuMjktMC4wMDYtMC41MjMsMC4xNDMtMC42NDQsMC4zODUNCgkJCUM1Ljc3OSwzLjIwMiw0LjEzOSwzLjc5NCwzLjAxLDMuNzk0Yy0wLjQ5MywwLTAuODE1LTAuMTE4LTAuODE1LTAuMTE4Yy0wLjI0Mi0wLjA5Ny0wLjUxNy0wLjA1LTAuNzE0LDAuMTINCgkJCVMxLjE5OCw0LjIzMSwxLjI1OCw0LjQ4NUMxLjcsNi4zNDMsNC4wOTQsMTUuNjE0LDcuODA3LDE1LjYxNHM2LjEwNi05LjI3MSw2LjU0OS0xMS4xMjlDMTQuNDE1LDQuMjM3LDE0LjMzMiwzLjk3OCwxNC4xNDQsMy44MDd6DQoJCQkgTTcuODA4LDE0LjIyYy0xLjc3NiwwLTMuODQtNC45ODQtNC45MzMtOS4wMzRjMS4wOTEsMC4wMzQsMy4xMzItMC4zMTIsNC44OTctMy4wMmMxLjU0NiwyLjU5OCwzLjU4NiwzLjAyMSw0Ljc1LDMuMDIxDQoJCQljMC4wNzYsMCwwLjE0OS0wLjAwMSwwLjIyLTAuMDA1QzExLjY1MSw5LjIzNCw5LjU4NiwxNC4yMiw3LjgwOCwxNC4yMnoiLz4NCgkJPHBhdGggc3R5bGU9ImZpbGw6IzAzMDEwNDsiIGQ9Ik03LjU0MywxMC4yNzhjLTAuNTIxLDAtMC44OTMsMC4zODEtMC44OTMsMC45MTJjMCwwLjUyMSwwLjM2MSwwLjkxMiwwLjg5MywwLjkxMg0KCQkJYzAuNTQxLDAsMC45MDItMC4zOTEsMC45MDItMC45MTJDOC40MzUsMTAuNjU4LDguMDg0LDEwLjI3OCw3LjU0MywxMC4yNzh6Ii8+DQoJCTxwYXRoIHN0eWxlPSJmaWxsOiMwMzAxMDQ7IiBkPSJNNy42ODIsNS4xMjZjLTAuNzEsMC0xLjMxMywwLjIwMS0xLjY3NCwwLjQxMmwwLjM0MSwxLjA5M2MwLjI2LTAuMTgxLDAuNjYyLTAuMzAyLDAuOTkyLTAuMzAyDQoJCQlDNy44NDIsNi4zNSw4LjA3Miw2LjU4LDguMDcyLDYuOTMxYzAsMC4zNDItMC4yNjEsMC42NjItMC41ODEsMS4wNDJDNy4wMzksOC41MTQsNi44Nyw5LjAzNiw2LjksOS41NDZsMC4wMSwwLjI2MmgxLjMzM1Y5LjYyNw0KCQkJYy0wLjAxLTAuNDUsMC4xNDEtMC44NDIsMC41MTItMS4yNTNjMC4zOC0wLjQyLDAuODUxLTAuOTIyLDAuODUxLTEuNjg0QzkuNjA3LDUuODU4LDkuMDA2LDUuMTI2LDcuNjgyLDUuMTI2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K'
            );

            register_post_type( 'faq_post', $args ); 
        }


        //Creates a side box on the faq sections
        public function faq_side_box() {
            add_meta_box( 
                'faq_side_box',
                __( 'FAQ Configurations', 'myplugin_textdomain' ),
                array($this, 'faq_admin_side_box'),
                'faq_post',
                'side',
                'high'
            );
        }

        public function faq_admin_side_box( $post )
        {
            //Is "disabled" for Google Arch updates
            $googleArchPostDisabled = get_post_meta( get_the_ID(), 'faq_google_arch_disable', true ) ? "checked" : ""; 

            //$saved_value = get_post_meta( get_the_ID(), 'faq_inside', true ); 
            wp_nonce_field( plugin_basename( __FILE__ ), 'faq_content_nonce' );
            // echo '<label for="faq_inside"></label>';
            // echo '<input type="text" id="faq_inside" value="'. $saved_value  .'" name="faq_inside" placeholder="enter a faq side example " />';
            echo '<label for="faq_google_arch_disable">Disable this answer showin in Google FAQ Arch  </label>';
            echo '<input type="checkbox" id="faq_google_arch_disable" '. $googleArchPostDisabled .' name="faq_google_arch_disable" />';
        }


        public function faq_side_box_admin( $post_id ) {

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

            if (!empty($_POST) && !empty($_POST['faq_content_nonce'])) {
                if ( !wp_verify_nonce( $_POST['faq_content_nonce'], plugin_basename( __FILE__ ) ) )
                return;
    
                if ( 'page' == $_POST['post_type'] ) {
                    if ( !current_user_can( 'edit_page', $post_id ) )
                    return;
                } else {
                    if ( !current_user_can( 'edit_post', $post_id ) )
                    return;
                }
                $faq_google_arch_disable = isset($_POST['faq_google_arch_disable']) ? true : false;
                update_post_meta( $post_id, 'faq_google_arch_disable', $faq_google_arch_disable );
                // list is empty.
            }
        }
        
        public function set_custom_faq_columns($columns) {
            $columns['Category'] = __( 'Category', 'your_text_domain' );

            //Placing Date into the last section of the $columns array
            foreach ($columns as $key => $val) {
                if (in_array($key, array('date'))) {
                    unset($columns[$key]);
                    $columns[$key] = $val;
                }
            }

            return $columns;
        }


        public function custom_faq_column( $column, $post_id ) {
            switch ( $column ) {

                case 'Category' :
                    $terms = get_the_term_list( $post_id , 'faqs' , '' , ',' , '' );
                    if ( is_string( $terms ) )
                        echo $terms;
                    else
                        _e( 'Unable to get faq(s) categories', 'your_text_domain' );
                    break;

            }
        }

        public function add_gge_admin_filters( $post_type ){
            if( 'faq_post' !== $post_type ){
                return;
            }
            $args = array(
                'name' => 'faqs',
                
            );
            $taxonomies = get_taxonomies( $args );
            // loop through the taxonomy filters array
            foreach( $taxonomies as $slug ){
                $taxonomy = get_taxonomy( $slug );
                $selected = '';
                // if the current page is already filtered, get the selected term slug
                $selected = isset( $_REQUEST[ $slug ] ) ? $_REQUEST[ $slug ] : '';
                // render a dropdown for this taxonomy's terms
                wp_dropdown_categories( array(
                    'show_option_all' =>  $taxonomy->labels->all_items,
                    'taxonomy'        =>  $slug,
                    'name'            =>  $slug,
                    'orderby'         =>  'name',
                    'value_field'     =>  'slug',
                    'selected'        =>  $selected,
                    'hierarchical'    =>  true,
                ) );
            }
        }


    }

?>