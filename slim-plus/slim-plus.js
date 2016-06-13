jQuery(document).ready(function ($) {

	$('.cn-hide').css('display', 'none');

	$('h3.cn-accordion-item').click( function() {
		var $this = $(this);
		var div = $this.attr('data-div-id');

		if ( $( '#' + div ).css('display') == 'block' ) {
			$( '#' + div ).slideUp();
			$($this).children('.cn-sprite').toggleClass('cn-open');
		} else {
			$( '#' + div ).slideDown();
			$($this).children('.cn-sprite').toggleClass('cn-open');
		}

		return false
	});

	$('select[name^=cn-cat]').chosen();
});