(function($){

	window.ContentBlockGallery = {

		nextId 	: 1,
		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: {},

		elem	: {},
		form	: {},
		layout	: 'a-b-c',
		ratio	: 1.33, //16:9,3:2,4:3
		add		: {},
		column	: 3,
		builder : null,
		itemW	: 0,
		itemH 	: 0,

		//preview	: {},

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');

			//change control
			$control = this.block.data('control');
			//$control.addClass('static');
			//$block.children('.cb-placeholder-top,.cb-placeholder-bottom').addClass('layout');
			//$block.children('.cb-border').remove();

			$elem = $('<div class="cb-gallery cb-clearfix"></div>');
			this.block.append( $elem );
			this.elem = $elem;

			this.items = $('<div class="cb-gallery-items"></div>');
			$wrapper = $('<div style="width:100%;overflow:hidden;"></div>');
			this.elem.append( $wrapper );
			$wrapper.append( this.items );
			//this.elem.append( this.items );

			this.add = $('<div class="cb-gallery-add cb-gallery-item" data-act="add"><span><em>Add new photo</em></span></div>');
			this.items.append( this.add );
			this.add.disableSelection();
			return( this );
		},

		init : function() {

			this.builder = this.block.data('builder');
			this.builder.bind('cb-editing', $.proxy( this.onEditHandle, this ) );

			this.add.click( function(e) {
				if( $(this).data('mouse-enabled') ) {
					var $editor = $(this).closest('.content-block').data('editor');
					ContentBuilder.ModalWindow('image-library', $.proxy( $editor.onModalHandle, $editor ), 'add-image' );
					//$editor.onModalHandle('image', {source:'http://smartbox-wp.valdas.dev.indigokids.lt/wp-content/uploads/2012/08/Tulips.jpg'}, 'add-image');
				}
			}).data('mouse-enabled', true );

			//create items
			var items = this.data.items;
			if( items != undefined ) {
				for( var i=0; i< items.length; i++ ) {
					var itemData = items[i];
					$item = this.createItem( itemData );
					$item.insertBefore( this.add );
				}
			}

			//with little sort placeholder bug
			this.items.sortable({
				handle:'.cb-gallery-screen',
				start : function( event, ui ) {
					$('.cb-gallery-item', event.currentTarget ).data('mouse-enabled', false );
					$('.cb-gallery-item-hover', event.currentTarget ).removeClass('cb-gallery-item-hover');
				},
				stop : function( event, ui ) {
					$('.cb-gallery-item', event.currentTarget ).data('mouse-enabled', true );
				},
				items : '.cb-gallery-item:not(.cb-gallery-add)',
				update:function(event, ui ){
					$block = $(this).closest('.content-block');
					$block.trigger('cb-updated');
				}
			});

			this.block.bind('cb-resize', function(e){
				$editor = $(e.target).data('editor');
				$editor.updatedItems();
			});

			this.updatedItems();

		},

		/**
		 * Builder Edit Mode handle
		 * @param event
		 * @param status
		 */
		onEditHandle : function( event, status ) {

			this.items.children('.cb-gallery-item').each(function(){
				if( status ) {
					$(this).fadeTo(0,0.5).removeClass('cb-gallery-item-hover');
					$(this).data('mouse-enabled', false );
				} else {
					$(this).fadeTo(0,1).removeClass('cb-gallery-item-hover');
					$(this).data('mouse-enabled', true );
				}
			});

			if( status ) {
				this.items.sortable('disable');
				this.add.data('mouse-enabled', false ).fadeTo(0,0.5);
			} else {
				this.items.sortable('enable');
				this.add.data('mouse-enabled', true ).fadeTo(0,1);
			}

			//if active current block select active image
			var $currTarget = $(event.target);
			if( status && $currTarget.attr('id') == this.id ) {
				var $item = this.form.data('item');
				$item.fadeTo(0,1);
			}

		},

		/**
		 * Update childs
		 */
		updatedItems : function(){

			this.itemW = Math.floor(this.elem.width()*0.32);
			this.itemH = Math.round(this.itemW*(1/this.ratio));

			var $e = this;
			this.items.children(':not(form)').each(function(){
				var $item = $(this);
				$item.width( $e.itemW ).height( $e.itemH );
				if( $item.hasClass('cb-gallery-add') ) {
					$item.removeClass('small');
					if( $item.width() < 110 ) {
						$item.addClass('small');
					}
				}
			});

		},

		/**
		 * Modal window handle
		 */
		onModalHandle : function( modal, data, action ) {

			if( action == 'add-image' ) {
				var $newItem = this.createItem({source:data.source});
				$newItem.insertBefore( this.add );
				this.updatedItems();
				this.block.trigger('cb-updated');
			}
			if( action == 'change-image' ) {
				var $item = this.form.data('item');
				var formData = ContentBuilder.FormData( this.form );
				formData.source = data.source;
				ContentBuilder.FormData( this.form, formData );
				this.previewItem( $item, formData );
			}

		},

		/**
		 * Create item
		 * @param data
		 * @return {jQuery}
		 */
		createItem : function( data ) {

			if( data === undefined ) {
				data = {};
			}

			var itemId = this.nextId;
			$item = $('<div class="cb-gallery-item"></div>');
			$item.data('data', data ).data('editor', this ).data('itemId', itemId ).attr('id', this.id+'-Item-'+itemId );
			$image = $('<img src="'+ContentBuilder.basePath+'/assets/images/s.gif" />');
			$remove = $('<a class="cb-gallery-remove" href="#remove" data-act="remove"></a>');
			$screen = $('<div class="cb-gallery-screen"></div>');
			$item.append($remove).append($image).append($screen);
			$item.width( this.itemW).height( this.itemH );

			$remove.click(function(){
				var $curr = $(this).closest('.cb-gallery-item');
				$editor = $curr.data('editor');
				var act = $(this).data('act');
				if( act == 'remove' ) {
					$editor.removeItem( $curr.data('itemId') );
				}
				return false;
			});
			$screen.click(function(){
				$item = $(this).closest('.cb-gallery-item');
				if( $item.data('mouse-enabled') ) {
					var $item = $(this).closest('.cb-gallery-item');
					$editor = $item.data('editor');
					$editor.editItem( $item );
				}
				return false;
			});
			$item.mouseenter(function(){
				if( $(this).data('mouse-enabled') ) {
					$(this).addClass('cb-gallery-item-hover');
				}
			}).mouseleave(function(){
				if( $(this).data('mouse-enabled') ) {
					$(this).removeClass('cb-gallery-item-hover');
				}
			}).data('mouse-enabled', true );

			this.nextId++;
			this.previewItem( $item );

			return( $item );

		},

		/**
		 * Remove item by id
		 * @param itemId
		 */
		removeItem : function( itemId ) {
			$( '#'+this.id+'-Item-'+itemId ).remove();
			this.block.trigger('cb-updated');
			this.updatedItems();
		},

		/**
		 * Edit item data
		 * @param $item
		 * @return {Boolean}
		 */
		editItem : function( $item ) {

			if( ContentBuilder.isEditMode( this.builder ) ) {
				return false;
			}

			//create form && asign action
			if( this.form.length != 1 ) {
				this.form = ContentBuilder.FormCreate( this.id+'-Form', this.config.form );
				this.block.prepend( this.form );
				this.form.bind('cb-control', $.proxy( this.onFormHandle, this ) );
				$arrow = $('span.arrow', this.form );
				$arrow.data('default-top', parseInt($arrow.css('top')) );
				//assign extra action
				$('a[data-act]', this.form ).click(function(){
					var $form = $(this).closest('form');
					var $editor = $(this).closest('.content-block').data('editor');
					var act = $(this).data('act');
					if( act == 'browse' ) {
						ContentBuilder.ModalWindow('image-library', $.proxy( $editor.onModalHandle, $editor ), 'change-image' );
					}
					return false;
				});
			}

			//set form position
			$f = $('div.cb-form', this.form );
			$arrow = $('span.arrow', this.form );
			var left = this.elem.offset().left - this.builder.offset().left;
			var top = $item.position().top+$item.height();
			$f.css('left', (left*-1)+'px').css('top', top+'px' );
			$f.width( this.builder.width() );
			$arrow.css('left', ($item.position().left+(($item.width()-$arrow.width())/2) )+'px' ).css('top', (top+$arrow.data('default-top'))+'px' );

			//assign form data
			this.form.data('item', $item );
			this.form[0].reset();
			var data = $item.data('data');
			data.source = ContentBuilder.baseToUrl( data.source );
			data.link = ContentBuilder.baseToUrl( data.link );
			ContentBuilder.FormData( this.form, data );
			this.form.show();
			this.block.trigger('cb-editing', [true] );

		},

		/**
		 * On form cancel/submit handle
		 * @param event
		 * @param action
		 */
		onFormHandle : function( event, action ) {
			$form = $(event.currentTarget);
			$item = $form.data('item');
			switch( action ) {
				case 'submit':
					var formData = ContentBuilder.FormData( $form );
					$item.data('data', formData );
					$editor.previewItem( $item );
					$form.hide();
					this.close();
					//$editor.block.trigger('editing',[false]);
					$editor.block.trigger('cb-updated');
					return false;
				break;
				case 'cancel':
					this.previewItem( $item, $item.data('data') );
					$form.hide();
					this.close();
				break;
			}
		},

		close : function(){
			this.block.trigger('cb-editing',[false]);
		},

		/**
		 * Preview item
		 * @param $item
		 * @param data
		 */
		previewItem : function( $item, data ) {
			if( data === undefined ) {
				data = $item.data('data');
			}
			$image = $('img', $item );

			if( data.source !== undefined ) {
				$image.attr('src', ContentBuilder.baseToUrl(data.source) );
				$image.css('width','100%');
			}


		},

		/*
		loadImage : function( url ) {

			var $editor = this;
			this.preview.data('loaded', false );
			this.preview.removeClass('empty').addClass('loading');
			$('<img/>').attr('src', url ).load( function() {
				$editor.preview.data('loaded', true );
				$editor.preview.removeClass('loading');
				var $img = $('img', $editor.preview );
				$img.attr('src', url );
				$img.width( this.width );
				$img.height( this.height );
				$img.data('width', this.width );
				$img.data('height', this.height );
				$editor.data.source = url;
				$editor.update();
				//$editor.data.width = this.width;
				//$editor.data.height = this.height;
			});

		},
		*/

		getData : function() {
			var data = $.extend({}, this.data);
			var items = [];
			this.items.children('.cb-gallery-item:not(.cb-gallery-add)').each(function(){
				var $item = $(this);
				var itemData = $.extend( {}, $item.data('data') );
				itemData.source = ContentBuilder.base( itemData.source );
				items.push( itemData );
			});
			data.itemWidth = this.itemW;
			data.itemHeight = this.itemH;
			data.items = items;
			return( data );
		}

	};

})(jQuery);