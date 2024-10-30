(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(function() {
		// Top menu disconnect button ajax call.
		$("#icontact-forms-disconnect-button").click( function() {
			this.disabled = true;
			jQuery('#wpbody').prepend('<div class="notice notice-warning is-dismissible icform-disconnect-warning"><p>After disconnecting, your forms will continue to work and collect email addresses unless you remove them from the page or delete the plugin.<div id="icform-confirmation-buttons"><button type="button" id="icform-confirm-disconnection" class="button button-primary">Disconnect</button><button type="button" id="icform-cancel-disconnection" class="button button-primary">Stay Connected</button></div></p></div>');
			$("#icform-cancel-disconnection").click( function() {
				$("#icontact-forms-disconnect-button")[0].disabled = false;
				$('.icform-disconnect-warning').empty();
				$('.icform-disconnect-warning').remove();
			});
	
			$("#icform-confirm-disconnection").click( function() {
				var parameters = {};
				$('input[name="topmenu-settings-options"]').each( function( index ) {
					parameters[ $( this )[0].id ] = $( this )[0].value;
				});	
				$.post( ajaxurl, parameters, function( response ) {
					location.reload();
				});
			});			
		});

		if ( typeof icform_forms_ajax_action !== 'undefined' ) {
			$("#icform-table-toolbox #search_id-search-input").on( 'input', function( event ) {
				if ( event.currentTarget.value == '' ) {
					$.post( ajaxurl, { action: icform_forms_ajax_action, search_nonce: icform_search_nonce }, function( response ) {
						$('#the-list')[0].innerHTML = jQuery.parseJSON(response).rows;
					});
					$.post( ajaxurl, { action: icform_forms_ajax_action, pagination: 1, search_nonce: icform_search_nonce }, function( response ) {
						$('.icontact-forms .tablenav.top')[0].innerHTML = jQuery.parseJSON(response).pagination;
						$('.icontact-forms .tablenav.bottom')[0].innerHTML = jQuery.parseJSON(response).pagination;
					});				
				}
			});
		}

		// Attach copy button click function on forms tables to copy the short code tag.
		$('button[name="copy-button"]').click( function( $e ) {
			copyToClipboard( 'form-' + $e.target.id );
		});

		// On form settings if the client has multiple folders show the modal with folder dropdown.
		if ( $('#icform-folders-modal').length ) {
			if (typeof tb_show === "function") {
				tb_show( '', '#TB_inline?height=238&modal=true&inlineId=icform-folders-modal' );
			}
		}
		
	});	

	function unsecuredCopyToClipboard( text ) {
		const textArea = document.createElement( 'textarea' );
		textArea.style.position = 'fixed';
		textArea.value=text;
		document.body.appendChild( textArea );
		textArea.focus();
		textArea.select();
		try{ 
			document.execCommand( 'copy' );
		} catch( err ) {
			console.error( 'Unable to copy to clipboard', err );
		}
		document.body.removeChild( textArea );
	}

	/**
	 * Copies the text passed as param to the system clipboard
	 * Check if using HTTPS and navigator.clipboard is available
	 * Then uses standard clipboard API, otherwise uses fallback
	*/
	function copyToClipboard( element_id ) {
	  var content = document.getElementById( element_id ).value;
	  if ( window.isSecureContext && navigator.clipboard ) {
		navigator.clipboard.writeText( content );
	  } else {
		unsecuredCopyToClipboard( content );
	  }
	}

})( jQuery );
