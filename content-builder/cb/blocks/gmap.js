(function($){
	window.ContentBlockGoogleMap = {

		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: null,

		builder : null,
		elem	: {},
		form	: {},
		welcome : {},
		screen	: {},

		resize	: {},
		preview	: {},
		iframe	: null,
		map		: null,
		marker	: null,
		geocoder : null,
		isEditing	: false,

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');

			$elem = $('<div class="cb-gmap cb-clearfix"></div>');
			this.block.append( $elem );
			this.elem = $elem;

			this.welcome = $('<div class="cb-gmap-welcome"><span class="icon"></span><p>Click To Add Location</p></div>');
			this.elem.append( this.welcome );
			this.welcome.show();

			this.preview = $('<div id="'+this.id+'-Preview" class="cb-gmap-preview"></div>');
			this.elem.append( this.preview );

			//this.resize = $('<div id="'+this.id+'-Resize" class="cb-gmap-resize-s"></div>');
			//this.preview.append( this.resize );
			//this.resize.disableSelection();

			this.screen = $('<div id="'+this.id+'-Screen" class="cb-gmap-screen"></div>');
			this.elem.append( this.screen );
			this.screen.show();
			this.screen.click( $.proxy( this.open, this ) );

			return( this );
		},

		init : function() {

			this.builder = this.block.data('builder');
			if( this.data == null ) {
				this.setData( {location:''} );
			}

			this.update();

			this.block.bind('cb-resize', function(){
				$editor = $(this).data('editor');
				if( $editor.map != null ) {
					try {
						$editor.iframe.contentWindow.resize();
					} catch(e){}
				}
			});

		},

		/**
		 * Open Edit Form
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
				this.form.bind('cb-control', $.proxy( this.onFormHandle, this ) );
				this.loadMap();
				$('a[data-act="locate"]', this.form).click(function(){
					$editor = $(this).closest('form').data('editor');
					$editor.iframe.contentWindow.locateAddress( $('input[name="location"]', $editor.form ).val() );
					return false;
				});
				$('input[name="location"]', this.form ).focus(function(){
					$(this).data('focused', true );
				}).blur(function(){
					$(this).data('focused', false );
				});
				$('input[name="height"]', this.form).change(function(){
					var value = $(this).val();
					$editor = $(this).closest('form').data('editor');
					var data = $.extend( {}, $editor.data );
					data.height = value;
					$editor.update( data );
				});
			}

			//assign form data
			ContentBuilder.FormData( this.form, this.data );

			//set form position
			$f = $('div.cb-form', this.form );
			var left = this.elem.offset().left - this.builder.offset().left;
			$f.css('left', (left*-1)+'px').width( this.builder.width() );

			this.form.show();
			this.screen.hide();
			this.isEditing = true;
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
					//locate address at first
					$location = $('input[name="location"]', this.form);
					if( $location.data('focused') ) {
						$('a[data-act="locate"]', this.form ).trigger('click');
						return false;
					}
					$form = $(event.currentTarget);
					var formData = ContentBuilder.FormData( $form );
					var value = {};
					value.location = formData.location;
					value.zoom = this.map.getZoom();
					value.mapTypeId = this.map.getMapTypeId();
					value.height = formData.height;
					value['class'] = formData['class'];
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

		/**
		 * Update
		 * @param data
		 */
		update : function( data ) {

			if( data === undefined ) {
				data = this.data;
			}

			var height = 300;
			if( parseInt(data.height) > 0 ) {
				height = data.height;
			}
			this.welcome.height( height );
			this.preview.height( height );

			if( data.location.length == 0 ) {
				this.welcome.show();
				this.preview.hide();
			} else {
				this.preview.show();
				this.welcome.hide();
				if( this.map !== null ) {
					var latlngStr = data.location.split(',', 2);
					var lat = parseFloat(latlngStr[0]);
					var lng = parseFloat(latlngStr[1]);
					var latlng = new this.iframe.contentWindow.google.maps.LatLng(lat, lng);
					this.marker.setPosition( latlng );
					this.map.setCenter( latlng );
					this.map.setZoom( data.zoom );
					this.map.setMapTypeId( data.mapTypeId );
				}
				if( this.map == null ) {
					this.loadMap();
				}
			}

		},

		/**
		 * Update location
		 * @param location
		 */
		updateLocation : function( location ) {
			$location = $('input[name="location"]', this.form);
			$location.val( location );
		},

		/**
		 * Load map
		 */
		loadMap : function() {

			if( this.iframe == null ) {
				var query = {};
				query.action = 'iframe-edit';
				query.block = {block:this.type,blockId:this.id};
				query.block = $.extend({},query.block, this.data );
				var mapUrl = ContentBuilder.ajaxUrl+'?'+jQuery.param(query);
				$iframe = $('<iframe id="'+this.id+'-Iframe" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>');
				this.preview.append( $iframe );
				this.iframe = document.getElementById(this.id+'-Iframe');
				$iframe.attr('src', mapUrl).css('visibility','hidden');
				$iframe.load( $.proxy( this.onMapFrameLoaded, this ) );
				this.preview.show();
				this.welcome.hide();
			}

		},

		/**
		 * Map frame loaded
		 */
		onMapFrameLoaded : function(){

			$iframe = $(this.iframe);
			$iframe.css('visibility','visible');
			this.iframe.contentWindow.editor = this;
			this.map = this.iframe.contentWindow.map;
			this.marker = this.iframe.contentWindow.marker;
			this.marker.setDraggable( true );
			this.geocoder = new this.iframe.contentWindow.google.maps.Geocoder();
			this.marker.editor = this;
			this.iframe.contentWindow.editor = this;
			this.iframe.contentWindow.google.maps.event.addListener(this.marker, 'dragend', function( e ) {
				this.editor.updateLocation( e.latLng.toUrlValue() );
			});
			if( !this.isEditing ) {
				this.update();
			}
			
		},

		/**
		 * Close form
		 */
		close : function() {

			this.form.hide();
			this.screen.show();
			this.isEditing = false;
			this.block.trigger('cb-editing',[false]);

		},

		/**
		 * Set data
		 * @param value
		 */
		setData : function( value ) {

			value = ( value === undefined ? {location:''} : value );
			if( value.location != undefined ) {
				if( $.trim(value.location).length > 0 ) {
					value.location = $.trim(value.location);
				}
			} else {
				value.location = '';
			}
			this.data = value;

		}

	};
})(jQuery);