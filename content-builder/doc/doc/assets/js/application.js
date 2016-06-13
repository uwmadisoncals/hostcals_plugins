!function ($) {
	function resizeThumb(){
		$('ul.doc-thumbnails').each(function(){
			var height = 0;
			$('div.caption', this).each(function(){
				$(this).css('min-height','auto');
				height = Math.max( height, $(this).height() );
			});
			$('div.caption', this).css('min-height', height+'px');
		});
	}
	$(document).ready(function(){

		var $window = $(window);
		// Disable certain links in docs
		$('section [href^=#]').click(function (e) {
			e.preventDefault()
		});

		// side bar
		$('.doc-sidenav-cont').affix({
			offset: {
				top: function () { return $('header').height()+30; }
				, bottom: 0
			}
		});

		// make code pretty
		window.prettyPrint && prettyPrint();

		resizeThumb();
	});
	$(window).resize(function(){
		resizeThumb();
	});

}(window.jQuery);