<?php
// Helpful features for GiveWP

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail if GiveWP is not active
if ( ! class_exists( 'Give' ) ) {
	return;
}




// GIVEWP - Data Export Improvements
// ADD THE ABILITY TO FILTER BY & EXPORT THE ANONYMOUS DONATION FIELD
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
