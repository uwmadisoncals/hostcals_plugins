(function($){
	window.ContentBlockImage = {

		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: null,

		elem	: {},
		preview	: {},
		empty	: {},
		form	: {},
		builder : null,
		loader	: {},

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');

			$elem = $('<div class="cb-image cb-clearfix"></div>');
			this.block.append( $elem );
			this.elem = $elem;

			this.preview = $('<div id="'+this.id+'-Preview" class="cb-image-preview"><img src="'+ContentBuilder.basePath+'/assets/images/s.gif"/></div>');
			this.elem.append( this.preview );
			this.preview.click( $.proxy( this.open, this ) );

			this.empty = $('<div class="cb-image-empty"><span class="icon"></span><p><a href="#select" data-act="browse" title="Select Image">Select</a> or <a href="#upload" data-act="upload" title="Upload Image">Upload</a> image</p></div>');
			this.elem.append( this.empty );

			this.loader = $('<div class="cb-image-loader"></div>');
			this.elem.append( this.loader );
			this.loader.hide();

			this.preview.hide();

			return( this );
		},

		init : function() {

			this.builder = this.block.data('builder');
			if( this.data === null ) {
				this.setData();
			}

			$('a[data-act]', this.empty).click( function(e) {
				var $btn = $(this);
				var $editor = $(this).closest('.content-block').data('editor');
				ContentBuilder.ModalWindow('image-'+($(this).data('act')=='upload'?'upload':'library'), $.proxy( $editor.onModalHandle, $editor ), 'empty-image' );
				e.stopPropagation();
				return false;
			});

			this.update();

			this.block.bind('cb-updated', function() {
				$editor = $(this).data('editor');
				if( $editor ) {
					$editor.update();
				}
			});

		},

		open : function() {

			if( ContentBuilder.isEditMode( this.builder ) ) {
				return false;
			}

			//create form && asign action
			if( this.form.length != 1 ) {

				this.form = ContentBuilder.FormCreate( this.id+'-Form', this.config.form );
				this.form.data('editor', this );
				this.elem.append( this.form );
				this.form.bind('cb-control', $.proxy( this.onFormHandle, this ) );

				$('input[name="source"]', this.form ).change(function(){
					$editor = $(this).closest('form').data('editor');
					var data = $.extend( {}, ContentBuilder.FormData($editor.form) );
					data.source = $(this).val();
					$editor.update( data );
				});

				$('select[name="scale"]', this.form ).change(function(){
					$editor = $(this).closest('form').data('editor');
					var data = $.extend( {}, ContentBuilder.FormData($editor.form) );
					data.scale = $(this).val();
					$editor.update( data );
					return false;
				});

				$('a[data-act]', this.form ).click(function(e){
					e.stopPropagation();
					var $form = $(this).closest('form');
					var $editor = $(this).closest('form').data('editor');
					var act = $(this).data('act');
					if( act == 'browse' ) {
						ContentBuilder.ModalWindow('image-library', $.proxy( $editor.onModalHandle, $editor ), 'change-image' );
					}
					//console.log('Action:', act );
					/*
					if( act == 'link' ) {
						window.wpLink.cb_callback = $.proxy( $editor.onLinkHandle, $editor );
						window.wpLink.open();
					}
					*/
					return false;
				});


			}

			//assign form data
			ContentBuilder.FormData( this.form, this.data );

			//set form position
			$f = $('div.cb-form', this.form );
			var left = this.elem.offset().left - this.builder.offset().left;
			$f.css('left', (left*-1)+'px').width( this.builder.width() );
			this.form.show();

			this.block.trigger('cb-editing',[true]);

		},

		/**
		 * On form cancel/submit handle
		 * @param event
		 * @param action
		 */
		onFormHandle : function( event, action ) {

			switch( action ) {
				case 'submit':
					$form = $(event.currentTarget);
					var formData = ContentBuilder.FormData( $form );
					var value = {};
					value.source = formData.source; //$.trim( );
					value.alt = formData.alt;
					value.link = formData.link;
					value.target = formData.target;
					value['class'] = formData['class'];
					value.scale = formData.scale;
					this.setData( value );
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

		/*
		onLinkHandle : function( linkData ) {

			var data = $.extend( ContentBuilder.FormData( this.form ), {} );
			data.link = linkData.href;
			data.alt = linkData.title;
			if( linkData.target == '_blank' ) {
				data.target = '_blank';
			}
			//data = $.extend( data, this.data, {} );
			console.log('onLinkHandle:', data );
			ContentBuilder.FormData( this.form, data );
			this.update();
		},
		*/

		/**
		 * Modal window handle
		 */
		onModalHandle : function( modal, data, action ) {

			if( modal == 'image-library' || modal == 'image-upload' ) {

				if( action == 'empty-image' ) {
					this.open();
				}
				var update = $.extend( {}, this.data, ContentBuilder.FormData( this.form ), data );
				ContentBuilder.FormData( this.form, update );
				this.update( update );

			}

		},

		close : function(){

			this.form.hide();
			this.block.trigger('cb-editing',[false]);

		},

		loadImage : function( url ) {
			this.elem.addClass('loading');
			try {
				//Run some code here
				$('<img/>').data('url', url ).data('editor', this ).load( function() {
					var source = $(this).data('url');
					$editor = $(this).data('editor');
					$editor.preview.data('loadedUrl', source );
					var data = $.extend( {}, $editor.data );
					data.source = source;
					var $img = $('img', $editor.preview );
					$img.attr('src', source );
					$img.width( this.width );
					$img.height( this.height );
					$img.data('width', this.width );
					$img.data('height', this.height );
					$editor.elem.removeClass('loading');
					$editor.update( data );
				}).attr('src', url);
			} catch(err) {
				//Handle errors here
				this.elem.removeClass('loading');
				var data = $.extend( {}, this.data );
				data.source = '';
				this.update( data );
			}
		},

		update : function( data ) {

			if( data === undefined ) {
				data = this.data;
			}

			var mode = 'empty';
			if( data.source.length > 0 ) {
				mode = 'pending';
			}

			if( mode == 'pending') {
				var loadedUrl = this.preview.data('loadedUrl');
				if( loadedUrl !== data.source ) {
					this.loadImage( data.source );
					return;
				}
				mode = 'loaded';
			}

			if( mode == 'loaded' ) {

				var $img = $('img', this.preview );
				if( data.scale == 'none' ) {
					$img.css('width','auto');
					$img.css('height','auto');
				}
				if( parseInt(data.scale) > 0 ) {
					$img.css('width', data.scale );
					$img.css('height','auto');
				}

				this.preview.show();
				this.empty.hide();

			} else {
				this.preview.data('loadedUrl', null );
				this.empty.show();
				this.preview.hide();

			}
			/*
			$('a.scale', this.form ).removeClass('active');
			$('a.scale[href="#'+data.scale+'"]', this.form ).addClass('active');
			if( data.scale == 'none' ) {
				$img.css('width','auto');
				$img.css('height','auto');
			}
			if( parseInt(data.scale) > 0 ) {
				$img.css('width', data.scale );
				$img.css('height','auto');
			}
			$('input[name="width"]', this.form ).val( $img.width() );
			$('input[name="height"]', this.form ).val( $img.height() );
			$('a.position', this.form ).removeClass('active');
			$('a.position[href="#'+data.position+'"]', this.form ).addClass('active');
			this.preview.removeClass('left').removeClass('right').removeClass('center');
			$img.css('margin-left','0px');
			if( data.position !== undefined ) {
				this.preview.addClass( data.position );
				if( data.position == 'center' ) {
					var left = Math.round( ( this.elem.width() - $img.width() ) / 2 );
					$img.css('margin-left', left+'px');
				}
			}
			*/
		},

		setData : function( data ) {

			data = ( data === undefined ? {source:''} : data );

			if( data.scale === undefined ) {
				data.scale = '100%';
			}
			if( data.position === undefined ) {
				data.position = 'center';
			}

			if( data.source === undefined ) {
				data.source = '';
			} else {
				if( $.trim(data.source).length > 0 ) {
					data.source = $.trim(data.source);
					data.source = ContentBuilder.baseToUrl( data.source );
				}
			}
			if( data.link !== undefined ) {
				data.link = ContentBuilder.baseToUrl( data.link );
			}

			//leave store data if exist
			if( this.data != null ) {
				if( this.data.store !== undefined ) {
					data.store = this.data.store;
				}
			}

			this.data = data;
		},

		getData : function() {
			var data = $.extend({}, this.data );
			if( data.source !== undefined ) {
				data.source = ContentBuilder.base( data.source );
				if( data.source.length > 0 ) {
					var $img = $('img', this.preview );
					data.size = {
						width:$img.width(),
						height:$img.height()
					};
				}
			}
			if( data.link !== undefined ) {
				data.link = ContentBuilder.base( data.link );
			}

			return( data );
		}

	};

})(jQuery);