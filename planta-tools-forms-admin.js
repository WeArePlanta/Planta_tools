jQuery( document ).ready( function() {

	var planta_tools_forms_add_slot = function( data ) {

		var last_id		= jQuery( '#planta_tools_forms_container .planta_tools_form_slot:last-child' ).attr( 'id' );
		var last_slot 	= last_id ? parseInt( last_id.replace( 'planta_tools_form_slot_', '' ) ) + 1 : '0';
		var id_suffix	= '_' + last_slot;

		var planta_tools_forms_idx 	= last_slot;
		var planta_tools_form_slot 	= jQuery( '<div/>' )
								.addClass( 'planta_tools_form_slot' )
								.attr( 'id', 'planta_tools_form_slot' + id_suffix );


		planta_tools_form_slot.append( jQuery('<input/>' )
			.addClass( 'planta_tools_forms_id' )
			.attr( 'type', 'text' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][id]' )
			.attr( 'id', 'planta_tools_forms_id' + id_suffix )
			.attr( 'required', 'required' )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_id )
			.val( data ? data.id : '' ) );

		planta_tools_form_slot.append( jQuery('<input/>' )
			.addClass( 'planta_tools_forms_submit_url' )
			.attr( 'type', 'text' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][submit_url]' )
			.attr( 'id', 'planta_tools_forms_submit_url' + id_suffix )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_submit_url )
			.val( data ? data.submit_url : '' ) );

		planta_tools_form_slot.append( jQuery('<input/>' )
			.addClass( 'planta_tools_forms_post_type' )
			.attr( 'type', 'text' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][post_type]' )
			.attr( 'id', 'planta_tools_forms_post_type' + id_suffix )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_post_type )
			.val( data ? data.post_type : '' ) );

		planta_tools_form_slot.append( jQuery('<input/>' )
			.addClass( 'planta_tools_forms_category' )
			.attr( 'type', 'text' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][category]' )
			.attr( 'id', 'planta_tools_forms_category' + id_suffix )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_category )
			.val( data ? data.category : '' ) );

		planta_tools_form_slot.append( jQuery('<input/>' )
			.addClass( 'planta_tools_forms_email' )
			.attr( 'type', 'text' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][email]' )
			.attr( 'id', 'planta_tools_forms_email' + id_suffix )
			.attr( 'required', 'required' )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_email )
			.val( data ? data.email : '' ) );

		planta_tools_form_slot.append( jQuery('<input/>' )
			.addClass( 'planta_tools_forms_confirmation_email' )
			.attr( 'type', 'text' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][confirmation_email]' )
			.attr( 'id', 'planta_tools_forms_confirmation_email' + id_suffix )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_confirmation_email )
			.val( data ? data.confirmation_email : '' ) );

		planta_tools_form_slot.append( jQuery('<input/>' )
			.addClass( 'planta_tools_forms_confirmation_email_subject' )
			.attr( 'type', 'text' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][confirmation_email_subject]' )
			.attr( 'id', 'planta_tools_forms_confirmation_email_subject' + id_suffix )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_confirmation_email_subject )
			.val( data ? data.confirmation_email_subject : '' ) );


		planta_tools_form_slot.append( jQuery('<textarea/>' )
			.addClass( 'planta_tools_forms_html' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][html]' )
			.attr( 'id', 'planta_tools_forms_html' + id_suffix )
			.attr( 'required', 'required' )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_html )
			.val( data ? data.html : '' ) );

		planta_tools_form_slot.append( jQuery('<textarea/>' )
			.addClass( 'planta_tools_forms_confirmation_html' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][confirmation_html]' )
			.attr( 'id', 'planta_tools_forms_confirmation_html' + id_suffix )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_confirmation_html )
			.val( data ? data.confirmation_html : '' ) );

		planta_tools_form_slot.append( jQuery('<textarea/>' )
			.addClass( 'planta_tools_forms_custom_css' )
			.attr( 'name', 'planta_tools_forms_info[' + planta_tools_forms_idx + '][custom_css]' )
			.attr( 'id', 'planta_tools_forms_custom_css' + id_suffix )
			.attr( 'placeholder', planta_tools_forms_i18n.placeholder_custom_css )
			.val( data ? data.custom_css : '' ) );

		planta_tools_form_slot.append( jQuery( '<a/>' )
			.addClass( 'planta_tools_forms_remove_slot' )
			.attr( 'href', '#' )
			.html( planta_tools_forms_i18n.remove )
			.click( function( e ) {
				e.preventDefault();

				jQuery( this ).parent().remove();
			} ) );

		jQuery( '#planta_tools_forms_container' ).append( planta_tools_form_slot );
	};

	jQuery( '#planta_tools_forms_add_slot' ).click( function( e ) {
		e.preventDefault();

		planta_tools_forms_add_slot();
	} );

	/*jQuery( '#planta_tools_forms' ).submit( function( e ) {
		var errors = false;

		if( ! jQuery( '#planta_tools_forms_container .planta_tools_form_slot' ).length )
			return false;

		return ! errors;
	} );*/

	if( "undefined" != typeof( planta_tools_forms_info ) && planta_tools_forms_info )
		for( var i in planta_tools_forms_info )
			planta_tools_forms_add_slot( planta_tools_forms_info[ i ] );
});
