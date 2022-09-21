<?php
/**
 * Plugin Name: Planta Tools
 * Version:     1.0.7
 * Description: Customizaciones para el sitio weareplanta.com
 * Author:      Natalia Ciraolo 
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

define( 'PLANTA_TOOLS_VERSION', '1.0.7' );
define( 'PLANTA_TOOLS_FORMS_OPTION_NAME', 'planta_tools_forms_option' );
define( 'PLANTA_TOOLS_FORMS_NONCE', 'planta_tools_nonce' );


add_action( 'init', function() {

	wp_enqueue_script( 
		'copy_to_clipboard_script', 
		'/wp-content/plugins/planta-tools/js/copy-to-clipboard.js', 
		array(), 
		PLANTA_TOOLS_VERSION, 
		true 
	);
	
	wp_enqueue_style( 
		'copy_to_clipboard_style', 
		'/wp-content/plugins/planta-tools/css/copy-to-clipboard.css', 
		array(), 
		PLANTA_TOOLS_VERSION
	);
} );



add_action(
	'wp_enqueue_scripts',
	function() {
		if ( ! wp_script_is( 'planta-tools-forms-ajax', 'registered' ) ) {
			wp_register_script( 'planta-tools-forms-ajax', WP_CONTENT_URL . '/plugins/planta-tools/planta-tools-forms-ajax.js', array(), PLANTA_TOOLS_VERSION, true );
		}

		wp_register_script( 'planta-tools-forms-js', WP_CONTENT_URL . '/plugins/planta-tools/form.js', array( 'planta-tools-forms-ajax' ), PLANTA_TOOLS_VERSION, true );
	}
);

add_action(
	'admin_enqueue_scripts',
	function( $hook ) {
		if ( 'toplevel_page_planta_tools_forms_settings' === $hook ) {
			wp_enqueue_style( 'planta-tools-forms', WP_CONTENT_URL . '/plugins/planta-tools/planta-tools-forms.css', array(), PLANTA_TOOLS_VERSION );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );

			wp_enqueue_script( 'planta-tools-forms', WP_CONTENT_URL . '/plugins/planta-tools/planta-tools-forms-admin.js', array( 'jquery-ui-dialog' ), PLANTA_TOOLS_VERSION, false );
			wp_enqueue_script( 'jquery-ui-dialog' );

			wp_localize_script(
				'planta-tools-forms',
				'planta_tools_forms_i18n',
				array(
					'error_name'                     => __( 'Complete the required fields', 'planta-tools-forms' ),
					'placeholder_category'           => __( 'Category', 'planta-tools-forms' ),
					'placeholder_confirmation_email_subject' => __( 'Confirmation email subject', 'planta-tools-forms' ),
					'placeholder_confirmation_email' => __( 'Confirmation email receiver (input name)', 'planta-tools-forms' ),
					'placeholder_confirmation_html'  => __( 'Confirmation mail HTML', 'planta-tools-forms' ),
					'placeholder_custom_css'         => __( 'Confirmation mail CSS', 'planta-tools-forms' ),
					'placeholder_email'              => __( 'Email receiver (required)', 'planta-tools-forms' ),
					'placeholder_html'               => __( 'HTML form (required)', 'planta-tools-forms' ),
					'placeholder_id'                 => __( 'ID (required)', 'planta-tools-forms' ),
					'placeholder_post_type'          => __( 'Post Type', 'planta-tools-forms' ),
					'placeholder_submit_url'         => __( 'Submit URL', 'planta-tools-forms' ),
					'remove'                         => __( 'Remove', 'planta-tools-forms' ),
				)
			);
		}
	}
);

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			'Planta Tools Forms',
			'Planta Tools Forms',
			'manage_options',
			'planta_tools_forms_settings',
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'planta-tools-forms' ) );
				}

				$message = '';

				if ( ! empty( $_POST ) && check_admin_referer( 'planta_tools_forms', PLANTA_TOOLS_FORMS_NONCE ) ) {
					$message = planta_tools_forms_save_data();
				}

				$planta_tools_forms_info = get_option( PLANTA_TOOLS_FORMS_OPTION_NAME );
				?>
		<div class="wrap">
				<?php if ( $message ) { ?>
			<div class="updated"><p><?php echo esc_html( $message ); ?></p></div>
			<?php } ?>
			<h2><?php esc_html_e( 'Planta Tools Forms', 'planta-tools-forms' ); ?></h2>
			<form method="post" id="planta_tools_forms">
				<a href="#" class="button-secondary" id="planta_tools_forms_add_slot"><?php echo esc_html__( 'Add New Planta Tools Form Slot', 'planta-tools-forms' ); ?></a>
				<div id="planta_tools_forms_container"></div>
				<script type="text/javascript">var planta_tools_forms_info = <?php echo wp_json_encode( $planta_tools_forms_info ); ?>;</script>
				<?php submit_button(); ?>
				<?php wp_nonce_field( 'planta_tools_forms', PLANTA_TOOLS_FORMS_NONCE ); ?>
			</form>
		</div>

		<div class="wrap planta-tools-forms-export">
			<h2>Exportar Form</h2>
			<p>Seleccione el formulario a exportar</p>

			<form method="post">
				<?php wp_nonce_field( 'planta_tools_form_export_form', 'planta_tools_form_export_form_nonce' ); ?>
				<select name="planta_tools_form_export_post" required>
				<?php
				$form_with_post_types = planta_tools_forms_get_forms_with_post_types();

				if ( ! empty( $form_with_post_types ) ) {
					foreach ( $form_with_post_types as $form_with_post_type ) {
						$form_post_type = sanitize_title( $form_with_post_type->post_type );
						?>
					<option value="<?php echo esc_attr( $form_post_type ); ?>"><?php echo esc_html( $form_with_post_type->post_type ); ?></option>
						<?php
					}
				}
				?>
				</select>
				<input type="submit" class="button-secondary" id="planta_tools_form_export_form" value="Exportar">
			</form>
		</div>
				<?php
			}
		);
	}
);

/**
 * This functions saves the WP Admin settings in an option. The option contains
 * an array of form slot objects.
 *
 * @return string Message explaining the result of the process.
 */
function planta_tools_forms_save_data() {
	if ( ! isset( $_POST[ PLANTA_TOOLS_FORMS_NONCE ] ) ) {
		return __( 'Missing Nonce', 'planta-tools-forms' );
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ PLANTA_TOOLS_FORMS_NONCE ] ) ), 'planta_tools_forms' ) ) {
		return __( 'Invalid Nonce', 'planta-tools-forms' );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return __( 'You have not permissions', 'planta-tools-forms' );
	}

	$planta_tools_forms_info = array();

	if ( ! empty( $_POST['planta_tools_forms_info'] ) ) {
		$forms      = array_values( $_POST['planta_tools_forms_info'] );
		$form_count = count( $forms );

		for ( $i = 0; $i < $form_count; $i++ ) {
			$form = $forms[ $i ];

			if ( ! empty( sanitize_text_field( $form['id'] ) ) &&
				! empty( wp_kses_post( $form['html'] ) )
			) {
				$form_slot = new StdClass();

				$form_slot->id                         = sanitize_text_field( $form['id'] );
				$form_slot->html                       = wp_kses_post( $form['html'] );
				$form_slot->submit_url                 = sanitize_text_field( $form['submit_url'] );
				$form_slot->post_type                  = sanitize_text_field( $form['post_type'] );
				$form_slot->category                   = sanitize_text_field( $form['category'] );
				$form_slot->email                      = sanitize_text_field( $form['email'] );
				$form_slot->confirmation_email         = sanitize_text_field( $form['confirmation_email'] );
				$form_slot->confirmation_email_subject = sanitize_text_field( $form['confirmation_email_subject'] );
				$form_slot->confirmation_html          = wp_kses_post( $form['confirmation_html'] );
				$form_slot->custom_css                 = sanitize_text_field( $form['custom_css'] );

				$planta_tools_forms_info[] = $form_slot;
			}
		}
	}

	update_option( PLANTA_TOOLS_FORMS_OPTION_NAME, $planta_tools_forms_info );

	return __( 'Planta Tools Forms Settings Updated', 'planta-tools-forms' );
}

add_shortcode(
	'planta_tools_form',
	function ( $atts ) {
		wp_enqueue_script( 'planta-tools-forms-js' );

		$html_form = '';
		$form_slot = null;

		$atts = shortcode_atts(
			array(
				'id' => null,
			),
			$atts
		);

		if ( $atts['id'] ) {
			$form_slot = planta_tools_forms_get_form_slot_by_id( $atts['id'] );
		}

		if ( $form_slot ) {
			$html_form = planta_tools_forms_generate_form( $form_slot );
		}

		return $html_form;
	}
);

/**
 * Returns the form slot object that matches with the given ID.
 *
 * @param string $id Alphanumeric ID set by the user.
 *
 * @return null|Object Form slot object or null in case of failure.
 */
function planta_tools_forms_get_form_slot_by_id( $id ) {
	$planta_tools_forms_info = get_option( PLANTA_TOOLS_FORMS_OPTION_NAME );
	$planta_tools_form       = null;

	if ( ! empty( $planta_tools_forms_info ) ) {
		foreach ( $planta_tools_forms_info as $planta_tools_form_slot ) {
			if ( $planta_tools_form_slot->id === $id ) {
				$planta_tools_form = $planta_tools_form_slot;
			}
		}
	}

	return $planta_tools_form;
}

/**
 * Prints the form in the Front End for visitors to fill and submit it.
 *
 * @param Object $form_slot StdClass with the form settings from the WP Option.
 *
 * @return string HTML string with the form.
 */
function planta_tools_forms_generate_form( $form_slot ) {
	$html   = '';
	$action = $form_slot->submit_url ? $form_slot->submit_url : '';

	if ( ! empty( $form_slot->custom_css ) ) {
		$html .= '<style>' . esc_attr( $form_slot->custom_css ) . '</style>';
	}

	$html .= '<form id="planta-tools-form-id-' . esc_attr( strtolower( $form_slot->id ) ) . '" class="planta-tools-forms-ajax-form" ';
	$html .= 'method="post" action="' . esc_attr( $action ) . '">';
	$html .= '<input type="hidden" name="planta-tools-form-id" value="' . esc_attr( $form_slot->id ) . '">';
	$html .= '<input type="hidden" name="planta-tools-form-ajax" value="' . esc_attr( admin_url( 'admin-ajax.php' ) ) . '">';
	$html .= wp_nonce_field( 'planta_tools_forms_ajax_submit_form', 'planta_tools_forms_submit_form_nonce', true, false );
	$html .= wp_kses_post( $form_slot->html );
	$html .= '<p class="planta-tools-form-response-message"></p>';
	$html .= '</form>';

	return $html;
}

/**
 * Returns all the form slot objects with post types defined in the WP Admin
 * settings.
 *
 * @return Array Array with the Post Slots that have an associated post type.
 */
function planta_tools_forms_get_forms_with_post_types() {
	$planta_tools_forms_info      = get_option( PLANTA_TOOLS_FORMS_OPTION_NAME );
	$planta_tools_form_post_types = array();

	if ( ! empty( $planta_tools_forms_info ) ) {
		foreach ( $planta_tools_forms_info as $planta_tools_form_slot ) {
			if ( $planta_tools_form_slot->post_type ) {
				$planta_tools_form_post_types[] = $planta_tools_form_slot;
			}
		}
	}

	return $planta_tools_form_post_types;
}

add_action(
	'init',
	function () {
		$form_with_post_types = planta_tools_forms_get_forms_with_post_types();

		if ( ! empty( $form_with_post_types ) ) {
			foreach ( $form_with_post_types as $form_with_post_type ) {
				$form_post_type = sanitize_title( $form_with_post_type->post_type );

				if ( ! post_type_exists( $form_post_type ) ) {
					register_post_type(
						$form_post_type,
						array(
							'label'        => sanitize_text_field( $form_with_post_type->post_type ),
							'show_ui'      => true,
							'public'       => true,
							'has_archive'  => true,
							'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
							'taxonomies'   => array( 'category' ),
							'capabilities' => array(
								'edit_post'          => 'manage_options',
								'read_post'          => 'manage_options',
								'delete_post'        => 'manage_options',
								'edit_posts'         => 'manage_options',
								'edit_others_posts'  => 'manage_options',
								'delete_posts'       => 'manage_options',
								'publish_posts'      => 'manage_options',
								'read_private_posts' => 'manage_options',
							),
						)
					);
				}
			}
		}
	}
);

/**
 * This functions takes a string formatted with a function of the likes of
 * 'sanitize_title' and makes it more like normal human written text.
 *
 * @param string $text The string to modify.
 *
 * @return string Modified string.
 */
function planta_tools_forms_make_human_readable( $text ) {
	$text = preg_replace( '/[-_]/', ' ', $text );
	$text = ucwords( $text );

	return $text;
}

add_action( 'wp_ajax_planta_tools_forms_submit_form', 'planta_tools_forms_ajax_submit_form' );
add_action( 'wp_ajax_nopriv_planta_tools_forms_submit_form', 'planta_tools_forms_ajax_submit_form' );

/**
 * This function is hooked to wp_ajax and wp_ajax_nopriv. It processes the
 * submissions of forms previously set up in the WP Admin. It sends a json
 * with the result of the process.
 */
function planta_tools_forms_ajax_submit_form() {
	$response  = false;
	$form_slot = null;
	$html_data = '';

	if ( isset( $_POST['planta_tools_forms_submit_form_nonce'] ) &&
		wp_verify_nonce(
			sanitize_text_field(
				wp_unslash( $_POST['planta_tools_forms_submit_form_nonce'] )
			),
			'planta_tools_forms_ajax_submit_form'
		)
	) {
		$form_id = isset( $_POST['planta-tools-form-id'] ) ? strval( sanitize_text_field( wp_unslash( $_POST['planta-tools-form-id'] ) ) ) : null;

		if ( $form_id ) {
			$form_slot = planta_tools_forms_get_form_slot_by_id( $form_id );
		}

		if ( $form_slot ) {

			if ( ! empty( $_POST ) ) {
				$form_data = wp_json_encode( $_POST );

				foreach ( $_POST as $form_field_name => $form_field_value ) {

					if ( $form_field_name !== 'planta-tools-form-ajax'
					&& $form_field_name !== 'planta_tools_forms_submit_form_nonce' 
					&& $form_field_name !== '_wp_http_referer'
					&& $form_field_name !== 'action' ) {
						
						$html_data .= '<p><strong>' . sanitize_text_field( planta_tools_forms_make_human_readable( $form_field_name ) ) . ': </strong> ';
	
						if ( is_array( $form_field_value ) ) {
							$html_data .= sanitize_text_field( implode( ',', $form_field_value ) ) . '</p>';
						} else {
							$html_data .= sanitize_text_field( $form_field_value ) . '</p>';
						}
					}

				}
				
				$form_post_type = sanitize_title( $form_slot->post_type );

				$form_data = str_replace( '\\', '\\\\', $form_data );

				if ( $form_post_type ) {
					$post_insertion = wp_insert_post(
						array(
							'post_title'   => sanitize_text_field( $form_slot->post_type ) . ' Entry',
							'post_content' => wp_kses_post( $html_data ),
							'post_excerpt' => sanitize_text_field( $form_data ),
							'post_type'    => sanitize_text_field( $form_post_type ),
							'post_status'  => 'draft',
						)
					);
				} else {
					$post_insertion = false;
				}

				if ( ! empty( $_FILES ) && $post_insertion ) {
					$attachment_urls = array();

					if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
						require_once ABSPATH . 'wp-admin/includes/image.php';
						require_once ABSPATH . 'wp-admin/includes/file.php';
						require_once ABSPATH . 'wp-admin/includes/media.php';
					}

					foreach ( $_FILES as $file => $array ) {
						if ( isset( $_FILES[ $file ]['error'] ) && UPLOAD_ERR_OK === $_FILES[ $file ]['error'] ) {
							$attachment_id     = media_handle_upload( $file, $post_insertion );
							$attachment_urls[] = wp_get_attachment_url( $attachment_id );
						}
					}
				}

				if ( $attachment_urls ) {
					foreach ( $attachment_urls as $attachment_url ) {
						if ( false === strpos( get_post_mime_type( $attachment_id ), 'image' ) ) {
							$link_content = 'Media Attachment Link';
						} else {
							$link_content = '<img src="' . esc_url( $attachment_url ) . '">';
						}

						$html_data .= '<br><a href="' . esc_url( $attachment_url ) . '">' . $link_content . '</a>';
					}

					wp_update_post(
						array(
							'ID'           => $post_insertion,
							'post_content' => wp_kses_post( $html_data ),
						)
					);
				}

				$headers = array(
					'Content-Type: text/html; charset=UTF-8',
					'From: ' . get_bloginfo( 'name' ) . ' <admin@mail.com>',
				);

				$mail_sending = wp_mail( $form_slot->email, $form_slot->id, $html_data, $headers );

				$user_email = ! empty( $form_slot->confirmation_email ) ? $form_slot->confirmation_email : null;

				if ( ! empty( $user_email ) ) {
					$confirmation_email = ! empty( $_POST[ $user_email ] ) ? sanitize_text_field( wp_unslash( $_POST[ $user_email ] ) ) : null;
				}

				$confirmation_email_subject = ! empty( $form_slot->confirmation_email_subject ) ? $form_slot->confirmation_email_subject : null;

				$confirmation_email_html = ! empty( $form_slot->confirmation_html ) ? $form_slot->confirmation_html : null;

				if ( $confirmation_email &&
					$confirmation_email_html &&
					$confirmation_email_subject
				) {
					wp_mail( $confirmation_email, $confirmation_email_subject, $confirmation_email_html, $headers );
				}

				if ( $post_insertion || $mail_sending ) {
					$response = true;
				}
			}
		}
	}

	do_action( 'planta_tools_forms_after_form_data_processing' );

	if ( $response ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}

add_action( 'admin_init', 'planta_tools_forms_report_export_headers' );

/**
 * This functions hooks to the admin_init to check if the forms are trying to
 * be exported, in which case it calls planta_tools_form_export_form().
 */
function planta_tools_forms_report_export_headers() {
	if ( isset( $_POST['planta_tools_form_export_form_nonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['planta_tools_form_export_form_nonce'] ) ), 'planta_tools_form_export_form' ) ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		if ( ! empty( $_POST['planta_tools_form_export_post'] ) && post_type_exists( $_POST['planta_tools_form_export_post'] ) ) {
			planta_tools_form_export_form( sanitize_text_field( wp_unslash( $_POST['planta_tools_form_export_post'] ) ) );
		}
	}
}

/**
 * Sends the headers for an xls export andthe  contents of the stored forms
 * submissions in a HTML table format.
 *
 * @param string $post_type Post type key (name).
 */
function planta_tools_form_export_form( $post_type ) {
	$query = new WP_Query(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 100,
			'order'          => 'asc',
		)
	);

	$i = 0;

	header( 'Content-Encoding: UTF-8' );
	header( 'Content-Type: application/vnd.ms-excel; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename= ' . planta_tools_forms_make_human_readable( $post_type ) . ' ' . date( 'jS \of F Y' ) . '.xls' );
	echo "\xEF\xBB\xBF"; // UTF-8 BOM.

	echo '<html><body><table>';

	$html      = '';
	$html_head = '';

	if ( ! empty( $query->posts ) ) {
		foreach ( $query->posts as $post ) {
			$entry = (array) json_decode( $post->post_excerpt );

			$html .= '<tr>';

			foreach ( $entry as $key => $value ) {
				if ( 0 === $i ) {
					$html_head .= '<th>' . $key . '</th>';
				}

				$string_value = is_array( $value ) ? implode( '<br>', $value ) : $value;
				$html        .= '<td>' . $string_value . '</td>';
			}

			$html .= '<tr>';

			$i++;
		}
	}

	echo '<tr>' . wp_kses_post( $html_head ) . '</tr>';
	echo wp_kses_post( $html );

	echo '</table></body></html>';
	die();
}

add_action(
	'after_setup_theme',
	function() {
		add_filter(
			'wp_kses_allowed_html',
			function( $allowed, $context ) {
				if ( 'post' === $context ) {
					$allowed['select'] = array(
						'name'     => true,
						'id'       => true,
						'required' => true,
					);

					$allowed['option'] = array(
						'value' => true,
						'disabled' => true,
						'selected' => true,
						'hidden' => true,
					);

					$allowed['textarea'] = array(
						'name'        => true,
						'placeholder' => true,
						'id'          => true,
						'class'       => true,
						'required'    => true,
					);

					$allowed['input'] = array(
						'type'        => true,
						'name'        => true,
						'placeholder' => true,
						'value'       => true,
						'id'          => true,
						'class'       => true,
						'checked'     => true,
						'required'    => true,
						'size'        => true,
					);

					$allowed['script'] = array(
						'type' => true,
						'src'  => true,
					);

					$allowed['link'] = array(
						'rel'  => true,
						'href' => true,
					);

					$allowed['style']             = true;

					$allowed['div']               = array();
					$allowed['div']['class']      = true;
					$allowed['div']['data-value'] = true;
					$allowed['div']['data-mask']  = true;
					$allowed['div']['id']         = true;
					$allowed['div']['onclick']    = true;
					$allowed['div']['style']      = true;

					$allowed['a']['download'] = true;

					$allowed['iframe']                = array();
					$allowed['iframe']['class']       = true;
					$allowed['iframe']['width']       = true;
					$allowed['iframe']['height']      = true;
					$allowed['iframe']['frameborder'] = true;
					$allowed['iframe']['src']         = true;
					$allowed['iframe']['scrolling']   = true;
				}

				return $allowed;
			},
			10,
			2
		);
	}
);
