(function($){
	window.ContentBlockHeading = {

		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: null,

		elem	: {},
		form	: {},
		content	: {},
		tag		: null,
		builder : null,
		emptyText : '',

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');

			$elem = $('<div class="cb-heading cb-clearfix"></div>');
			this.block.append( $elem );
			this.elem = $elem;

			this.content = $('<div id="'+this.id+'-Content">[click here]</div>');
			this.elem.append( this.content );

			//this.form = $('<form id="'+this.id+'-Form" class="cb-form"><span class="arrow"></span></form>');
			//this.elem.append( this.form );
			//this.form.hide();

			this.content.show();
			this.content.click( $.proxy( this.open, this ) );
			this.emptyText = this.content.text();

			return( this );
		},

		/**
		 * Initialize than data is ready
		 */
		init : function() {

			this.builder = this.block.data('builder');
			if( this.data === null ) {
				this.setData();
			}

			this.update();

		},

		/**
		 * Open edit mode
		 * @return {Boolean}
		 */
		open : function() {

			if( ContentBuilder.isEditMode( this.builder ) ) {
				return false;
			}

			//create form && asign action
			if( this.form.length != 1 ) {
				this.form = ContentBuilder.FormCreate( this.id+'-Form', this.config.form );
				this.form.data('editor', this );
				this.elem.append( this.form );
				this.form.bind('cb-control', $.proxy( this.onFormControlHandle, this ) );
				$('select[name="heading"]', this.form).change(function(){
					var value = $(this).val();
					$editor = $(this).closest('form').data('editor');
					var data = $.extend( {}, $editor.data );
					data.heading = value;
					$editor.update( data );
				});
			}

			//assign form data
			ContentBuilder.FormData( this.form, this.data );

			//assign empty space for tag
			if( $.trim(this.data.text).length == 0 ) {
				this.tag.html('&nbsp;');
			}

			this.tag.attr('contenteditable', 'true').focus();
			//disable paste
			//this.tag.bind('paste',function( event, data ){
			//	return false;
			//});

			//set form position
			$f = $('div.cb-form', this.form );
			var left = this.elem.offset().left - this.builder.offset().left;
			$f.css('left', (left*-1)+'px');
			$f.width( this.builder.width() );
			this.form.show();
			this.block.trigger('cb-editing',[true]);

		},

		/**
		 * On form cancel/submit handle
		 * @param event
		 * @param action
		 */
		onFormControlHandle : function( event, action ) {

			switch( action ) {
				case 'submit':
					$form = $(event.currentTarget);
					var formData = ContentBuilder.FormData( $form );
					var data = {};
					data.heading = formData.heading;
					data.text = ContentBuilder.EncodeEntities( $.trim( this.tag.text() ) );
					data.link = formData.link;
					data.target = formData.target;
					data.title = formData.title;
					data['class'] = formData['class'];
					this.setData( data );
					this.close();
					this.update();
					this.block.trigger('cb-updated');
				break;
				case 'cancel':
					this.close();
					this.setData( this.data );
					this.update();
				break;
			}

		},

		/**
		 * 	Close edit mode
		 */
		close : function() {
			this.form.hide();
			this.tag.attr('contenteditable', 'false').focus();
			this.block.trigger('cb-editing',[false]);
		},

		/**
		 * 	Update view
		 */
		update : function( data ) {

			if( data === undefined ) {
				data = this.data;
			}

			//if self tag
			$tag = $('<'+data.heading+'></'+data.heading+'>');
			if( this.tag !== null ) {
				if( data.heading.toUpperCase() === this.tag.get(0).tagName.toUpperCase() ) {
					$tag = this.tag;
				}
			}
			if( this.tag === null ) {
				text = $.trim( data.text );
				text = ContentBuilder.DecodeEntities( text );
			} else {
				text = $.trim( this.tag.text() );
			}

			if( text.length == 0 ) {
				$tag.text( this.emptyText );
			} else {
				$tag.text( text );
			}

			if( this.tag !== $tag ) {
				this.content.empty().append( $tag );
				if( this.tag != null ) {
					if( this.tag.attr('contenteditable') ) {
						$tag.attr('contenteditable', this.tag.attr('contenteditable') );
						$tag.text( this.tag.text() );
						$tag.focus();
					}
				}
			}

			this.tag = $tag;

		},

		/**
		 * Set data
		 * @param data
		 */
		setData : function( data ) {
			data = ( data === undefined ? {text:''} : data );

			if( data.heading === undefined ) {
				data.heading = 'h2';
			}
			if( data.text === undefined ) {
				data.text = '';
			}
			if( data.link !== undefined ) {
				data.link = ContentBuilder.baseToUrl( data.link );
			}
			data.text = $.trim(data.text);
			this.data = data;
		},

		getData : function() {
			var data = $.extend({}, this.data );
			if( data.link !== undefined ) {
				data.link = ContentBuilder.base( data.link );
			}
			return( data );
		}

	};
})(jQuery);