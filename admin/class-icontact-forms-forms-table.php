<?php
/**
 * The forms menu page table functionality of the plugin.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/admin
 */

namespace Icontact\Icontact_Forms;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * The forms menu page functionality of the plugin.
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/admin
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Forms_Forms_Table extends \WP_List_Table {
	/**
	 * Data filtered for pagination or search
	 *
	 * @var array
	 */
	private $found_data = array();

	/**
	 * Full array of data found to build the table list
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * WordPress table standard construct plus data preparation for the table.
	 *
	 * @param boolean $is_api_connected API wrapper bool to know if user is connected or not to the api.
	 */
	public function __construct( bool $is_api_connected ) {
		parent::__construct(
			array(
				'singular' => __( 'form', 'mylisttable' ), // Singular name of the listed records.
				'plural'   => __( 'forms', 'mylisttable' ), // Plural name of the listed records.
				'ajax'     => true, // Does this table support ajax?
			)
		);

		if ( $is_api_connected ) {
			$forms_json = apply_filters( ICONTACT_FORMS_OPTION_PREFIX . 'get_form_index', array() );
		} else {
			$forms_json = array();
		}

		if ( isset( $_POST['search_nonce'] ) ) {
			$wpnonce = sanitize_text_field( wp_unslash( $_POST['search_nonce'] ) );
		}

		/**
		 * We process all the $_POST data only on top-menu to not process twice the same $_POST action
		 * since top-menu always is the first view called since is a special one.
		 */
		if ( ! empty( $wpnonce ) && wp_verify_nonce( $wpnonce, 'search_nonce' ) ) {
			if ( ! empty( $_POST['s'] ) ) {
				$search_string = sanitize_text_field( wp_unslash( $_POST['s'] ) );
				$filtered_arr  = array();
				foreach ( $forms_json as $form ) {
					if ( stristr( $form['name'], $search_string ) ) {
						array_push( $filtered_arr, $form );
					}
				}
				$forms_json = $filtered_arr;
			}
		}
		$this->data = $forms_json;
	}

	/**
	 * Show no items message
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No forms found.', 'icontact-forms' );
	}
	/**
	 * Default columns output process
	 *
	 * @param array  $item Array of columns.
	 * @param string $column_name Column name.
	 * @return string Column output.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'updated':
				return wp_date( 'M d, Y', strtotime( $item['updated'] ) );
			case 'lists':
			default:
				return htmlentities2( $item[ $column_name ] );
		}
	}

	/**
	 * WP Table get sortable columns.
	 *
	 * @return array Array of sortable columns.
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name'    => array( 'name', true ),
			'updated' => array( 'sortby', 'asc' ),
		);
		return $sortable_columns;
	}

	/**
	 * WP Table get columns name.
	 *
	 * @return array Array of column names.
	 */
	public function get_columns() {
		$columns = array(
			'name'      => __( 'Form name', 'mylisttable' ),
			'updated'   => __( 'Updated On', 'mylisttable' ),
			'shortcode' => __( 'Shortcode', 'mylisttable' ),
			'lists'     => __( 'Associated list(s)', 'mylisttable' ),
		);
		return $columns;
	}

	/**
	 * Return sorted array
	 *
	 * @param array $a Current table element to sort.
	 * @param array $b Next table element to sort.
	 * @return string Sort order
	 */
	public function usort_reorder( $a, $b ) {
		// phpcs:disable
		// There was no way to send wp nonce on the order by button with current table class, but it's safe since is just for decide the order.
		// If no sort, default to title.

		$orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'updated';
		$orderby = ( isset( $a[ $orderby ] ) && isset( $b[ $orderby ] ) ) ? $orderby : 'updated';

		// If no order, default to asc.
		$order = ( ! empty( $_GET['order'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'desc';
		// phpcs:enable

		// Determine sort order.
		if ( 'updated' === $orderby ) {
			$result = strtotime( $b[ $orderby ] ) - strtotime( $a[ $orderby ] );
		} else {
			$result = strnatcasecmp( (string) $b[ $orderby ], (string) $a[ $orderby ] );
		}

		// Send final sort direction to usort.
		return ( 'desc' === $order ) ? $result : -$result;
	}

	/**
	 * Add Edit and Preview links to each result on name column
	 *
	 * @param array $item Array with the item information.
	 * @return String Modified HTML output of with Edit and Preview links integrated on it.
	 */
	public function column_name( $item ) {
		// Build the ajax URL of the form.
		$form_url = sprintf(
			'%s?action=%sform_output&_wpnonce=%s&%s',
			admin_url( 'admin-ajax.php' ),
			ICONTACT_FORMS_OPTION_PREFIX,
			wp_create_nonce(),
			http_build_query( $item )
		);
		$actions  = array(
			'Edit'    => sprintf( '<a href="%s/core/mycontacts/signup/designer?formId=%s" target="_blank"><span class="dashicons dashicons-external"></span>Edit</a>', ICONTACT_APIURL, esc_attr( $item['formid'] ) ),
			'Preview' => sprintf( '<a href="%s#TB_ajax?" class="thickbox" id="%s"><span class="dashicons dashicons-visibility"></span>Preview</a>', esc_attr( $form_url ), esc_attr( $item['formid'] ) ),
		);
		return sprintf( '%1$s %2$s', '<b>' . htmlentities2( $item['name'] ) . '</b>', $this->row_actions( $actions ) );
	}

	/**
	 * Modify the output of the associated list columns to show the lists stacked in an ul li element.
	 *
	 * @param array $item Array with the item information.
	 * @return string Modified HTML output.
	 */
	public function column_lists( $item ) {
		$output = '<ul>';
		foreach ( $item['lists'] as $key => $value ) {
			$output .= '<li>' . htmlentities2( $value ) . '</li>';
		}
		$output .= '</ul>';
		return $output;
	}

	/**
	 * Modify output of the column Shortcode.
	 *
	 * @param array $item Array with the item information.
	 * @return string Modified HTML output of the item to show Shortcode box.
	 */
	public function column_shortcode( $item ) {
		$shortocde_tag = esc_attr( sprintf( '[icform formid="%s" cfid="%s" cid="%s"]', $item['formid'], $item['cfid'], $item['cid'] ) );
		$form_id       = esc_attr( $item['formid'] );
		return sprintf( '<input id="form-%s" type="text" value="%s" disabled><button type="button" name="copy-button" id="%s">Copy</button>', $form_id, $shortocde_tag, $form_id );
	}

	/**
	 * Default WP Table function to prepare the information to build the table.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->data, array( &$this, 'usort_reorder' ) );

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = count( $this->data );

		// Only necessary because we have sample data.
		$this->found_data = array_slice( $this->data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // WE have to calculate the total number of items.
				'per_page'    => $per_page,     // WE have to determine how many items to show on a page.
			)
		);
		$this->items = $this->found_data;
	}
}
