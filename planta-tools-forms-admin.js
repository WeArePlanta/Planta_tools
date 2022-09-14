jQuery( document ).ready( function() {

	var alt_forms_add_slot = function( data ) {

		var last_id		= jQuery( '#alt_forms_container .alt_form_slot:last-child' ).attr( 'id' );
		var last_slot 	= last_id ? parseInt( last_id.replace( 'alt_form_slot_', '' ) ) + 1 : '0';
		var id_suffix	= '_' + last_slot;

		var alt_forms_idx 	= last_slot;
		var alt_form_slot 	= jQuery( '<div/>' )
								.addClass( 'alt_form_slot' )
								.attr( 'id', 'alt_form_slot' + id_suffix );


		alt_form_slot.append( jQuery('<input/>' )
			.addClass( 'alt_forms_id' )
			.attr( 'type', 'text' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][id]' )
			.attr( 'id', 'alt_forms_id' + id_suffix )
			.attr( 'required', 'required' )
			.attr( 'placeholder', alt_forms_i18n.placeholder_id )
			.val( data ? data.id : '' ) );

		alt_form_slot.append( jQuery('<input/>' )
			.addClass( 'alt_forms_submit_url' )
			.attr( 'type', 'text' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][submit_url]' )
			.attr( 'id', 'alt_forms_submit_url' + id_suffix )
			.attr( 'placeholder', alt_forms_i18n.placeholder_submit_url )
			.val( data ? data.submit_url : '' ) );

		alt_form_slot.append( jQuery('<input/>' )
			.addClass( 'alt_forms_post_type' )
			.attr( 'type', 'text' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][post_type]' )
			.attr( 'id', 'alt_forms_post_type' + id_suffix )
			.attr( 'placeholder', alt_forms_i18n.placeholder_post_type )
			.val( data ? data.post_type : '' ) );

		alt_form_slot.append( jQuery('<input/>' )
			.addClass( 'alt_forms_category' )
			.attr( 'type', 'text' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][category]' )
			.attr( 'id', 'alt_forms_category' + id_suffix )
			.attr( 'placeholder', alt_forms_i18n.placeholder_category )
			.val( data ? data.category : '' ) );

		alt_form_slot.append( jQuery('<input/>' )
			.addClass( 'alt_forms_email' )
			.attr( 'type', 'text' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][email]' )
			.attr( 'id', 'alt_forms_email' + id_suffix )
			.attr( 'required', 'required' )
			.attr( 'placeholder', alt_forms_i18n.placeholder_email )
			.val( data ? data.email : '' ) );

		alt_form_slot.append( jQuery('<input/>' )
			.addClass( 'alt_forms_confirmation_email' )
			.attr( 'type', 'text' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][confirmation_email]' )
			.attr( 'id', 'alt_forms_confirmation_email' + id_suffix )
			.attr( 'placeholder', alt_forms_i18n.placeholder_confirmation_email )
			.val( data ? data.confirmation_email : '' ) );

		alt_form_slot.append( jQuery('<input/>' )
			.addClass( 'alt_forms_confirmation_email_subject' )
			.attr( 'type', 'text' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][confirmation_email_subject]' )
			.attr( 'id', 'alt_forms_confirmation_email_subject' + id_suffix )
			.attr( 'placeholder', alt_forms_i18n.placeholder_confirmation_email_subject )
			.val( data ? data.confirmation_email_subject : '' ) );


		alt_form_slot.append( jQuery('<textarea/>' )
			.addClass( 'alt_forms_html' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][html]' )
			.attr( 'id', 'alt_forms_html' + id_suffix )
			.attr( 'required', 'required' )
			.attr( 'placeholder', alt_forms_i18n.placeholder_html )
			.val( data ? data.html : '' ) );

		alt_form_slot.append( jQuery('<textarea/>' )
			.addClass( 'alt_forms_confirmation_html' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][confirmation_html]' )
			.attr( 'id', 'alt_forms_confirmation_html' + id_suffix )
			.attr( 'placeholder', alt_forms_i18n.placeholder_confirmation_html )
			.val( data ? data.confirmation_html : '' ) );

		alt_form_slot.append( jQuery('<textarea/>' )
			.addClass( 'alt_forms_custom_css' )
			.attr( 'name', 'alt_forms_info[' + alt_forms_idx + '][custom_css]' )
			.attr( 'id', 'alt_forms_custom_css' + id_suffix )
			.attr( 'placeholder', alt_forms_i18n.placeholder_custom_css )
			.val( data ? data.custom_css : '' ) );

		alt_form_slot.append( jQuery( '<a/>' )
			.addClass( 'alt_forms_remove_slot' )
			.attr( 'href', '#' )
			.html( alt_forms_i18n.remove )
			.click( function( e ) {
				e.preventDefault();

				jQuery( this ).parent().remove();
			} ) );

		jQuery( '#alt_forms_container' ).append( alt_form_slot );
	};

	jQuery( '#alt_forms_add_slot' ).click( function( e ) {
		e.preventDefault();

		alt_forms_add_slot();
	} );

	/*jQuery( '#alt_forms' ).submit( function( e ) {
		var errors = false;

		if( ! jQuery( '#alt_forms_container .alt_form_slot' ).length )
			return false;

		return ! errors;
	} );*/

	if( "undefined" != typeof( alt_forms_info ) && alt_forms_info )
		for( var i in alt_forms_info )
			alt_forms_add_slot( alt_forms_info[ i ] );
});
