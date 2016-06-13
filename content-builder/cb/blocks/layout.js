(function($){

	window.ContentBlockLayout = {

		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: {},

		//childs	: [],

		cont		: {},
		column		: [],
		layout		: {},
		placeholder	: {},
		layoutType	: '12',
		types		: {},
		builder 	: null,

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');

			this.column = [];

			if( this.type != 'root' ) {
				$control = this.block.data('control');
				$control.addClass('static');
				$block.children('.cb-placeholder-top,.cb-placeholder-bottom').addClass('layout');
			}

			$elem = $('<div class="cb-layout cb-clearfix"></div>');
			this.block.append( $elem );
			this.cont = $elem;

			this.layout = $('<div id="'+this.id+'-Layout" class="cb-clearfix cb-layout-columns row-fluid"></div>');
			this.cont.append( this.layout );

			this.placeholder = $('<div id="'+this.id+'-Placeholder" class="cb-clearfix cb-layout-placeholders row-fluid"></div>');
			this.cont.append( this.placeholder );

			return( this );

		},

		/**
		 * Initialize
		 */
		init : function() {

			this.builder = this.block.data('builder');

			$control = this.block.data('control');
			if( $control ) {
				$editor = this;
				$types = $('<ul class="cb-control-types cb-clearfix"></ul>');
				for( var i=0; i<this.config.types.length; i++ ) {
					$types.append('<li data-type="'+this.config.types[i]+'" class="layout-'+this.config.types[i]+'"><em></em></li>');
				}
				$types.width( this.config.types.length*45 );
				$('div:first', $control ).append( $types);
				this.types = $types;
				$('li', this.types).click(  $.proxy( this.onTypeButtonHandle, this ) );
			}

			var data = this.data;
			if( data.layout ) {
				this.setLayout( data.layout );
			} else {
				this.setLayout( this.config.layout );
			}

			if( data.childs ) {
				for( i=0; i<data.childs.length; i++ ) {
					var columnChilds = data.childs[i];
					//this.column[i].empty();
					ContentBuilder.createBlocks( this.column[i], columnChilds );
				}
			}

			if( this.block.hasClass('layout') ) {
				this.builder.bind('cb-dragging', $.proxy( this.ResizeDroppableArea, this )  );
			}
		},

		onTypeButtonHandle : function( e ) {

			var $button = $(e.currentTarget);
			var isResize = this.layoutType != $button.data('type');
			this.setLayout( String($button.data('type')) );
			if( isResize ) {
				$('.content-block', this.cont ).trigger('cb-resize');
			}
			this.block.trigger('cb-updated');
		},

		setLayout : function( layoutType ) {

			var layoutColumn = String(layoutType).split('-').length;
			this.cont.removeClass('layout-'+this.layoutType );
			this.cont.addClass('layout-'+ layoutType );
			var columns = this.column.length;
			var $editor = this;

			if( columns != layoutColumn ) {

				if( this.column.length < layoutColumn ) {
					//Create New columns
					for( var index=columns; index<layoutColumn; index++ ) {
						//Create childs
						var $childs = $('<div id="'+this.id+'-Column-'+index+'"></div>');
						$childs.addClass('cb-layout-column');
						this.layout.append( $childs );
						this.column.push( $childs );
						//Create placeholder
						//
						$placeholder = $('<div id="'+this.id+'-Placeholder-'+index+'"><div><span><em>'+this.type+'</em></span></div></div>');
						$placeholder.addClass('cb-layout-placeholder');
						this.placeholder.append( $placeholder );
						$placeholder.disableSelection();
						if( this.type == 'layout' ) {
							var c = 'ABCDEFG';
							$('em', $placeholder).text('column ('+(c[this.column.length-1])+')');
						}
						$('div', $placeholder ).data('container', $childs.attr('id') ).droppable({
							accept : 'div.content-block, li.toolbar-item',
							hoverClass: "placeholder-over",
							tolerance: "pointer",
							drop: function( event, ui ) {
								$target = $(event.target);
								if( $target.parent().hasClass('cb-layout-placeholder') ) {
									ContentBuilder.PlaceholderDropHandle( ui.draggable, $('#'+$( this ).data('container') ), 'append' );
								}
							}
						});
					}
				} else {
					//Merge Columns
					var $container = this.column[ layoutColumn-1 ];
					for( var i=columns; i>layoutColumn; i-- ) {
						var $c = this.column[i-1];
						//merge data
						$c.children().each( function(){
							$container.append( $(this) );
						});
						var placeholderId = $c.attr('id').replace('-Column-','-Placeholder-');
						$('#'+placeholderId).remove();
						$c.remove();
					}
					this.column = [];
					$('.cb-layout-column', this.layout ).each(function(){
						$editor.column.push( $(this) );
					});
				}

			}

			var columnClass = layoutType.split('-');
			for( var j=0; j<this.column.length; j++ ) {
				var colName = 'span'+columnClass[j];
				var $column = this.column[j];
				if( $column.data('colName') ) {
					$column.removeClass( $column.data('colName') );
				}
				$column.addClass(colName);
				$column.data('colName', colName );
				$placeholder = $('#'+ $column.attr('id').replace('-Column-','-Placeholder-') );
				if( $placeholder.data('colName') ) {
					$placeholder.removeClass( $placeholder.data('colName') );
				}
				$placeholder.addClass(colName);
				$placeholder.data('colName', colName );
			}

			this.layoutType = layoutType;
			if( this.types.length ) {
				$('li', this.types ).removeClass('act');
				$('li[data-type="'+layoutType+'"]', this.types).addClass('act');
			}

		},

		ResizeDroppableArea : function( event, state ) {
			var $columnsWrap = this.layout;
			var $placeholdersWrap = $columnsWrap.next('.cb-layout-placeholders');
			var $placeholders = $placeholdersWrap.find('.ui-droppable');
			var $columns = $columnsWrap.find('.cb-layout-column');
			var totalHeight = $columnsWrap.height();
			var placeholderDefaultHeight = $placeholdersWrap.height();
			$.each( $columns, function( index, elem ) {
				var $column = $(elem);
				var columnHeight = $column.height();
				if( totalHeight > columnHeight && state ) {
					$placeholders.eq( index ).css({
						'top': -(totalHeight - columnHeight),
						'height': totalHeight - columnHeight + placeholderDefaultHeight
					});
				} else {
					$placeholders.eq( index ).css({
						'top': 0,
						'height': placeholderDefaultHeight
					});
				}
			});
		},

		setData : function( data ) {

			this.data = data;
			if( data.layout == undefined ) {
				this.data.layout = this.config.layout;
			}
			var childs = data.childs;
			var column = this.data.layout.split('-');
			if( column.length == 1 ) {
				this.data.childs = [];
				this.data.childs.push( childs );
			}

		},

		getData : function(){

			var data = {};
			data.layout = this.layoutType;
			data.childs = [];
			for( i=0; i<this.column.length; i++ ) {
				data.childs.push( ContentBuilder.getContainerData( this.column[i] ) );
			}
			var column = data.layout.split('-');
			if( column.length == 1 ) {
				data.childs = data.childs[0];
			}
			return( data );
		}

	};
})(jQuery);