$(document).ready(function() {
  cron();

  $('.shifted').hide();

	$('.resourcei input').click(function(e) {
    if (e.shiftKey) {
		  $('[data-group="' + $(this).data('group') + '"]').attr('checked',($(this).attr('checked') || false));
    }
	});
	
	$('body').keydown(function(e) {
		if (e.which == 16) {
		  $('.shifted').fadeIn('slow');
		}
	});
	
	$('body').keyup(function(e) {
		if (e.which == 16) {
		  $('.shifted').fadeOut('slow');
		}
	});
	
	$('#users_edit button.btn.btn-primary').click(function(e) {
		if ($('#username').val() == '') {
			$('.username').addClass('error');
			e.preventDefault();
		}
		if ($('#email').val() == '') {
			$('.email').addClass('error');
			e.preventDefault();
		}
		if ($('#user_id').val() == 0) {
			if ($('#password').val() == '') {
				$('.password').addClass('error');
				e.preventDefault();
			}
		}
	});
	
	$('#access button.btn.btn-primary').click(function(e) {
    if ($('#access_name').val() == '') {
    	$('.access_name').addClass('error');
    	e.preventDefault();
    }
	});
	
	$('#modules button.btn').click(function(e) {
	  if ($('#model_name').val() == '') {
    	$('.name').addClass('error');
    	e.preventDefault();
	  }
	});
	
	$('#settings button.btn.btn-primary').click(function(e) {
    if ($('#slug').val() == '') {
    	$('.slug').addClass('error');
    	e.preventDefault();
    }
    if ($('#value').val() == '') {
    	$('.value').addClass('error');
    	e.preventDefault();
    }
	});

	$('i.shifted').not('.download').click(function(e) {
		e.preventDefault();
		$.ajax(location.href + '/delete/' + $(this).data('id')).done(function() { window.location.replace(location.href); });
	});	
	
	$('i.shifted.download').click(function(e) {
		e.preventDefault();
		window.location.replace(location.href + '/download/' + $(this).data('id'));
	});	
	
	$('.shiftdashboard').click(function(e) {
    if (e.shiftKey) {
  		e.preventDefault();
			$.ajax(location.href + '/delete/' + $(this).data('which') + '/' + $(this).data('id')).done(function() { window.location.replace(location.href); });
		}
	});
	
	$('#settings td').popover({
		placement: 'right'
	});
 
});

function cron() {
  $.ajax(base_url + '/cron');
}
