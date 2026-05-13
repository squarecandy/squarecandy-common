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
		}

	endif;

	// SEOPress Redirections
	if ( ! function_exists( 'squarecandy_seopress_metabox_seo_tabs' ) ) :
		//remove seopress redirections tab from edit post metabox (nb if we use block editor will need to do this again somewhere else?)
		add_filter( 'seopress_metabox_seo_tabs', 'squarecandy_seopress_metabox_seo_tabs' );
		function squarecandy_seopress_metabox_seo_tabs( $seo_tabs ) {
			unset( $seo_tabs['redirect-tab'] );
			return $seo_tabs;
		}
	endif;

	if ( ! function_exists( 'squarecandy_seopress_redirections_hook' ) ) :
		//prevent seopress redirections from running
		add_action( 'wp', 'squarecandy_seopress_redirections_hook', 1 );
		function squarecandy_seopress_redirections_hook() {
			remove_action( 'template_redirect', 'seopress_redirections_hook', 1 ); // seopress redirections hook
		}
	endif;

endif;


// prevent Git Updater errors when using Redis Cache
// see: https://github.com/rhubarbgroup/redis-cache/issues/449
if ( class_exists( 'Fragen\Git_Updater\Ignore' ) ) :
	new Fragen\Git_Updater\Ignore( 'redis-cache', 'redis-cache/redis-cache.php' );
	new Fragen\Git_Updater\Ignore( 'woocommerce-subscriptions-gifting', 'woocommerce-subscriptions-gifting/woocommerce-subscriptions-gifting.php' );
	new Fragen\Git_Updater\Ignore( 'woocommerce-name-your-price', 'woocommerce-name-your-price/woocommerce-name-your-price.php' );
	new Fragen\Git_Updater\Ignore( 'woocommerce-mix-and-match-products', 'woocommerce-name-your-price/woocommerce-name-your-price.php' ); //they have incorrect file in their header
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

		// Views2 body class
		if ( sqcdy_is_views2( 'squarecandy' ) ) {
			$classes[] = 'sqcdy-views2';
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

require_once 'sqcdy-base-theme-givewp.php';

// intercept calls to api.wordpress.org/core/browse-happy in wp_check_browser_version()
// and api.wordpress.org/core/serve-happy in wp_check_php_version()
// ... and any other *-happy shenanigans that get added in the future.
if ( ! function_exists( 'squarecandy_stop_wordpress_org_api_calls' ) ) :
	add_filter( 'pre_http_request', 'squarecandy_stop_wordpress_org_api_calls', 10, 3 );
	function squarecandy_stop_wordpress_org_api_calls( $ret, array $request, string $url ) {
		if ( preg_match( '!^https?://api\.wordpress\.org/core/.*-happy/!i', $url ) ) { // phpcs:ignore WordPress.WP.CapitalPDangit

			// set the same transient as wp_check_browser_version() does so it stops checking.
			$key = md5( $_SERVER['HTTP_USER_AGENT'] );
			set_site_transient( 'browser_' . $key, array(), YEAR_IN_SECONDS * 100 ); // set for absurd far future date so it doesn't check again

			// set the same transient as wp_check_php_version() does so it stops checking.
			$version = PHP_VERSION;
			$key     = md5( $version );
			set_site_transient( 'php_check_' . $key, array(), YEAR_IN_SECONDS * 100 ); // set for absurd far future date so it doesn't check again

			return new WP_Error( 'stop_wordpress_org_api_calls', sprintf( 'Request to %s was blocked. We will check again in 100 years!', $url ) );
		}

		return $ret;
	}
endif;


// remove the contain-intrinsic-size: 3000px 1500px declaration forced into WP Core <head> output
remove_action( 'wp_head', 'wp_print_auto_sizes_contain_css_fix', 1 );

// completely disable speculative loading rules introduced in WP 6.5
add_filter(
	'wp_speculation_rules_configuration',
	function ( $config ) {
		// Returning null disables all speculative loading rules.
		return null;
	}
);


// disable Post Types Order plugin drag&drop reordering on regular admin archive pages
if ( ! function_exists( 'squarecandy_pto_options' ) ) :

	add_filter( 'pto/get_options', 'squarecandy_pto_options' );

	function squarecandy_pto_options( $options ) {

		// if this fires before the admin screen function, bail out
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $options;
		}

		$screen           = get_current_screen();
		$is_settings_page = isset( $screen->id ) && 'settings_page_cpto-options' === $screen->id;
		$is_archive_page  = isset( $screen->id ) && 'edit' === $screen->base && isset( $screen->post_type );

		// if we're not in either place, abort
		if ( ! $is_settings_page && ! $is_archive_page ) {
			return $options;
		}

		// check and adjust the cpto options, $options['allow_reorder_default_interfaces'] might be an empty array, or an array of post_types with 'yes' or 'no'
		if ( isset( $options['allow_reorder_default_interfaces'] ) && is_array( $options['allow_reorder_default_interfaces'] ) ) {

			$override_settings = true;
			$count             = count( $options['allow_reorder_default_interfaces'] );

			// check if we have customized settings in the db (i.e. not all the same) we want to keep those if so
			if ( $count ) {
				$unique = array_unique( $options['allow_reorder_default_interfaces'] );
				if ( count( $unique ) > 1 ) {
					$override_settings = false;
				}
			}

			// otherwise set the option(s) to 'no'
			if ( $override_settings ) {
				if ( $is_archive_page ) {
					// on the archive page we just need to set this one to no
					$options['allow_reorder_default_interfaces'][ $screen->post_type ] = 'no';
				} else {
					// on the settings page set everything to no so if we want to customize we can just turn on the ones we want
					$post_types = $count ? array_keys( $options['allow_reorder_default_interfaces'] ) : get_post_types();
					foreach ( $post_types as $post_type ) {
						$options['allow_reorder_default_interfaces'][ $post_type ] = 'no';
					}
				}
			}
		}
		return $options;
	}
endif;


/* Heartbeat API Fixes */

if ( ! function_exists( 'squarecandy_heartbeat_settings' ) ) :
	// Control WordPress Heartbeat API to prevent excessive admin-ajax.php requests
	function squarecandy_heartbeat_settings( $settings ) {
		// Slow down heartbeat to every 60 seconds (default is 15)
		$settings['interval'] = 60;
		return $settings;
	}
	add_filter( 'heartbeat_settings', 'squarecandy_heartbeat_settings', PHP_INT_MAX - 10 );
endif;

if ( ! function_exists( 'squarecandy_disable_heartbeat' ) ) :
	// Disable heartbeat on all post list screens where it's not needed
	function squarecandy_disable_heartbeat() {
		global $pagenow;
		// Disable on all post/page/CPT list screens - heartbeat only needed when editing individual posts
		if ( 'edit.php' === $pagenow || 'edit-tags.php' === $pagenow ) {
			wp_deregister_script( 'heartbeat' );
		}
	}
	add_action( 'admin_enqueue_scripts', 'squarecandy_disable_heartbeat', 999 );
endif;

// Disable SearchWP's admin bar heartbeat integration on all wp-admin pages
// (Note: The 10-second heartbeat interval on post.php is from WordPress core's post lock detection, not SearchWP)
add_filter( 'searchwp\admin_bar', '__return_false' );

if ( ! function_exists( 'squarecandy_heartbeat_autostop' ) ) :
	// Stop heartbeat when session expires to prevent 400 errors from stale tabs
	function squarecandy_heartbeat_autostop() {
		?>
		<script>
		(function($) {
			$(document).ready(function() {
				// Override WordPress core's aggressive 10-second post lock heartbeat
				// Wait for first heartbeat-tick to ensure post.js has executed and set its interval
				if (typeof wp !== 'undefined' && wp.heartbeat) {
					$(document).one('heartbeat-tick', function() {
						var currentInterval = wp.heartbeat.interval();
						if (currentInterval < 40) {
							wp.heartbeat.interval(40);
							console.log('Overriding WordPress core heartbeat from ' + currentInterval + 's to 40s for better server performance');
						}
					});
				}

				// Watch for session expiration by monitoring when wp-auth-check-wrap becomes visible
				// WordPress changes the modal from display:none to display:block when session expires
				var $modal = $('#wp-auth-check-wrap');
				if ($modal.length) {
					var visibilityObserver = new MutationObserver(function(mutations) {
						mutations.forEach(function(mutation) {
							if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
								var $target = $(mutation.target);
								if ($target.is(':visible') && $target.css('display') !== 'none') {
									if (typeof wp !== 'undefined' && wp.heartbeat) {
										wp.heartbeat.interval(86400);
										console.log('User got logged out (login modal became visible). Heartbeat slowed to 24 hours ("stop" functionality not available).');
									}
								}
							}
						});
					});
					visibilityObserver.observe($modal[0], { attributes: true });
				}
			});
		})(jQuery);
		</script>
		<?php
	}
	add_action( 'admin_footer', 'squarecandy_heartbeat_autostop' );
endif;


