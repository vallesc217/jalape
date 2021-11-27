( function($) {
	"use strict";
	
	var ajax_majesty_demo_content = majesty_demo_content;
	$('.add_update_demo').click( function(e) {
		$('.add_update_demo').attr("disabled", "disabled");
		var progress = {
			'current'	: 'cat',
			'title'		: 'Categories'			
		};
		$('.demo-timer').timer();
		reload_ajax(progress);		
    });
	
	function ajaxloaddemocontent(args) {
		$.ajax({
				type: 'POST',
				dataType: 'JSON',
				url: majesty_demo_content.ajaxurl,
				
				data: {
					
					// The data parameter is an object which contains the data you want to pass
					'action': 			'majesty_add_update_demo_content', // used in wordpress add action majesty_load_more_posts
					'demosecurity': 	majesty_demo_content.demononce,
					'current_progress': args.current,
					
				},
				beforeSend: function() {
					var stringadd = '<div class="alert success '+ args.current +'"><div class="add-icon icon-circle-o-notch"></div><div class="message"><strong class="loadingitem">Import ...!</strong> '+ args.title +'.</div></div>';
					$('.majesty-democontent-message').append(stringadd);
				},
				success: function( data ) {
					//console.log(data.current);
					//$(".eye_ajax_loader").remove();
					if( data.current == 'finished' ) {
						//$('form').removeClass('loading');
						$('.add_update_demo').removeAttr("disabled", "disabled");
						$('.demo-timer').timer('pause');
						console.log(data.title);
						$('.majesty-democontent-message .alert:last-child .loadingitem').text('Imported: ');
						$('.majesty-democontent-message .alert:last-child .add-icon').addClass('icon-check');
						$('.majesty-democontent-message .alert:last-child .add-icon').removeClass('icon-circle-o-notch');
						
						var stringadd = '<div class="alert success '+ data.current +'"><div class="icon-check"></div><div class="message"><strong class="loadingitem">Demo content imported... have fun!</strong></div></div>';
						$('.majesty-democontent-message').append(stringadd);
					
						$('.add_update_demo').text('Demo Imported');
						//console.log(data.error);
					} else if( data.current != '' ) {
						//console.log(data.title);
						//console.log(data.current);
						if( data.success != 'false' ) {
							$('.majesty-democontent-message .alert:last-child .loadingitem').text('Imported: ');
							$('.majesty-democontent-message .alert:last-child .add-icon').addClass('icon-check');
							$('.majesty-democontent-message .alert:last-child .add-icon').removeClass('icon-circle-o-notch');
						} else {
							$('.majesty-democontent-message .alert:last-child .loadingitem').text('Failed : To ');
							$('.majesty-democontent-message .alert:last-child .add-icon').removeClass('icon-circle-o-notch');
							$('.majesty-democontent-message .alert:last-child .message').append(data.error);
						}
						
						var progress = {
							'current'	: data.current,
							'title'	: data.title
						};
						reload_ajax(progress);
						/*setTimeout(function () {
								reload_ajax(progress);
						}, 5000)*/
					}	
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
				}
			});
	}
	
	function reload_ajax(args) {
		
		ajaxloaddemocontent(args);
	}
})(jQuery);