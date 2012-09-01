<?php
/**
 * Twenty Eleven functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyeleven_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 * 
 * Unlike style.css, the functions.php of a child theme does not override its counterpart from the parent. 
 * Instead, it is loaded in addition to the parent’s functions.php. (Specifically, it is loaded right 
 * before the parent’s file.)
 **/

	/*
	 * ADDING ADDITIONAL CATEGORIES OF VARIOUS KINDS
	 */

	// Size of header image
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyeleven_header_image_width', 1024 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyeleven_header_image_height', 181 ) );
	
	// Add new size category
	add_action( 'after_setup_theme', 'theme_setup' );
	function theme_setup() { // theme_setup can change name here, and add_action can be placed below
  		add_image_size( 'preview', 450, 225, true ); // true/false refers to crop
  		add_image_size( 'mediumpreview', 300, 150, true);
	} 

	// Register new Widget "Featured"
	register_sidebar( array(
		'name' => __( 'Featured', 'twentyeleven' ),
		'id' => 'featured',
		'description' => __( 'Featured', 'twentyeleven' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	) );
	
	function posted_on() {
		printf( __( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a>' ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'twentyeleven' ), get_the_author() ) ),
			get_the_author()
		);
	}
	
	function posted_by_author() {
	printf( __( '<span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentyeleven' ), get_the_author() ) ),
		get_the_author()
	);
	}

	function twentyeleven_posted_on() {
		printf( __( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven' ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'twentyeleven' ), get_the_author() ) ),
			get_the_author()
		);
	}
	
	

	/*
	 * FEATURED IMAGES AND IMAGE HANDLING
	 */
	
	// Return an array of attachments from a poast
	function get_post_attachments($postID) {
		$args = array(
		'post_type' => 'attachment',
		'numberposts' => null,
		'post_status' => null,
		'post_parent' => $postID
		);

		$attachments = get_children($args);
		return $attachments;
	}
	
	// Get thumbnail/preview for post ('featured image') - LARGE version
	function get_featured_image_large($postID) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), 'preview' );
		return $image[0];
	}
	
	// Get thumbnail for post ('featured image') - MEDIUM version
	function get_featured_image_medium($postID) {
		
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), 'mediumpreview' );
		
		if ($image[1] == 300 && $image[2] == 150) {
			$mediumimage = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), 'mediumpreview' );
			return $mediumimage[0];
		} else {
			$mediumimage = get_first_post_image_medium ($postID);
			return $mediumimage;
		}
	}
	
	// Get the first image of a poast
	/*function get_featured_image_medium ($postID) {
		
		$args = array(
		'post_type' => 'attachment',
		'numberposts' => null,
		'post_status' => null,
		'post_parent' => $postID
		);

		$attachments = get_children($args);
		
		if ($attachments) {
			foreach ($attachments as $attachment) { // Loop does this for all attachments in $attachments
				$first_image = wp_get_attachment_medium_url($attachment->ID);
			}
			return $first_image[0];*/
			
		//} // Else we have no attachments, should call get_first_img() here
	//}
	
	// Returns the URL of the first image of a post in medium size
	function get_first_post_image_medium ($postID) {
		
		$args = array(
		'post_type' => 'attachment',
		'numberposts' => null,
		'post_status' => null,
		'post_parent' => $postID
		);

		$attachments = get_children($args);
		//$url_array = array();
		
		if ($attachments) {
			foreach ($attachments as $attachment) { // Loop does this for all attachments in $attachments
				$first_image = wp_get_attachment_medium_url($attachment->ID);
			}
			//$number_of_entries = count($url_array);
			return $first_image;
			
		} // Else we have no attachments, should return a random TLS image else {
			//return linktoimage
		//}
	}
	
		
	// Function for finding url to medium image in an attatchment
	function wp_get_attachment_medium_url( $id ){
	    $medium_array = image_downsize( $id, 'medium' );
	    $medium_path = $medium_array[0];
	    return $medium_path;
	}
	
	// Returns all attachments URLs (medium) from a post as an array
	/*function get_attachment_urls_medium($postID) {
		$args = array(
		'post_type' => 'attachment',
		'numberposts' => null,
		'post_status' => null,
		'post_parent' => $postID
		);

		$attachments = get_children($args);
		
		$url_array = array();
		
		if ($attachments) {
			foreach ($attachments as $attachment) { // Loop does this for all attachments in $attachments
				$url = image_attributes[0]; // First entry in array (0) is the URL
				array_push($url_array, wp_get_attachment_medium_url($attachment->ID));
			}
			return $url_array;
			
			/* For loop, if you want to do something with this
			 * $number_of_entries = count($url_array);
			 * for ($i=0; $i < $number_of_entries; $i++) {
				echo '<img src="'.$url_array[$i].'"/> <br />';
				echo $url_array[$i];
				echo '<br />';
			}*/
			
		//} //Else no attachment, break(?) naw, return null
		//return null;
	//}
	
	
	// Get the first post ID
	function get_first_post_ID() {
		query_posts('posts_per_page=1');
			while (have_posts()) : the_post(); 
				if (get_post_type() == 'post') { // only posts, not pages
					return get_the_ID();
				}
			endwhile;
		wp_reset_query();
	}

	// Get the first category for a post (by ID)
	function get_categories_for_post($id) {
		$categories = wp_get_post_categories($id);
		
		foreach ($categories as $category) {
			$theCategory = $category;
			break;
		}
		return $theCategory;
	}
	
	// Return ID of the 4 latest posts to use in the next function to check for posts in categories already listed
	// on the front page
	function get_four_latest_posts() {
		$array = array();
		
		$string = '&showposts=4'; //Change this number for more posts
		query_posts($string);
			while ( have_posts() ) : the_post() ;			
				if (get_post_type() =='post') {
					$theID = get_the_ID();
					array_push($array, $theID);
				}				
			endwhile;
		wp_reset_query();
		return $array;
	}
	
	// Echo the latest posts from a category
	function echo_last_posts_from_category($catid) {
		$fourLatestPosts = get_four_latest_posts();
		$counter = 0; // counts posts in the same category
		foreach ($fourLatestPosts as $entry) {
			if (get_categories_for_post($entry) == $catid) {
				$counter++;
			}
		}
		
		$string = 'cat=' . $catid . '&showposts=3&offset=' .$counter; //Change $showposts=x to change the number of links output
		query_posts($string);
			while ( have_posts() ) : the_post() ;			
				if (get_post_type() =='post') {
					echo('<li><a href="');
					echo(the_permalink());
					echo('">');
					echo(the_title());
					echo('</a></li>');
				}				
			endwhile;
		wp_reset_query();
	}


	// Echo the first post preview
	function echo_first_post_preview($id) {
		$theCategory = get_categories_for_post($id);
		echo('<p class="previews-stories-category"><a href="' . get_category_link($theCategory) . '">');
		echo(get_the_category_by_ID($theCategory));
		echo('</a></p><h1><a href="');
		echo(get_permalink($id));
		echo('">');
		echo(get_the_title($id));
		echo('</a></h1>');
		echo('<p class="previews-topstoryexcerpt">' . new_excerpt_firstpreview($id, 20, '...'));
		echo('<a href="');
		echo(get_permalink($id));
		echo('">more</a></p>');
		echo('<p class="previews-topstoryexcerpt">Related articles</p>');
		echo('<div class="preview-topstory-related">');
		echo('<ul>');
		echo_last_posts_from_category($theCategory);
		echo('</ul>');
		echo('</div>');
	}
	
	// Echo the first post previews belonging image
	function echo_first_post_preview_image($id) {
		echo('<img src="' . get_featured_image_large($id) . '" />');
	}
	
	// Creates 'custom made' the_excerpt
	// From http://smashingwp.com/tutorials/add-a-custom-excerp-size-in-you-wordpress-theme/
	function new_excerpt_firstpreview($id, $excerpt_length, $ending, $superending) {
		$post = get_post($id);
		$text = $post->post_content;
		//$text = get_the_content('');
		$text = strip_shortcodes( $text );
	
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
	
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $ending;
			return ''.$text.' '.$superending;
		} else {
			$text = implode(' ', $words);
			return '<p>'.$text.'</p>';
		}
	}
	
	// Echo previews A to B
	function echo_n_post_preview($fromPreviewNo, $toPreviewNo) {
		$aString = 'posts_per_page=' . (string)$toPreviewNo; // Have to cast int to string
		query_posts($aString);
		//$postsArray[] = array("ffo", "bar");
		$count = 1;
		while ( have_posts() ) : the_post();
			if ($count >= $fromPreviewNo) {
				if (get_post_type() =='post') { // Checks that post is 'post' and not 'page'
					// Create an array and store the ID's in it
					//$theID = get_the_ID();
					//array_push($postsArray, $theID);
					echo('');
						$theID = get_the_ID();
						$theCategory = get_categories_for_post($theID);					
						echo('</p><div class="mediumimg">');
						echo('<img src="' . get_featured_image_medium($theID) . '" />');
						echo('</div>');
						echo('<p class="previews-stories-category"><a href="' . get_category_link($theCategory) . '">');
						echo(get_the_category_by_ID($theCategory));
						echo('</a></p><a href="');
						echo the_permalink($theID);
						echo('"><h2>');
						echo the_title();
						echo('</h2></a>');
						//the_excerpt();
					echo('');
				}
			}
			$count ++;
		endwhile;
		//echo($postsArray);
		//return $postsArray;
		//echo($postsArray);
		wp_reset_query();
	}
	
	
	// Display the tls_border div. Input: Text to be echoed on top of the border
	function echo_tls_border($titleOnBorder, $id = "") {
		if (!empty($id)) {
			echo '<div id="', $id, '" class="tls_border">';
		} else {
			echo('<div class="tls_border">');
		}
		echo('<p>' . $titleOnBorder . '</p>');
		echo('</div>');
	}

	// Function found on Internet. Added to be able to add the sidebar to single Posts ("blogposts") as it was
	// removed as being default in Twenty Eleven - see The Lifestream 4 documentation for sauce
	add_filter('body_class', 'fix_body_class_for_sidebar', 20, 2);
	function fix_body_class_for_sidebar($wp_classes, $extra_classes) {
		if( is_single() ){
		   if (in_array('singular',$wp_classes)){
				foreach($wp_classes as $key => $value) {
					if ($value == 'singular')
						unset($wp_classes[$key]);
				}
			}
		}
		 return array_merge($wp_classes, (array) $extra_classes);
	}
	
	/*
	 * PULLING OUT STUFF FROM THE FORUMS
	 */
	
	// Returns a thread from the forum.
	function forum_get_thread($threadid) {
		include('forumcall.php');
		
		$query_posts = "select post.postid, post.dateline, post.userid, user.username, customavatar.filename, usergroup.usertitle, post.pagetext
			from post
			inner join user
			on post.userid = user.userid
			inner join customavatar
			on user.userid = customavatar.userid
			inner join usergroup
			on user.displaygroupid = usergroup.usergroupid
			where threadid=" . $threadid . "
			order by postid";
		
		$result = mysql_query($query_posts);
		
		if ($result == '') {
			echo('String is empty');
			echo('<br />');
		}
		
		$numberofrows = mysql_num_rows($result);
		$numberofcolumns = mysql_num_fields($result);
		
		for ($i = 0; $i < $numberofrows; $i++) {
			$row = mysql_fetch_row($result);
			for ($j = 0; $j < $numberofcolumns; $j++) {
				echo($row[$j]);
				echo ' ';
			}
			echo '<br />';
		}
		include('forumcallclose.php');
	}
	
	// Returns the 10 threads with the latest posts from the forums
	function forum_get_latest_posts() {
		include('forumcall.php');
		
		$query_posts = "
			select thread.threadid, thread.title, thread.forumid, forum.title, user.userid, thread.lastposter, post.pagetext, thread.views, thread.replycount, thread.lastpostid
			from thread
			inner join post
			on post.postid = thread.lastpostid
			inner join user
			on user.userid = post.userid
			inner join forum
			on forum.forumid = thread.forumid
			where thread.forumid IN (3, 4, 5, 6, 7, 8, 9, 10, 11, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 46, 37, 36, 38, 47, 39, 41, 55, 43, 44, 48, 51, 56, 57, 58, 59, 61, 60, 65, 68)
			order by thread.lastpost DESC
			LIMIT 0, 10";
		
		$result = mysql_query($query_posts);
		
		if ($result == '') {
			echo('String is empty');
			echo('<br />');
		}
		
		//threadid title 	forumid 	title 	userid 	lastposter 	pagetext 	views 	replycount
		//Ruins your day Mk 2 in General Discussion - posted by Cthulhu (text) 66 views | 66 replies
		
		$numberofrows = mysql_num_rows($result);
		$numberofcolumns = mysql_num_fields($result);
		
		for ($i = 0; $i < $numberofrows; $i++) {
			$row = mysql_fetch_row($result);
			$postbody = substr($row[6], 0, 100);
			// Preferably also strip the last empty space. And strip away IMG-tags etc
			
			$thread = '<a class="forum-thread" href="http://thelifestream.net/forums/showthread.php?goto=newpost&t=' . $row[0] . '">' . $row[1] . ' ></a>';
			$forum = '<a class="forum-forum" href="http://thelifestream.net/forums/forumdisplay.php?f=' . $row[2] . '">' . $row[3] . '</a>'; 
			$user = '<a class="forum-user" href="http://thelifestream.net/forums/viewprofile.php?p=' . $row[4] . '">' . $row[5] . '</a> ';
			/* echo(' <em>' . $postbody . '...</em> ');*/
			$views = $row[7] . ' views, ';
			$replies = $row[8] . ' replies';
			
			echo($user);
			echo('<br />');
			echo($thread);
			echo('<br />');
			echo($forum);
			echo('<br /><br />');
		}
		
		include('forumcallclose.php');
	}
	
	/*
	 * WIDGETS
	 */
	
	// Class for creating the unique "Menu for Sidebar" Widget
	class MenuForSidebar extends WP_Widget {
	
		// Constructor
		function MenuForSidebar() {
			// Instantiate the parent object
			$widget_ops = array( 'description' => __('Use this widget to add menus to the Sidebar.') );
			parent::__construct( 'menu_sidebar', 'Menu for Sidebar', $widget_ops );
		}
	
		function widget( $args, $instance ) {
			// Widget output
			
			$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;
			
			/* Remove check for now - gives us pain
			 * if ( !$nav_menu )
				echo('Empty?!');
				return;*/
			
			$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$instance['imagelink'] = apply_filters( 'widget_title', empty( $instance['imagelink'] ) ? '' : $instance['imagelink'], $instance, $this->id_base );
			
			$beforeimagetag = '<img src="';
			$afterimagetag = '" />';
			
			echo $args['before_widget'];
			
			if ( !empty($instance['imagelink']) ) {
				echo $args['before_title'] . $beforeimagetag . $instance['imagelink'] . $afterimagetag . $args['after_title'];
			} else {
				echo $args['before_title'] . $instance['title'] . $args['after_title'];
			}
	
			wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu ) );
	
			echo $args['after_widget'];
		}
	
		function update( $new_instance, $old_instance ) {
			// Save widget options
			$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
			$instance['imagelink'] = strip_tags( stripslashes($new_instance['imagelink']) );
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];
			return $instance;
		}
	
		function form( $instance ) {
			// Output admin widget options form
			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			$imagelink = isset( $instance['imagelink'] ) ? $instance['imagelink'] : '';
			$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
	
			// Get menus
			$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
	
			// If no menus exists, direct the user to go and create some.
			if ( !$menus ) {
				echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
				return;
			}
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('imagelink'); ?>"><?php _e('Image link:') ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('imagelink'); ?>" name="<?php echo $this->get_field_name('imagelink'); ?>" value="<?php echo $imagelink; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
				<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
			<?php
				foreach ( $menus as $menu ) {
					$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
					echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
				}
			?>
				</select>
			</p>
			<?php
		}
	}

	function ourwidgets_register_widgets() {
		register_widget( 'MenuForSidebar' );
	}
	add_action( 'widgets_init', 'ourwidgets_register_widgets' );


			
?>
