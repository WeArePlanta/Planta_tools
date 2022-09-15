/**
 * This file handles the submission of Planta Forms.
 *
 * Uses '/planta-tools-ajax.js';
 */

var PlantaForm = {
	form : null,

	messageElement: null,

	init : function() {
		this.messageElement = this.form.getElementsByClassName( 'planta-tools-form-response-message' )[0];

		this.form.dataset.isPlantaFormListening = true;

		this.form.addEventListener(
			'submit', function( e ) {
				e.preventDefault();

				this.submit();
			}.bind( this )
		);
	},

	submit : function() {
		this.messageElement.innerText = '';
		this.messageElement.className = 'planta-tools-form-response-message';

		var ajax = new AJAX();

		ajax.url = this.form.querySelector('input[name="planta-tools-form-ajax"]').value;
		ajax.method     = this.form.method;
		ajax.parameters = new FormData( this.form );
		ajax.parameters.append( 'action', 'planta_tools_forms_submit_form' ); //this corresponds to wp_action: wp_ajax_planta_tools_forms_submit_form.

		// var file = this.form.querySelector('input[type="file"]').value;
		// ajax.parameters.append( 'file', file );

		var files = this.form.querySelectorAll('input[type="file"]');

		for ( let i = 0; i < files.length; i++ ) {
			files[ i ];
			ajax.parameters.append( files[ i ].name, files[ i ].value );
		}

		ajax.callbacks.progress	= function( e ) {
			this.messageElement.innerText = 'Enviando información';
			this.messageElement.className = 'planta-tools-form-response-message sending';
		}.bind( this );

		ajax.callbacks.success = function( response ) {
			this.enable( true );

			if ( response.success ) {
				document.dispatchEvent( new Event( 'PlantaFormSubmissionSuccess' ) );

				if ( this.form.action && this.form.action !== window.location.href ) {
					HTMLFormElement.prototype.submit.call( this.form );
				}

				this.form.className = 'planta-tools-forms-ajax-form planta-tools-form-submission-success';

				this.messageElement.innerText = '¡Muchas gracias! ¡Hemos recibido tus datos correctamente! Descargar guías <a href="https://weareplanta.com/wp-content/uploads/2022/04/foto-nati-1.jpg" download>';
				this.messageElement.className = 'planta-tools-form-response-message success';

			} else {
				document.dispatchEvent( new Event( 'PlantaFormSubmissionError' ) );

				this.messageElement.innerText = '¡Ha ocurrido un error! Vuelve a intentarlo más tarde!';
				this.messageElement.className = 'planta-tools-form-response-message error';
			}
		}.bind( this );

		ajax.callbacks.error = function( response ) {
			this.enable( true );

			this.form.className = 'planta-tools-forms-ajax-form planta-tools-form-submission-error';

			var e = new Event( 'PlantaFormSubmissionError' );

			e.error = response;

			document.dispatchEvent( e );
		}.bind( this );

		ajax.callbacks.complete	= function() {
			// console.log( 'ajax complete' );
		}.bind( this );

		/**
		 * When we start sending we disable the form elements and add a class
		 * for styling purposes.
		 */
		this.enable( false );
		this.form.className = 'planta-tools-forms-ajax-form planta-tools-form-sending';

		document.dispatchEvent( new Event( 'PlantaFormSubmissionStart' ) );

		ajax.send();
	},

	enable : function( enable ) {
		for ( var i in this.form.elements ) {
			this.form.elements[ i ].disabled = ! enable;
		}
	},
};

var PlantaFormsInit = function() {
	var formElements = document.getElementsByClassName( 'planta-tools-forms-ajax-form' );
	var forms = {};

	for ( let i = 0; i < formElements.length; i++ ) {
		forms.i = Object.create( PlantaForm );
		forms.i.form = formElements[ i ];
		forms.i.init();
	}
};

if ( 'complete' === document.readyState ) {
	PlantaFormsInit();
} else {
	document.addEventListener( 'DOMContentLoaded', PlantaFormsInit );
}
