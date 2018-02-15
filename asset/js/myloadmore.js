(function($){

	var $input = $('.your-cf7-tag-name');

	$input.each(function(index, element){ 
		
		var $output = $('#short-code-'+$(this).attr("id"));
		$(this).on('input', function(event) {
			var value = $(this).val();
			$output.text(value);

			if( $(this).val() !== '' ){
				$('select[id*="cf7-mailtag-' + $(this).attr("id")).attr("disabled", true);
			}else{
				$('select[id*="cf7-mailtag-' + $(this).attr("id")).removeAttr("disabled");
				$output.text($('select[id*="cf7-mailtag-' + $(this).attr("id")).val());
			}
		});	

		$('select[id*="cf7-mailtag-' + $(this).attr("id")).change(function(){
			$output.text($(this).val());
		});
	
	});

})(jQuery);

(function($){

	$( 'input.kintone-form-insert-tag' ).click( function() {

		var $form = $( this ).closest( 'form.tag-generator-panel' );
		var tag = $form.find( 'textarea.tag' ).val();		
		wpcf7.taggen.insert( tag );
		tb_remove(); // close thickbox
		return false;
	} );


})(jQuery);