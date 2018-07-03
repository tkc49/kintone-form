(function($){

	$('#js-get-kintone-data').click(function(e){

		e.preventDefault();

		// console.log(form_data_to_kintone_load_kintone_data_ajax_param.posts);
		let number = $(e.target).data('number');
		
		let domain = $("#kintone-form-domain").val();
		
		let basicAuthId = $("#kintone-basic-authentication-id").val();
		let basicAuthPassword = $("#kintone-basic-authentication-password").val();
		let appId = $("#kintone-form-appid-"+number).val();
		let token = $("#kintone-form-token-"+number).val();
				

		$.ajax({
			type: 'POST',
			url: 'http://sync.test/wp-content/plugins/kintone-form/connetc-kintone-data.php',
			data: {
				basicAuthId: 	basicAuthId,
				basicAuthPassword: 	basicAuthPassword,
				appId: 	appId,
				token: 	token
			},
			success: function(data) {
				console.log(data);
			}
		});
	})


})(jQuery);