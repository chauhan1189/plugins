jQuery(document).ready(function(){

	// click event for mark as complete

	jQuery(document).on('click','.mark_complete',function(){

		var user_id=jQuery(this).attr('data-userid');
		var post_id=jQuery(this).attr('data-postid');
		var type=jQuery(this).attr('data-type');

		jQuery(this).html('<i class="fa fa-circle-o-notch fa-spin fa-6x fa-fw margin-bottom"></i>');

		//jQuery('.mark_button_listing i').show();
		jQuery.ajax({
			method:"POST",
			url:ajax_login_object.ajaxurl,
			dataType: 'json',
			data:"action=act_mark_complete"+"&post_id="+post_id+"&user_id="+user_id+"&type="+type,
			success:function(data){
				//jQuery('.mark_button_listing i').hide();
				if(data.acts == 'complete') {
					jQuery('.post_id_'+data.post_id).addClass("disabled remove_acts").removeClass("mark_complete mark-act-completed-c").text(data.button_text);
				}
			}
		});

	});

	// click event for mark as un-complete 

	jQuery(document).on('click','.remove_acts',function(){

		var user_id=jQuery(this).attr('data-userid');
		var post_id=jQuery(this).attr('data-postid');
		var type=jQuery(this).attr('data-type');

		jQuery(this).html('<i class="fa fa-circle-o-notch fa-spin fa-6x fa-fw margin-bottom"></i>');

		jQuery('.ajax_loader').show();
		jQuery.ajax({
			method:"POST",
			url:ajax_login_object.ajaxurl,
			dataType: 'json',
			data:"action=act_remove_acts"+"&post_id="+post_id+"&user_id="+user_id+"&type="+type,
			success:function(data){
				jQuery('.ajax_loader').hide();
				jQuery('.post_id_'+data.post_id).html(data.updated_text).addClass('mark-act-completed-c mark_complete').removeClass('remove_acts');
				if(data.acts == 'acts_removed' && data.button_type == "myacts") {
					jQuery('.post-'+data.post_id).remove();
				}
				if(data.completed_count == '0' && data.button_type == "myacts") {
					jQuery('.my_acts_wrapper').html('<div class="alert error no-result">'+data.oops_msg+'</div>');
				}
			}
		});

	});


});