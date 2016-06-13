(function($){
	window.ContentBlockVideo = {

		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: null,

		elem	: {},
		form	: {},
		edit	: {},
		preview	: {},
		builder : null,
		empty	: {},

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');

			$elem = $('<div class="cb-video cb-clearfix"></div>');
			this.block.append( $elem );
			this.elem = $elem;

			this.preview = $('<div id="'+this.id+'-Preview" class="cb-video-preview"></div>');
			this.elem.append( this.preview );

			this.empty = $('<div class="cb-video-empty"><span class="icon"></span><p>Embed Youtube or Vimeo video</p></div>');
			this.elem.append( this.empty );
			this.empty.hide();

			this.edit = $('<div id="'+this.id+'-Edit" class="cb-video-edit"></div>');
			this.elem.append( this.edit );
			this.edit.show();
			this.edit.click( $.proxy( this.open, this ) );

			return( this );
		},

		init : function() {

			this.builder = this.block.data('builder');
			if( this.data === null ) {
				this.setData( {url:''} );
			}
			this.update();

			this.block.bind('cb-updated', function() {
				$editor = $(this).data('editor');
				if( $editor ) {
					$editor.update();
				}
			});

			this.block.bind('cb-resize', function(e){
				$editor = $(this).data('editor');
				$editor.update();
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

				$('input[name="url"]', this.form ).change(function(){
					$editor = $(this).closest('form').data('editor');
					var data = $.extend( {}, ContentBuilder.FormData($editor.form) );
					data.source = $(this).val();
					$editor.update( data );
				});

				$('select[name="scale"],select[name="ratio"]', this.form ).change(function(){
					$editor = $(this).closest('form').data('editor');
					var data = $.extend( {}, ContentBuilder.FormData($editor.form) );
					data[ $(this).attr('name') ] = $(this).val();
					//console.log( $(this).attr('name') +'/'+ $(this).val() );
					$editor.update( data );
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
			this.edit.hide();

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
					value.url = formData.url; //$.trim( );
					value['class'] = formData['class'];
					value.ratio = formData.ratio;
					value.scale = formData.scale;
					this.close();
					this.setData( value );
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

		update : function( data ) {

			if( data === undefined ) {
				data = this.data;
			}

			// 640/360 16:9
			// 400/300 4:3
			var width = 500;
			var height = 0;

			if( data.url.length == 0 ) {
				this.empty.show();
				this.preview.hide();
			} else {
				var prevUrl = this.preview.data('url');
				if( prevUrl != data.url ) {
					var url = data.url;
					var id = null;
					this.preview.data('url', url ).empty();
					if( url.indexOf('vimeo.com') > 0 ) {
						id = url.substring( url.lastIndexOf('/')+1, ( url.indexOf('?')==-1?url.length:url.indexOf('?') ) );
						$embed = $('<div class="cb-video-embed"><iframe src="http://player.vimeo.com/video/'+id+'?portrait=0&color=333" width="100%" height="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>');
					}
					if( url.indexOf('youtube.com') > 0 ) {
						if( url.indexOf('?v=') != -1 ) { id = url.substring( url.indexOf('?v=')+3 ); }
						if( url.indexOf('&v=') != -1 ) { id = url.substring( url.indexOf('&v=')+3 ); }
						if( id != null ) { if( id.indexOf('&')!=-1 ) { id = id.substring(0, id.indexOf('&') ); } }
						$embed = $('<div class="cb-video-embed"><iframe class="youtube-player" type="text/html" width="100%" height="100%" src="http://www.youtube.com/embed/'+id+'?wmode=opaque" frameborder="0"></iframe></div>');
					}
					this.preview.show().append( $embed );
					this.empty.hide();
				}
			}

			if( parseInt(data.scale) > 0 ) {
				width = Math.round( this.elem.width()*parseInt(data.scale)/100 );
			}

			if( data.ratio == '16:9' ) {
				height = Math.round( width*9/16 );
			}
			if( data.ratio == '4:3' ) {
				height = Math.round( width*3/4 );
			}
			//console.log('data.scale:', data.scale,'->', width,'x', height );

			this.empty.height( height );
			$embed = $('div.cb-video-embed', this.preview );
			if( $embed.length == 1 ) {
				$embed.width( width ).height( height );
			}

		},

		close : function() {

			this.form.hide();
			this.edit.show();
			this.block.trigger('cb-editing',[false]);

		},

		setData : function( value ) {

			value = ( value === undefined ? {url:''} : value );

			if( value.scale === undefined ) {
				value.scale = 'default';
			}
			if( value.ratio === undefined ) {
				value.ratio = '16:9';
			}

			if( value.url != undefined ) {
				if( $.trim(value.url).length > 0 ) {
					value.url = $.trim(value.url);
				}
			} else {
				value.url = '';
			}

			this.data = value;
		},

		getData : function() {
			var data = $.extend({}, this.data );
			if( data.url !== undefined ) {
				if( data.url.length > 0 ) {
					$embed = $('div.cb-video-embed', this.preview );
					data.size = {
						width:$embed.width(),
						height:$embed.height()
					};
					//console.log('Get DATA:', data.size.width,'x',data.size.height );
				}
			}
			return( data );
		}

	};
})(jQuery);