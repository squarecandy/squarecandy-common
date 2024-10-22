<?php
// PHP code that should be distributed to all base or standalone themes goes here.

// Turn Off Application Passwords on All Projects:
add_filter( 'wp_is_application_passwords_available', '__return_false', 20 );
// use priority higher than 20 to override elsewhere

// EWWW/EasyIO - short-circuit and bypass auto-generation of extra srcset sizes
// https://docs.ewww.io/article/48-customizing-exactdn
add_filter( 'exactdn_srcset_multipliers', '__return_false' );


// SEOPress
// filter the description for html markup and length
// fix a bug that leaves twitter:image empty
if ( function_exists( 'seopress_init' ) ) :

	if ( ! function_exists( 'squarecandy_seopress_titles_desc' ) ) :

		add_filter( 'seopress_titles_desc', 'squarecandy_seopress_titles_desc' );

		function squarecandy_seopress_titles_desc( $seopress_titles_desc ) {

			global $post;
			$max_length     = apply_filters( 'squarecandy_seopress_desc_length', 170 );
			$min_length     = 15;
			$explicitly_set = is_singular() && get_post_meta( $post->ID, '_seopress_titles_desc', true ); //custom desc for this post
			$default_desc   = esc_attr( seopress_get_service( 'TitleOption' )->getHomeDescriptionTitle() );
			$default_set    = is_singular() && $default_desc === $seopress_titles_desc; //default global desc

			// if desc is empty, try excerpt
			if ( ! $seopress_titles_desc ) {
				$seopress_titles_desc = esc_attr( get_the_excerpt( $post ) );
			}

			//input at this point has been esc_attr()
			$seopress_titles_desc = str_replace( '&nbsp;', ' ', $seopress_titles_desc ); // replace non-breaking space with normal space

			// filter unwanted tags etc - first html decode so tag closures etc aren't encoded
			$seopress_titles_desc = html_entity_decode( $seopress_titles_desc, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
			$seopress_titles_desc = wp_strip_all_tags( $seopress_titles_desc ); //strip out html tags, might still have some entities

			// remove shortcodes, including [caption] [gallery] [audio] etc.
			$seopress_titles_desc = strip_shortcodes( $seopress_titles_desc );

			//strip out multiple separators
			$options              = get_option( 'seopress_titles_option_name' );
			$sep                  = isset( $options['seopress_titles_sep'] ) ? htmlspecialchars( $options['seopress_titles_sep'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) : '-';
			$regex                = '#(?<= ' . $sep . ' )[ ' . $sep . ']*|[ ' . $sep . ']*$|^[' . $sep . ' ]*#u'; //repeated separators, separators at start or end
			$seopress_titles_desc = preg_replace( $regex, '', $seopress_titles_desc );

			// handle lots of line breaks
			$seopress_titles_desc = trim( $seopress_titles_desc );
			$seopress_titles_desc = str_replace( array( "\r\r", "\n\n", "\r\n\r\n", "\r\n", "\r", "\n", '<br>' ), ' • ', $seopress_titles_desc );

			// handle length, but only if we didn't explicitly set the desc for this post & aren't falling back to the global desc
			if ( ! $explicitly_set && ! $default_set && strlen( $seopress_titles_desc ) > $max_length ) {

				// first try to truncate to sentences
				// get the first period under the max length, this gives us length of the first sentence
				preg_match_all( '/[a-zA-Z{2,}](?<!Dr|Ms|Mrs|Mr)\. |\.["\'”]|\.\.\.|[!?]/u', $seopress_titles_desc, $matches, PREG_OFFSET_CAPTURE );
				$first_sentence = 0;
				$offset         = 1;
				foreach ( $matches[0] as $match ) {
					if ( $match[1] <= $max_length ) {
						$first_sentence = $match[1];
						$length         = strlen( $match[0] );
						if ( $length > 2 ) {
							$offset = $length;
						}
					}
				}
				$excerpt = $first_sentence > $min_length ? substr( $seopress_titles_desc, 0, $first_sentence + $offset ) : '';

				//if no sentences under max_length, truncate to words
				if ( ! $excerpt ) {
					$excerpt = substr( $seopress_titles_desc, 0, strrpos( $seopress_titles_desc, ' ', $max_length - strlen( $seopress_titles_desc ) - 3 ) ) . '...';
				}
				$seopress_titles_desc = $excerpt ? $excerpt : $seopress_titles_desc;
			}

			$seopress_titles_desc = esc_attr( $seopress_titles_desc ); // final safety escape

			return $seopress_titles_desc;
		}
	endif;

	if ( ! function_exists( 'squarecandy_seopress_twitter_image' ) ) :

		add_filter( 'seopress_social_twitter_card_thumb', 'squarecandy_seopress_twitter_image' );

		// fix bug where seopress is checking for '' but seopress_get_service('SocialOption')->getSocialTwitterCardOg() returns null
		function squarecandy_seopress_twitter_image( $image ) {
			if ( ! $image ) {
				$url                   = '';
				$default_twitter_image = seopress_get_service( 'SocialOption' )->getSocialTwitterImg();
				if ( $default_twitter_image ) { //Default Twitter
					$url = $default_twitter_image;
				} else {
					$default_fb_image = seopress_get_service( 'SocialOption' )->getSocialFacebookImg();
					if ( $default_fb_image && '1' === seopress_get_service( 'SocialOption' )->getSocialTwitterCardOg() ) {
						$url = $default_fb_image;
					}
				}
				if ( $url ) {
					return '<meta name="twitter:image" content="' . $url . '">';
				}
			}
			return $image;
		}
	endif;

	if ( ! function_exists( 'squarecandy_seopress_social_title' ) ) :

		add_filter( 'seopress_social_og_title', 'squarecandy_seopress_social_title' );
		add_filter( 'seopress_social_twitter_card_title', 'squarecandy_seopress_social_title' );

		/**
		 * Fix bug where if there's no custom title set for this archive type, SEOPress defaults to the_title_attribute,
		 * which will be the title of one of the posts in the archive. We want the WP generated post title tag instead.
		 * @param $title string e.g. <meta property="og:title" content="Worship (In Person and Online)">
		 * @return string
		 */
		function squarecandy_seopress_social_title( $title ) {
			if ( is_archive() ) {
				preg_match( '/<meta.*content="(.*)"/m', $title, $matches );
				$post_title     = the_title_attribute( 'echo=0' ); //seopress will default to this if no custom title for an archive type
				$seopress_title = seopress_titles_the_title(); //this will be blank if there us no custom title for the archive type
				if ( ! $seopress_title && $matches && isset( $matches[1] ) && $matches[1] === $post_title ) {
					//replace the content part of the tag with the WP generated tag title
					$title = str_replace( $post_title, wp_get_document_title(), $title );
				}
			}
			return $title;
		};

	endif;

endif;


// prevent Git Updater errors when using Redis Cache
// see: https://github.com/rhubarbgroup/redis-cache/issues/449
if ( class_exists( 'Fragen\Git_Updater\Ignore' ) ) :
	new Fragen\Git_Updater\Ignore( 'redis-cache', 'redis-cache/redis-cache.php' );
	new Fragen\Git_Updater\Ignore( 'woocommerce-subscriptions-gifting', 'woocommerce-subscriptions-gifting/woocommerce-subscriptions-gifting.php' );
endif;


// Add page slug to body class
// modified from code originally found in: Starkers WordPress Theme - http://www.elliotjaystocks.com/
if ( ! function_exists( 'squarecandy_add_slug_to_body_class' ) ) :
	function squarecandy_add_slug_to_body_class( $classes ) {
		global $post;

		if ( is_home() ) {
			$key = array_search( 'blog', $classes, true );
			if ( $key > -1 ) {
				unset( $classes[ $key ] );
			}
		} elseif ( is_page() ) {
			$classes[] = sanitize_html_class( $post->post_name );
		} elseif ( is_singular() ) {
			$classes[] = sanitize_html_class( $post->post_name );
		}

		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
			$classes[] = 'not-single';
		}
		return $classes;
	}
	add_filter( 'body_class', 'squarecandy_add_slug_to_body_class' );
endif;


// Add browser to body classes
if ( ! function_exists( 'squarecandy_browser_body_class' ) ) :
	function squarecandy_browser_body_class( $classes ) {

		global $is_edge, $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';

		if ( $is_edge ) {
			$classes[] = 'edge';
		}
		if ( $is_lynx ) {
			$classes[] = 'lynx';
		}
		if ( $is_gecko ) {
			$classes[] = 'gecko';
		}
		if ( $is_opera ) {
			$classes[] = 'opera';
		}
		if ( $is_NS4 ) {
			$classes[] = 'ns4';
		}
		if ( $is_safari ) {
			$classes[] = 'safari';
		}
		if ( $is_chrome ) {
			$classes[] = 'chrome';
		}
		if ( $is_IE ) {
			$classes[] = 'ie';
		}
		if ( $is_iphone || stristr( $user_agent, 'iPhone' ) ) {
			$classes[]         = 'iphone';
			$classes['mobile'] = 'mobile';
		}
		if ( stristr( $user_agent, 'iPad' ) ) {
			$classes[]         = 'ipad';
			$classes['mobile'] = 'mobile';
		}
		if ( stristr( $user_agent, 'Android' ) ) {
			$classes[]         = 'android';
			$classes['mobile'] = 'mobile';
		}

		if ( stristr( $user_agent, 'mac' ) ) {
			$classes[] = 'osx';
		} elseif ( stristr( $user_agent, 'linux' ) ) {
			$classes[] = 'linux';
		} elseif ( stristr( $user_agent, 'windows' ) ) {
			$classes[] = 'windows';
		}
		return $classes;
	}

	add_filter( 'body_class', 'squarecandy_browser_body_class' );
endif;

// filter [STAGING] out of blogname so staging sites are identical to production
// NB only works if 2nd param of get_blog_info is set to 'display'
// ( useful for when we run visual regression tests )
if ( ! function_exists( 'squarecandy_staging_blogname' ) ) :
	add_filter( 'bloginfo', 'squarecandy_staging_blogname', 10, 2 );
	function squarecandy_staging_blogname( $name, $show ) {
		if ( 'name' === $show && strpos( $name, '[STAGING]' ) !== false ) {
			$name = trim( str_replace( '[STAGING]', '', $name ) );
		}
		return $name;
	}
endif;

// GIVEWP - ADD THE ABILITY TO FILTER BY & EXPORT THE ANONYMOUS DONATION FIELD

if ( class_exists( 'Give\Donations\DonationsAdminPage' ) ) :

	/**
	* GIVEWP - Add checkbox to select the 'Anonymous Donation' to an export via Donations > Tools > Export > Generate CSV
	*/
	if ( ! function_exists( 'squarecandy_give_export_donation_standard_payment_fields' ) ) :
		add_action( 'give_export_donation_standard_payment_fields', 'squarecandy_give_export_donation_standard_payment_fields' );
		function squarecandy_give_export_donation_standard_payment_fields() {
			?>
			<li>
				<label for="give-export-anonymous-donation">
					<input type="checkbox" checked
						name="give_give_donations_export_option[anonymous_donation]"
						id="give-export-anonymous-donation"><?php _e( 'Anonymous Donation', 'give' ); ?>
				</label>
			</li>
			<?php
		}
	endif;

	/**
	* GIVEWP - Filter to modify Donation CSV data when exporting donation
	* If 'anonymous_donation' export checkbox checked, pull the '_give_anonymous_donation' value for the donation
	*
	* @param array Donation data
	* @param Give_Payment              $payment Instance of Give_Payment
	* @param array                     $columns Donation data $columns that are not being merge
	* @param Give_Export_Donations_CSV $this    Instance of Give_Export_Donations_CSV
	*
	* @return array Donation data
	*/
	if ( ! function_exists( 'squarecandy_give_export_donation_data' ) ) :
		add_filter( 'give_export_donation_data', 'squarecandy_give_export_donation_data', 10, 4 );
		function squarecandy_give_export_donation_data( $data, $payment, $columns, $give ) {

			$data['anonymous_donation'] = ! empty( $payment->payment_meta['_give_anonymous_donation'] ) ? 'Anonymous' : '';
			return $data;
		}
	endif;

	/**
	 * GIVEWP - Filter to get columns name when exporting donation
	 * If 'anonymous_donation' export checkbox checked, customize the title for the column in the csv
	 *
	 * @since 2.1
	 *
	 * @param array $cols    columns name for CSV
	 * @param array $columns columns select by admin to export
	 */
	if ( ! function_exists( 'squarecandy_give_export_donation_get_columns_name' ) ) :
		add_filter( 'give_export_donation_get_columns_name', 'squarecandy_give_export_donation_get_columns_name', 10, 2 );
		function squarecandy_give_export_donation_get_columns_name( $cols, $columns ) {
			if ( isset( $columns['anonymous_donation'] ) ) {
				$cols['anonymous_donation'] = __( 'Anonymous Donation', 'give' );
			}
			return $cols;
		}
	endif;

	// GIVEWP - ADD AN ANONYMOUS DONATION FILTER TO	LIST VIEW

	/**
	 * Payments View.
	 *
	 * On the admin donations list page, adds a link to view only anonymous donations
	 * (based on give_recurring_payments_table_views)
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $views
	 * @param Give_Payment_History_Table $donation_table
	 *
	 * @return array
	 */
	if ( ! function_exists( 'squarecandy_give_anonymous_payments_table_views' ) ) :
		add_filter( 'give_payments_table_views', 'squarecandy_give_anonymous_payments_table_views', 20, 2 );
		function squarecandy_give_anonymous_payments_table_views( $views, $donation_table ) {

			$current = isset( $_GET['anonymous_donation'] ) ? $_GET['anonymous_donation'] : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			// bc of the way give sets up their existing filter links, they can end up with ?anonymous_donation in them too, so filter that out
			if ( '1' === $current ) {
				foreach ( $views as $key => $url ) {
					sqcdy_log( $url, 'views ' . $key );
					$views[ $key ] = str_replace( '&#038;anonymous_donation=1', '', $url );
				}
			}

			//set up a GiveWP query to get the number of anonymous donations
			$args = array(
				'number'     => -1, // Extract all donations
				'meta_query' => array(
					array(
						array(
							'key'   => '_give_anonymous_donation',
							'value' => '1',
						),
					),
				),
			);

			$donations_obj = new Give_Payments_Query( $args );
			$count         = count( $donations_obj->get_payments() );
			$renewal_count = '&nbsp;<span class="count">(' . $count . ')</span>';

			// get a new admin url for our query, first removing other possible parameters from the url
			// (there's prob a better to do this but this is how givewp does it)
			$status_url = add_query_arg(
				array( 'anonymous_donation' => '1' ),
				remove_query_arg(
					array(
						'paged',
						'_wpnonce',
						'_wp_http_referer',
						'donation_type',
						'status',
					)
				)
			);

			$views['anonymous_donation'] = sprintf(
				'<a href="%s"%s>%s</a>',
				esc_url( $status_url ),
				'1' === $current ? ' class="current"' : '',
				__( 'Anonymous', 'give-anonymous' ) . $renewal_count
			);

			return $views;
		}
	endif;

	/**
	 * GIVEWP - On the admin donations list page, add a hidden input for 'anonymous_donation'
	 */
	if ( ! function_exists( 'squarecandy_give_recurring_payment_table_advanced_filters' ) ) :
		add_action( 'give_payment_table_advanced_filters', 'squarecandy_give_recurring_payment_table_advanced_filters' );
		function squarecandy_give_recurring_payment_table_advanced_filters() {
			if ( ! empty( $_GET['anonymous_donation'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				?>
				<input type="hidden" name="anonymous_donation" value="<?php echo give_clean( $_GET['anonymous_donation'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>">
				<?php
			}
		}
	endif;

	/**
	 * Filter to modify Payment table Meta Query
	 * On the admin donations list page, add the value of GET['anonymous_donation'] to the query
	 */
	if ( ! function_exists( 'squarecandy_give_recurring_payment_table_payments_query' ) ) :
		add_filter( 'give_payment_table_payments_query', 'squarecandy_give_recurring_payment_table_payments_query' );
		function squarecandy_give_recurring_payment_table_payments_query( $args ) {
			sqcdy_log( $args, 'luna_give_recurring_payment_table_payments_query' );
			if ( ! empty( $_GET['anonymous_donation'] ) && '1' === give_clean( $_GET['anonymous_donation'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$args['meta_query'] = array(
					array(
						'key'   => '_give_anonymous_donation',
						'value' => '1',
					),
				);
			}
			return $args;
		}
	endif;

	if ( ! function_exists( 'squarecandy_give_payments_table_columns' ) ) :
		add_filter( 'give_payments_table_columns', 'squarecandy_give_payments_table_columns' );
		// GIVEWP - ADD AN ANONYMOUS DONATION COLUMN TO	LIST VIEW
		function squarecandy_give_payments_table_columns( $columns ) {
			$details                       = array_pop( $columns );
			$columns['anonymous_donation'] = __( 'Anonymous', 'give' );
			$columns['details']            = $details;
			return $columns;
		}
	endif;

	if ( ! function_exists( 'squarecandy_give_payments_table_column' ) ) :
		add_filter( 'give_payments_table_column', 'squarecandy_give_payments_table_column', 10, 3 );
		function squarecandy_give_payments_table_column( $value, $payment_id, $column_name ) {
			if ( 'anonymous_donation' === $column_name ) {
				$anon  = give_get_meta( $payment_id, '_give_anonymous_donation', true );
				$value = $anon ? 'Yes' : '';
			}
			return $value;
		}
	endif;
endif; // end GiveWP
