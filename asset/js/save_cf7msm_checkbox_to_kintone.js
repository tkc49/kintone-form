/**
 * 送信ボタンを押された時
 */
( function( $ ){

	document.addEventListener( 'wpcf7mailsent', function( e ){
		let currentInputs = {};
		let names         = [];

		let kintoneFromSaveCfmsmCheckboxToKintoneData = sessionStorage.getObject( 'kintone-form-save-cfmsm-checkbox-to-kintone-data' );
		if ( ! kintoneFromSaveCfmsmCheckboxToKintoneData ) {
			kintoneFromSaveCfmsmCheckboxToKintoneData = {};
		}


		$.each( e.detail.inputs, function( i ){
			let name  = e.detail.inputs[i].name;
			let value = e.detail.inputs[i].value;

			//make it compatible with cookie version
			if ( name.indexOf( '[]' ) === name.length - 2 ) {
				// name = name.substring(0, name.length - 2 );
				if ( $.inArray( name, names ) === -1 ) {
					currentInputs[name] = [];
				}
				currentInputs[name].push( value );
			}
			names.push( name );
		} );

		$.each( currentInputs, function( name, value ){
			kintoneFromSaveCfmsmCheckboxToKintoneData[name] = value;
		} );


		sessionStorage.setObject( 'kintone-form-save-cfmsm-checkbox-to-kintone-data', kintoneFromSaveCfmsmCheckboxToKintoneData );

	}, false );


} )( jQuery );

/**
 * 送信ボタンを押された時
 */
( function( $ ){
	function quoteattr(s, preserveCR) {
		preserveCR = preserveCR ? '&#13;' : '\n';
		return ('' + s) /* Forces the conversion to string. */
			.replace(/&/g, '&amp;') /* This MUST be the 1st replacement. */
			.replace(/'/g, '&apos;') /* The 4 other predefined entities, required. */
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			/*
			You may add other replacements here for HTML only
			(but it's not necessary).
			Or for XML, only if the named entities are defined in its DTD.
			*/
			.replace(/\r\n/g, preserveCR) /* Must be before the next replacement. */
			.replace(/[\r\n]/g, preserveCR);
	}

	jQuery( document ).ready( function( $ ){

		let cf7msm_field          = $( "input[name='_cf7msm_multistep_tag']" );
		const hasMultistepOptions = cf7msm_field.length > 0;
		let isCF7MSM              = hasMultistepOptions;
		if ( ! isCF7MSM ) {
			cf7msm_field = $( "input[name='cf7msm-step']" );
			isCF7MSM     = ( cf7msm_field.length > 0 );
		}
		if ( ! isCF7MSM ) {
			//not a multi step form
			return;
		}
		const cf7msm_form = cf7msm_field.closest( 'form' );

		let kintoneFromSaveCfmsmCheckboxToKintoneData = sessionStorage.getObject( 'kintone-form-save-cfmsm-checkbox-to-kintone-data' );

		$.each( kintoneFromSaveCfmsmCheckboxToKintoneData, function( key, values ){
			key = key.substr( 0, key.length - 2 );
			for ( var i = 0; i < values.length; i++ ) {
				cf7msm_form.append( $( '<input type="hidden" name="_kintone-form-save-cfmsm-checkbox-to-kintone-data[' + key + '][]" value="' + quoteattr( values[i] ) + '">' ) );
			}
		} )

	} );

} )( jQuery )
