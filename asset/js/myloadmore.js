(function($){

	console.log('hoge');
	console.log(misha_loadmore_params.posts);
	console.log('hoge2');

	$('input[name="get-kintone-data"]').click(function(){
		
		let kintoneDomain = $('#kintone-form-domain').val();
		let kintoenAppId = $('#kintone-form-appid').val();
		let kintoneFormToken = $('#kintone-form-token').val();

		console.log(kintoneDomain);
		





		// return false;
	
	});

})(jQuery);