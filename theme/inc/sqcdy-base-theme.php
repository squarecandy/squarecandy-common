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
			$seopress_titles_desc = trim( $seopress_titles_desc, " \n\r\t\v\x00'“”\"" ); //trim unbalanced quotes etc - should we still do this?

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
