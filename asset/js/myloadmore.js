(function($){

	var $input = $('.your-cf7-tag-name');

	$input.each(function(index, element){ 
		
		var $output = $('#short-code-'+$(this).attr("id"));
		$(this).on('input', function(event) {
		  var value = $(this).val();
		  $output.text(value);
		});	
	

	});

	// console.log('hoge');
	// console.log(misha_loadmore_params.posts);
	// console.log('hoge2');

	// $('input[name="get-kintone-data"]').click(function(){
		
	// 	let kintoneDomain = $('#kintone-form-domain').val();
	// 	let kintoenAppId = $('#kintone-form-appid').val();
	// 	let kintoneFormToken = $('#kintone-form-token').val();

	// 	console.log(kintoneDomain);
		





	// 	// return false;
	
	// });

})(jQuery);