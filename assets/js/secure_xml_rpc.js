/*! Secure XML-RPC - v0.1.0
 * http://wordpress.org/plugins/secure-xmlrpc
 * Copyright (c) 2013; * Licensed GPLv2+ */
/*global window, jQuery */
( function( window, $, undefined ) {
	'use strict';

	var document = window.document,
		CORE = window.xmlrpcs;

	/**
	 * Add a new row to the UI.
	 *
	 * @param {event} e
	 */
	function add_row( e ) {
		e.preventDefault();

		// First, remove the "no applications" row
		$( document.getElementById( 'xmlrpcs-no-apps' ) ).remove();

		// Fetch a new row from the server and inject it.
		var $request = $.ajax( {
			'type'     : 'POST',
			'url'      : CORE.ajaxurl,
			'data'     : {
				'action' : 'xmlrpcs_new_app',
				'_nonce' : CORE.new_nonce
			},
			'dataType' : 'html'
		} );

		// Insert the HTML returned by the server once we've got it.
		$request.done( function( data ) {
			$( data ).insertAfter( document.getElementById( 'xmlrpcs_app_body' ) );
		} );
	}

	/**
	 * Remove a row from the UI.
	 *
	 * @param {event} e
	 */
	var remove_row = function( e ) {
		if ( ! window.confirm( CORE.confirm_delete ) ) {
			return;
		}

		$( this ).parents( 'tr' ).first().remove();
	};

	// Bind events
	$( document.getElementById( 'xmlrpcs-generate' ) ).on( 'click', add_row );
	$( '.xmlrpcs-delete' ).on( 'click', remove_row );

} )( this, jQuery );