(function($){
	window.ContentBlockDivider = {

		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: null,

		elem	: {},

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');

			$elem = $('<div class="cb-divider cb-clearfix"><em></em></div>');
			this.block.append( $elem );
			this.elem = $elem;

			return( this );

		},

		/**
		 * Initialize than data is ready
		 */
		init : function() {

			this.builder = this.block.data('builder');


		}
	};
})(jQuery);