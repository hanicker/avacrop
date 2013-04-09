var uploaded_images_temp_path='upload/temp/';
var showLoader=function(){
	$('body').append('<div id ="centerspin" style="position:fixed;top:50%;left:50%;z-index:999000"></div>');
	new Spinner().spin(document.getElementById('centerspin'));
}
var hideLoader=function(){
	$('#centerspin').remove();
}
$('#change_avatar').click(function(){

	//Form conf
	$('#change_avatar_form .save').hide();
	$('#change_avatar_form .suggestion1').show();
	$('#change_avatar_form .suggestion2').hide();

	//Show change form
	$('#change_avatar_form').css({'top':$(window).height()/2-$('#change_avatar_form').height()/2,'left':$(window).width()/2-$('#change_avatar_form').width()/2}).show();
	
	//Hidden file position
	$('#change_avatar_form .hiddenfile').attr('style','z-index: 100000;	opacity: 0;');
	$('#change_avatar_form .hiddenfile').css('top',$('#change_avatar_form .avatar').position().top);
	$('#change_avatar_form .hiddenfile').css('left',$('#change_avatar_form .avatar').position().left);

	//Load file uploader
	var tmp_avatar=$('#change_avatar_form .avatar').clone();
	$('#change_avatar_form form').unbind().ajaxForm({
		beforeSend: function() {
			showLoader();
			$('#change_avatar_form .suggestion2').show();
			$('#change_avatar_form .suggestion1').hide();
			$('#change_avatar_form .hiddenfile').hide();
		},
		complete: function(xhr) {
			var name=xhr.responseText;
			hideLoader();
			
			//Load cropping
			$('#change_avatar_form .avatar img').load(function(){
				$('#change_avatar_form .avatar img').jWindowCrop({
					targetWidth:128,
					targetHeight:128,
					onChange: function(result) {
						/*console.log($(this).attr('id'));
						console.log('x: '+result.cropX);*/
					}
				});
				$('#change_avatar_form .save').show();
			});
			
			$('#change_avatar_form .avatar img').attr('src',uploaded_images_temp_path+name).attr('data-name',name);
		}
	}); 	
	$('#change_avatar_form form input').unbind().change(function(){
		if($(this).val().length>0){
			$('#change_avatar_form form').submit();
		}
	});	
	
	
	//Bind close and save
	$('#change_avatar_form .close').unbind().click(function(){
		$('#change_avatar_form').hide();
		$('#change_avatar_form .avatar').html(tmp_avatar.html());
	});
	$('#change_avatar_form .save').unbind().click(function(){
		showLoader();
		var top = $('#change_avatar_form .jwc_image').position().top;
		var left = -$('#change_avatar_form .jwc_image').position().left;
		var width = $('#change_avatar_form .jwc_image').width();
		var height = $('#change_avatar_form .jwc_image').height();
		$.get($('#change_avatar_form form').attr('action'), {
			'opt' : 'crop',
			'image' : $('#change_avatar_form .avatar img').attr('data-name'),
			"w" : (($('#change_avatar_form .avatar').width() / width)) + '',
			"h" : (($('#change_avatar_form .avatar').height() / height)) + '',
			"x" : (Math.abs((left / width))) + '',
			"y" : (Math.abs((top / height))) + ''
		}, function(data) {
			hideLoader();
			//window.location = '?';
			alert('whatever');
			var name=data;
			$('body').append($('<img />').attr('src',uploaded_images_temp_path+name));
			
			//Close
			$('#change_avatar_form .close').click();
			
		});
	});
	
});
