

(function($) {
	/**
	 * Block Class
	 * @type {Object}
	 */
	//window.cb.Block
	//window.cb
	window.ContentBlock  = {
		block	: {},
		id		: '',
		name 	: '',
		type	: '',
		config 	: {},
		data	: {},
		create : function( $block ){
			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');
			return( this );
		},
		init : function() {
		},
		setData : function( data ) {
			this.data = data;
		},
		getData : function(){
			return( this.data );
		}
	};

	/**
	 * Content Builder Main Class
	 * @type {Object}
	 */
	window.ContentBuilder = {

		config		: {},
		nextId 		: 1,
		instances 	: [],
		blocks		: {},
		basePath	: '',
		ajaxUrl		: '',
		version		: '',
		trails		: {},
		trailKeys	: [],
		trailUrl	: [],

		/**
		 * Initialize builder, assign blocks
		 * @param config
		 */
		init : function( config ) {

			this.blocks = config.blocks;
			this.basePath = config.basePath;
			this.ajaxUrl = config.ajaxUrl;
			this.version = config.version;
			this.config = config;
			this.trails = config.trails;

			for( var i in this.trails ) {
				this.trailKeys.push( i );
				this.trailUrl.push( this.trails[i] );
			}
		},

		updateToolbar : function() {
			var $el = $toolbar.parent();
			var offsetTop = $el.data('offsetTop');
			var scrollTop = $(window).scrollTop();
			var wpAdminBarHeight = $('#wpadminbar').height();

			if( scrollTop + wpAdminBarHeight >= offsetTop ) {
				$el.addClass('fixed').width( $('#wp-content-wrap').width() ).css('top', wpAdminBarHeight );
			} else {
				$el.removeClass('fixed').width('auto');
			}
		},

		/**
		 * Replace textarea
		 * @param name
		 * @param config
		 */
		replace : function( name, config ) {

			$builder = $('<div id="'+name+'-ContentBuilder" class="cb-theme content-builder"></div>').insertAfter( $('#'+name ) );
			$builder.data('instance', name );
			$builder.addClass('wordpress').css('width', this.config.width+'px');
			if($.browser.msie &&  parseInt($.browser.version) == 7 ) {
				$builder.addClass('cb-ie7');
			}

			//WP ACTIVE EDITOR
			$builder.mousedown(function(e){
				$active = $('div.content-block.cb-edit-mode');
				if( $active.length ) {
					return;
				}
				window.wpActiveEditor = $(this).data('instance');
			});

			var $root = ContentBuilder.createBlock('root', {layout:'a',editor:'ContentBlockLayout'} );
			ContentBuilder.appendBlock( $builder, $root, false );
			$builder.data('root', $root );
			this.instances[ name ] = $builder;

			var store = {};
			try {
				var jsonString = '';
				//parse shortcode
				var value = $('#'+name).val();
				var openTagStart = value.indexOf("[content-builder");
				var closeTagStart = value.indexOf('[/content-builder]', openTagStart );
				if( openTagStart != -1 && closeTagStart != -1 ) {
					var openTagEnd = value.indexOf(']', openTagStart );
					jsonString = value.substring( openTagEnd+1, closeTagStart );
				}
				//console.log( jsonString );
				if( jsonString.length > 0 ) {
					//Run some code here
					store = $.parseJSON( jsonString );
				}
				if( store.block == 'root' ) {
					this.nextId = parseInt(store.nextId);
					ContentBuilder.store( $builder, store );
					//Enable Content builder tab
					$('li[data-act="cb"]', $('#ContentBuilder-WP-Tabs') ).trigger('click');
				}
			} catch(err) {
				//Handle errors here
				console.log(err);
			}

			ContentBuilder.initBlock( $root );

			if( $('#ContentBuilder-Toolbar').length == 1 ) {
				$toolbar = $('#ContentBuilder-Toolbar');
			} else {
				$toolbar = $(this.config.toolbar);
				$root.prepend( $toolbar );
			}
			if($.browser.msie &&  parseInt($.browser.version) == 7 ) {
				$toolbar.addClass('cb-ie7');
			}

			$('.cb-center', $toolbar).css('width', this.config.width+'px');
			$builder.data('toolbar', $toolbar);
			$dropdown = $('.cb-toolbar-dropdown:first', $toolbar );
			$dropdown.data('container', $('.cb-layout-column', $root ).eq(0) );
			$ul = $('ul', $dropdown );
			for( var i in ContentBuilder.blocks ) {
				if( ContentBuilder.blocks.hasOwnProperty( i ) ) {
					var bData = ContentBuilder.blocks[ i ];
					$btn = $('<li class="'+bData.name+' toolbar-item" data-block="'+bData.name+'"><em></em>'+bData.label+'</li>');
					$ul.append( $btn );
				}
			}
			$builder.css('min-height', $ul.height()+'px' );

			$('span', $dropdown ).click(function(){
				$(this).closest('.cb-toolbar').addClass('cb-toolbar-open');
				$t = $(this).closest('.cb-toolbar-dropdown');
				$t.addClass('open');
			}).disableSelection();
			$('ul', $dropdown ).mouseleave(function(){
				$(this).closest('.cb-toolbar').removeClass('cb-toolbar-open');
				$t = $(this).closest('.cb-toolbar-dropdown');
				$t.removeClass('open');
			});
			$('li', $dropdown ).data('builder', $builder).click(function(){
				$(this).closest('.cb-toolbar').removeClass('cb-toolbar-open');
				$t = $(this).closest('.cb-toolbar-dropdown');
				$t.removeClass('open');
				var type = $(this).data('block');
				if( type !== undefined ) {
					$child = ContentBuilder.createBlock( type );
					ContentBuilder.appendBlock( $t.data('container'), $child, true, false );
					if( ! ContentBuilder.elemIsVisible( $child ) ) {
						$(document).scrollTop( $child.offset().top );
					}
					$builder.trigger('cb-updated');
				}
				return false;
			}).draggable({
				start : function( event, ui ){
					var $li = $(this);
					var $builder = $li.data('builder');
					$builder.addClass('cb-drag-mode');
					$builder.trigger('cb-dragging', [ true ] );
				},
				stop : function( event, ui ){
					var $builder = $(this).data('builder');
					$builder.removeClass('cb-drag-mode');
					$builder.trigger('cb-dragging', [ false ] );
				},
				cursor: 'move',
				cursorAt: {top:0,left:-10},
				helper: function(event) {
					var $li = $(event.target);
					return  $('<div/>', {
								'class': 'cb-drag-helper',
								'text': 'Add new: ' + $li.text()
							}).wrapInner('<label />');
				},
				appendTo: 'body',
				revert: false
			}).disableSelection();
			$('li:last', $toolbar).addClass('last');

			//assign updated event
			$builder.bind('cb-updated', function( e ){
				var data = ContentBuilder.store( $builder );
				var string = JSON.stringify( data );
				//WORDPRESS SHORTCODE
				string = '[content-builder]'+string+'[/content-builder]';
				$('#'+ $builder.data('instance') ).val( string );
				e.stopPropagation();
			});

			//assign editing event
			$builder.bind('cb-editing', function( e, status ) {
				var $toolbar = $(e.currentTarget).data('toolbar');
				$toolbarLock = $('.cb-toolbar-locker', $toolbar );
				if( status ) {
					//fix bug which hides form
					$('#wpbody-content').css('overflow','visible');
					$(e.currentTarget).addClass('cb-edit-mode');
					$('div.content-block.cb-block-over', e.currentTarget ).removeClass('cb-block-over');
					$(e.target).addClass('cb-edit-mode');
					$toolbarLock.fadeIn(250);
				} else {
					$('#wpbody-content').css('overflow','hidden');
					$(e.currentTarget).removeClass('cb-edit-mode');
					$(e.target).removeClass('cb-edit-mode');
					$toolbarLock.fadeOut(250);
				}
				e.stopPropagation();
			});

			$(window).scroll(ContentBuilder.updateToolbar);
			$(window).resize(ContentBuilder.updateToolbar);
			$toolbar.parent().data('offsetTop', $toolbar.offset().top);
		},

		elemIsVisible : function (elem) {
			var docViewTop = $(window).scrollTop();
			var docViewBottom = docViewTop + $(window).height();

			var elemTop = $(elem).offset().top;
			var elemBottom = elemTop + $(elem).height();

			return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
		},

		/**
		 * Create block
		 * @param {String} type
		 * @param {Object} config
		 * @return {jQuery}
		 */
		createBlock : function( type, config ) {

			//set block config
			config = config === undefined ? {} : config;
			var cfg = ContentBuilder.blocks[ type ];
			if( cfg !== undefined ) {
				config = $.extend(  {}, cfg, config );
			}

			//create unique instance
			var instance = 'ContentBuilder-';
			if( $('#'+instance+config.id).length != 0 ) {
				config.id = undefined;
			}
			if( config.id !== undefined ) {
				instance += config.id;
				if( parseInt(config.id) >= ContentBuilder.nextId ) {
					ContentBuilder.nextId = parseInt(config.id)+1;
				}
			} else {
				instance += ContentBuilder.nextId;
				ContentBuilder.nextId++;
			}

			var $block = $('<div id="'+instance+'" class="cb-clearfix content-block '+type+'"></div>');
			$block.data('id', instance );
			$block.data('type', type );
			$block.data('config', config );

			//create control for blocks
			if( type != 'root' ) {

				$control = $('<div id="'+instance+'-Control" class="cb-control"><div>'+
					'<label class="cb-drag-holder"><span class="icon icon-drag"></span>'+config.label+'</label>'+
					'<span class="icon-bg"></span>'+
					'<span class="info">Width <em>960</em>px</span>'+
					'<span class="icon icon-copy"></span>'+
					'<span class="icon icon-trash"></span>'+
					'</div></div>');
				$block.append( $control );
				$block.data('control', $control );
				$('div:first', $control ).append('<div class="cb-control-locker"></div>');
				$control.disableSelection();

				$block.append('<div class="cb-placeholder-top"><em class="line"></em><em class="right"></em><em class="left"></em></div>');
				$block.append('<div class="cb-placeholder-bottom"><em class="line"></em><em class="right"></em><em class="left"></em></div>');
			}

			if( config.layout === undefined ) {
				//$block.append('<em class="cb-border cb-border-top"></em>');
				$block.append('<span class="cb-border cb-border-bottom"><em></em></span>');
				$block.append('<span class="cb-border cb-border-left"><em></em></span>');
				$block.append('<span class="cb-border cb-border-right"><em></em></span>');
			}

			if( config.editor !== undefined ) {
				var EditorClass = window[ config.editor ];
				if( EditorClass !== undefined ) {
					$editor = $.extend( {}, ContentBlock, EditorClass );
					$block.data('editor', $editor.create( $block ) );
				}
			}
			return( $block );
		},

		/**
		 * Append new block
		 * @param {jQuery} 	$parent
		 * @param {jQuery} 	$block
		 * @param {boolean}	init
		 * @return {jQuery}
		 */
		appendBlock : function( $container, $block, init, prepend ) {
			init = init === undefined ? true : init;
			prepend = prepend === undefined ? false : prepend;
			if( prepend ) {
				$container.prepend( $block );
			} else {
				$container.append( $block );
			}
			var type = $block.data('type');
			if( type == 'root' ) {
				$block.data('builder', $container );
			} else {
				$builder = $block.closest('div.content-builder');
				$block.data('builder', $builder );
			}
			if( init === true ) {
				ContentBuilder.initBlock( $block );
			}
			return( $block );
		},

		store : function( $builder, data ) {

			var $root = $builder.data('root');
			if( data === undefined ) {
				return( ContentBuilder.getBlockData( $root ) );
			} else {
				ContentBuilder.setBlockData( $root, data );
			}
		},

		/**
		 * Get block data
		 * @param 	{jQuery} $block
		 * @return 	{Object}
		 */
		getBlockData : function( $block ) {
			var data = {};
			data.block = $block.data('type');
			data.id = $block.data('id').replace('ContentBuilder-','');
			if( data.block == 'root' ) {
				data.version = this.version;
				data.nextId = this.nextId;
			}
			var config = $block.data('config');
			if( config.editor !== undefined ) {
				var $editor = $block.data('editor');
				data = $.extend( data, $editor.getData() );
			}
			return( data );
		},

		/**
		 * Set block data
		 * @param {jQuery} 	$block
		 * @param {Object}	data
		 */
		setBlockData : function( $block, data ) {
			var config = $block.data('config');
			if( config.editor !== undefined ) {
				var $editor = $block.data('editor');
				$editor.setData( data );
			}
		},

		/**
		 * Init block
		 * @param $block
		 */
		initBlock : function( $block ) {

			var type = $block.data('type');
			var config = $block.data('config');

			if( type != 'root' ) {
				$( $block ).draggable({
					start : function( event, ui ){
						$block = $(event.currentTarget);
						$block.fadeTo(0,0.5);
						$helper = ui.helper;
						$control = $block.data('control');
						if( $control ) {
							var label = $('label', $control ).text();
							$helper.attr('class', 'cb-drag-helper').empty().fadeTo(0,1);
							$helper.html('<label>'+label+'</label>');
							$helper.data('block', $block );
						}
						var $builder = $block.data('builder');
						$builder.addClass('cb-drag-mode');
						$builder.trigger('cb-dragging', [ true ] );
					},
					stop : function( event, ui ){
						$block = $(event.target);
						$helper = ui.helper;
						var $builder = $block.data('builder');
						$builder.removeClass('cb-drag-mode');
						$block.fadeTo(500, 1, function(){
							$(this).attr('style', null );
						});
						$helper.remove();
						if( $block.data('dropped') ) {
							$block.data('dropped', false );
							$block.trigger('cb-updated');
						}
						$builder.trigger('cb-dragging', [false] );
					},
					cursor: "move",
					cursorAt:{top:0,left:-10},
					handle:'label.cb-drag-holder',
					helper:'clone',
					opacity:0.5,
					revert:false
				});
			}

			var $control = $block.data('control');
			if( $control ) {
				$('span.info em', $control ).text( $block.width() );
				$('.icon-trash', $control ).click( function(){
					$block = $(this).closest('div.content-block');
					$builder = $block.data('builder');
					label = $block.find('label').html().replace(/(<([^>]+)>)/ig,"");
					if( window.confirm('Confirm to delete "' + label + '" block') ) {
						$block.remove();
						$builder.trigger('cb-updated');
					}
				});
				$('.icon-copy', $control ).click( function(){
					$block = $(this).closest('div.content-block');
					$builder = $block.data('builder');
					var data = ContentBuilder.getBlockData( $block );
					delete( data.id );
					$child = ContentBuilder.createBlock( data.block );
					$child.data('builder', $block.data('builder') );
					$child.insertAfter( $block );
					ContentBuilder.setBlockData( $child, data );
					ContentBuilder.initBlock( $child );
					$block.trigger('cb-updated');
					return false;
				});
				if( $control.hasClass('static') ) {
					$block.bind('cb-resize', function(){
						$control = $(this).data('control');
						if( $control ) {
							$('span.info em', $control ).text( $(this).width() );
						}
					});
				}
			}

			if( config.layout === undefined && $block.children('.cb-border').length > 1 ) {
				$block.mouseenter(function(){
					$builder = $(this).data('builder');
					if( !$builder.hasClass('cb-drag-mode') && !$builder.hasClass('cb-edit-mode') ) {
						$('span.info em', $(this).data('control')).text( $(this).width() );
						$(this).children('.cb-border-top,.cb-border-bottom,.cb-control').width( $(this).width()+20 );
						$(this).children('.cb-border-left,.cb-border-right').height( $(this).height()+20 );
						$(this).children('.cb-border-bottom').css('top', ($(this).height()+8)+'px' );
						$(this).addClass('cb-block-over');
					}
				}).mouseleave(function(){
					$(this).removeClass('cb-block-over');
				});
			}

			$block.children('div.cb-placeholder-top,div.cb-placeholder-bottom').droppable({
				greedy: true,
				hoverClass: "cb-holder-over",
				tolerance: "pointer",
				accept : "div.content-block, li.toolbar-item",
				drop: function( event, ui ) {
					var $target = $(event.target);
					var $block = ui.draggable;
					if( $target.hasClass('cb-placeholder-top') || $target.hasClass('cb-placeholder-bottom') ) {
						var $friend = $target.parent();
						ContentBuilder.PlaceholderDropHandle( ui.draggable, $friend, ($target.hasClass('cb-placeholder-top')?'insertBefore':'insertAfter') );
					}
				}
			});

			if( config.editor !== undefined ) {
				var $editor = $block.data('editor');
				$editor.init();
			}

		},

		PlaceholderDropHandle : function( block, target, method ){

			var isNew = block.hasClass('toolbar-item');
			if( isNew ) {
				block = ContentBuilder.createBlock( block.data('block') );
			}
			if( method == 'insertBefore' ) {
				block.insertBefore( target );
			}
			if( method == 'insertAfter' ) {
				block.insertAfter( target );
			}
			if( method == 'append' ) {
				target.append( block );
			}
			if( method == 'prepend' ) {
				target.prepend( block );
			}
			$builder = target.closest('div.content-block').data('builder');
			if( isNew ) {
				//initialize new element
				block.data('builder', $builder );
				ContentBuilder.initBlock( block );
			} else {
				block.data('dropped', true );
				block.trigger('cb-resize');
			}
			$builder.trigger('cb-updated');
		},

		createBlocks : function( $container, childs ) {
			var blocks = [];
			for( var j=0; j<childs.length; j++ ) {
				var childData = childs[ j ];
				var params = {};
				if( childData.id !== undefined ) {
					params.id = childData.id;
				}
				var $child = ContentBuilder.createBlock( childData.block, params );
				ContentBuilder.appendBlock( $container, $child, false );
				ContentBuilder.setBlockData( $child, childData );
				ContentBuilder.initBlock( $child );
				blocks.push( $child );
			}
			return( blocks );
		},

		getContainerData : function( $container ) {
			var childs = [];
			$container.children('div.content-block').each(function(){
				var data = ContentBuilder.getBlockData( $(this) );
				childs.push( data );
			});
			return( childs );
		},

		isEditMode : function( $builder ) {
			return( $builder.hasClass('cb-edit-mode') );
		},


		/**
		 * Transform url to BASE address
		 * @param url
		 * @return {*}
		 */
		base : function( url ) {
			var re = new RegExp('^('+this.trailUrl.join('|')+')', "g");
			var match = re.exec(url);
			if( match != null ) {
				var keyIndex = $.inArray( match[0], this.trailUrl );
				var keyName = this.trailKeys[ keyIndex ];
				url = url.replace( match[1], '~'+keyName );
			}
			return( url );
		},

		/**
		 * Transform BASE address to url
		 * @param url
		 * @return {*}
		 */
		baseToUrl : function( url ) {
			var re = new RegExp('^~('+this.trailKeys.join('|')+')', "g");
			var match = re.exec(url);
			if( match != null ) {
				var urlIndex = $.inArray( match[1], this.trailKeys );
				var urlFull = this.trailUrl[ urlIndex ];
				url = url.replace( match[0], urlFull );
			}
			return( url );
		},

		PlaceholderDropHandle3 : function( event, ui ) {
			var $target = $(event.target);
			var $block = ui.draggable;
			if( ui.draggable.hasClass('toolbar-item') ) {
				type = ui.draggable.data('block');
				$block = ContentBuilder.createBlock( type );
			}
			if( $target.hasClass('cb-layout-column') ) {
				$target.append( $block );
			}
			if( $target.hasClass('cb-placeholder-top') || $target.hasClass('cb-placeholder-bottom') ) {
				var $friend = $target.parent();
				if( $target.hasClass('cb-placeholder-top') ) {
					$block.insertBefore( $friend );
				} else {
					$block.insertAfter( $friend );
				}
			}
			$builder.trigger('cb-updated');
			if( ui.draggable.hasClass('toolbar-item') ) {
				$builder = $target.closest('div.content-builder');
				$block.data('builder', $builder );
				ContentBuilder.initBlock( $block );
				return false;
			} else {
				$block.data('dropped', true );
			}

		},

		FormCreate : function( id, content ) {
			$form = $('<form id="'+id+'" class="cb-form">'+
				'<span class="arrow"></span>'+
				'<div class="cb-form"><fieldset class="bg"></fieldset>'+
					content+
					'<fieldset class="control cb-clearfix">'+
						'<button class="save" type="submit">Done</button> or <a href="#" control="cancel">cancel</a>'+
					'</fieldset>'+
				'</div>'+
			'</form>');
			$('[control]', $form ).click( function(){
				$form = $(this).closest('form');
				$form.trigger('cb-control', [$(this).attr('control')] );
				return false;
			});
			$form.submit(function(){
				$(this).trigger('cb-control', ['submit'] );
				return false;
			});
			ContentBuilder.FormInit( $form );
			return( $form );
		},

		FormInit : function( $form ) {

			var id = $form.attr('id');

			//replace selects to choice's
			var $selects = $('select.cb-choice', $form );
			if( $selects.length > 0 ) {
				$selects.each(function(){
					var $select = $(this);
					var $choice = $('<ul class="choice"></ul>');
					$choice.data('select', $select );
					$select.data('choice', $choice );
					$('option', $select).each(function(){
						$opt = $(this);
						$li = $('<li data-value="'+$opt.attr('value')+'"><em>'+$opt.text()+'</em></li>');
						$choice.append($li);
						if( $select.val() == $li.data('value') ) {
							$li.addClass('active');
						}
						$li.click(function(){
							$select = $(this).closest('ul.choice').data('select');
							$select.val( $(this).data('value')).trigger('change');
						});
					});
					$select.change(function(){
						var $choice = $(this).data('choice');
						$('li[data-value]', $choice).removeClass('active');
						$('li[data-value="'+$(this).val()+'"]', $choice ).addClass('active');
					}).hide();
					$choice.insertBefore($select);
				});
			}

			//init optional fields
			var $optional = $('fieldset.optional', $form );
			if( $optional.length > 0 ) {
				$buttons = $('<ul class="optional-btn"></ul>');
				$buttons.insertBefore( $form.find('fieldset.control') );
				$optional.each(function(){
					var $fieldset = $(this);
					var label = $('label', $fieldset).text();
					var input = $(':input', $fieldset ).eq(0);
					$btn = $('<li>'+ label.replace(':','') +'</li>');
					$btn.attr('id', id +'-Button-'+input.attr('name')  );
					$fieldset.attr('id', id+'-Fieldset-'+input.attr('name') );
					$btn.click( function() {
						$(this).hide();
						$('#'+$(this).attr('id').replace('-Button-','-Fieldset-') ).fadeIn('slow');
						return false;
					});
					$remove = $('<a id="'+id+'-Remove-'+input.attr('name')+'" class="remove" href="#"></a>');
					$('p', $fieldset).append( $remove );
					$remove.click(function(){
						var fieldset = $(this).closest('fieldset');
						fieldset.hide();
						$(':input', fieldset ).val('');
						$('#'+$(this).attr('id').replace('-Remove-','-Button-') ).fadeIn('slow');
						return false;
					});
					$buttons.append( $btn );
				});
			}

		},

		FormData : function( $form, data ) {
			var i = 0;
			if( data === undefined ) {
				var values = {};
				var fields = $form.serializeArray();
				for( i=0; i<fields.length; i++ ) {
					values[ fields[i].name ] = fields[i].value;
				}
				$('input[type="checkbox"]:checked', $form ).each(function(){
					values[ $(this).attr('name') ] = $(this).attr('value');
				});
				return( values );
			} else {
				$(':input', $form ).each(function(){
					var value = data[ $(this).attr('name') ];
					if( value != undefined ) {
						$(this).val( value );
					}
					if( $(this).hasClass('cb-choice') ) {
						var $choice = $(this).data('choice');
						$('li[data-value]', $choice).removeClass('active');
						$('li[data-value="'+$(this).val()+'"]', $choice ).addClass('active');
					}
					if( $(this).attr('type') == 'checkbox' ) {
						if( value === $(this).attr('value') ) {
							$(this).attr('checked','checked');
						}
					}
				});
				$('fieldset.optional', $form ).each(function(){
					var $fieldset = $(this);
					var $btn = $('#'+$fieldset.attr('id').replace('-Fieldset-','-Button-'));
					var input = $(':input', $fieldset ).eq(0);
					var value = input.val();
					if( value.length > 0 ) {
						$fieldset.show();
						$btn.hide();
					} else {
						$fieldset.hide();
						$btn.show();
					}
				});
			}
		},

		EncodeEntities : function(s) {
			return $("<div/>").text(s).html();
		},

		DecodeEntities : function(s) {
			return $("<div/>").html(s).text();
		},

		ModalWindow : function( type, callback, action ) {

			if( type == 'image-library' || type == 'image-upload' ) {
				//version 3.4
				//http://*/wp-admin/media-upload.php?post_id=102&tab=library
				//http://*/wp-admin/media-upload.php?post_id=102&TB_iframe=1
				$btn = $('#content-add_media');
				if( $btn.length == 1 ) {
					if( $btn.data('default') == undefined ) {
						$btn.data('default', $btn.attr('href') );
					}
					$btn.data('cb-modal-type', type ).data('cb-modal-action', action ).data('cb-modal-callback',  callback );
					var url = $btn.data('default');
					var tab = type.split('-');
					url = url.replace( '&TB_iframe=1', '&tab='+(tab[1]=='upload'?'type':'library')+'&TB_iframe=1');
					$btn.attr('href', url );
					$btn.trigger('click');
					$btn.attr('href', $btn.data('default') );
				}
				//version 3.5
				$btn = $('.insert-media','#wp-content-media-buttons');
				if( $btn.length == 1 ) {
					$btn.trigger('click');
					$btn.data('cb-modal-type', type ).data('cb-modal-action', action ).data('cb-modal-callback',  callback );
				}

			}

		},

		addUrlParam : function( search, key, val){
			/*
			var newParam = key + '=' + val, params = '?' + newParam;
			// If the "search" string exists, then build params from it
			if (search) {
				// Try to replace an existance instance
				var re = new RegExp('[\?&]' + key + '[^&]*');
				var match = re.exec(search);
				if( match != null ) {
					params = search.replace( match.input.substr(1,match.input.length), newParam );
				}
				// If nothing was replaced, then add the new param to the end
				if ( params === search) {
					params += '&' + newParam;
				}
			}
			*/
			return params;
		}

};

	/**
	 * For Wordpress
	 */

	// WP iskvieciamas media pasirinkimo langas
	$(function() {
		if( window.send_to_editor !== undefined ) {
			window.send_to_editor_wp = window.send_to_editor;
			window.send_to_editor = function(html) {
				var callback = undefined;
				var version = '3.4';
				//version 3.4
				$btn = $('#content-add_media');
				if( $btn.length == 1 ) {
					callback = $btn.data('cb-modal-callback');
				}
				//version 3.5
				if( $btn.length == 0 ) {
					$btn = $('.insert-media','#wp-content-media-buttons');
					if( $btn.length == 1 ) {
						callback = $btn.data('cb-modal-callback');
						version = '3.5';
					}
				}
				if( callback !== undefined ) {
					var data = {};
					data.extra = {};
					var $img = $('img', html );
					//if only image no link
					if( $img.length == 0 ) {
						$img = $(html);
					}
					var cls = $img.attr('class');
					var pattern = /wp-image-(\d+)/;
					if( pattern.test(cls) ) {
						var attachId = cls.match(pattern)[1];
						if( attachId ) {
							data.extra.wp_id = attachId;
						}
					}
					pattern = /size-(\w+)/;
					if( pattern.test(cls) ) {
						var size = cls.match(pattern)[1];
						if( size ) {
							data.extra.wp_size = size;
						}
					}
					var className = $img.attr('class');
					if( className ) {
						data.extra.className = className;
					}
					var source = $img.attr('src');
					if( source ) {
						data.source = source;
					}
					var title = $img.attr('title');
					if( title ) {
						data.alt = title;
					}
					var alt = $img.attr('alt');
					if( alt ) {
						data.alt = alt;
					}
					var $a = $('a', html );
					var link = $a.attr('href');
					if( link === undefined ) {
						$a = $(html);
						link = $a.attr('href');
					}
					if( link ) {
						data.link = link;
					}
					retVal = callback( $btn.data('cb-modal-type'), data, $btn.data('cb-modal-action') );
					$btn.data('cb-modal-callback', null );
					if( version == '3.4' ) {
						tb_remove();
					}
					return retVal;
				}
				window.send_to_editor_wp( html );
			};
		}
	});

	// WP iskvieciamas linko pasirinkimo langas
	/*
	$(function() {


		if( window.wpLink ) {

			window.wplink_beforeOpen_wp = window.wpLink.beforeOpen;
			wpLink.beforeOpen = function(){

				if( window.ContentBlockTinymce.isActiveEditor(wpActiveEditor) ) {
					tinyMCEPopup.init();
					var range = tinymce.get(wpActiveEditor).selection.getRng();
					$('#'+wpActiveEditor).data('range', range );
					var node = tinymce.get(wpActiveEditor).selection.getNode();
					$('#'+wpActiveEditor).data('node', node );
					return;
				}

				window.wplink_beforeOpen_wp();
			};
			window.wplink_isMCE_wp = window.wpLink.isMCE;
			wpLink.isMCE = function() {
				if( window.ContentBlockTinymce.isActiveEditor(wpActiveEditor) ) {
					return true;
				}
				return( window.wplink_isMCE_wp() );
			};

			window.wplink_mceRefresh_wp = wpLink.mceRefresh;
			wpLink.mceRefresh = function() {
				if( window.ContentBlockTinymce.isActiveEditor(wpActiveEditor) ) {
					tinyMCEPopup.restoreSelection();
					var e;
					var ed = tinymce.get(wpActiveEditor);
					var node = $('#'+wpActiveEditor).data('node');
					// If link exists, select proper values.
					if ( e = ed.dom.getParent(node, 'A') ) {
						// Set URL and description.
						$('#url-field').val( ed.dom.getAttrib(e, 'href') );
						$('#link-title-field').val( ed.dom.getAttrib(e, 'title') );
						// Set open in new tab.
						if ( "_blank" == ed.dom.getAttrib(e, 'target') )
							$('#link-target-checkbox').prop('checked', true);

						// Update save prompt.
						$('#wp-link-submit').val( wpLinkL10n.update );
					} else {
						wpLink.setDefaultValues();
					}
					tinyMCEPopup.storeSelection();
					return;
				}
				window.wplink_mceRefresh_wp();
			};

			window.wplink_update_wp = window.wpLink.update;
			wpLink.update = function(){
				$builder = $('#'+wpActiveEditor+'-ContentBuilder');
				if( $builder.length ) {
					if( $builder.css('display') == 'block' ) {
						var attrs = wpLink.getAttrs();
						if( window.wpLink.cb_callback ) {
							window.wpLink.cb_callback( attrs );
						}
						//update
						window.wpLink.cb_callback = undefined;
						wpLink.close();
						return;
					}
				}
				if( window.ContentBlockTinymce.isActiveEditor(wpActiveEditor) ) {
					var range = $('#'+wpActiveEditor).data('range');
					tinymce.get(wpActiveEditor).selection.setRng( range );
					window.wpLink.mceUpdate();
					return;
				}
				window.wplink_update_wp();
				return;
			};

		}

	});
	*/

})(jQuery);
