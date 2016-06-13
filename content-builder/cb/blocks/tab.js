(function($) {
	window.ContentBlockTab = {

		nextId 	: 1,
		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: null,

		builder : null,
		elem	: {},
		header	: {},
		locker : {},
		content	: {},
		addTab	: {},
		form	: {},

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
			$control.addClass('static');
			$block.children('.cb-placeholder-top,.cb-placeholder-bottom').addClass('layout');
			$block.children('.cb-border').remove();

			$elem = $('<div class="cb-tab tabbable cb-clearfix"></div>');
			this.block.append( $elem );
			this.elem = $elem;

			this.header = $('<ul class="nav nav-tabs cb-clearfix"></ul>');
			this.addTab = $('<li class="tab-add"><a href="#">Add New Tab</a></li>');
			this.header.append(this.addTab);
			this.locker = $('<li class="tab-locker"></li>');
			this.header.append( this.locker );
			this.content = $('<div class="tab-content"></div>');
			this.elem.append( this.header );
			this.elem.append( this.content );
			return( this );
		},

		init : function() {

			//builder
			this.builder = this.block.data('builder');
			this.builder.bind('cb-editing', $.proxy( this.onEditHandle, this ) );

			//assign tabs
			if( this.data == null ) {
				var tmp = [
					{label:'Tab1',childs:[]},
					{label:'Tab2',childs:[]},
					{label:'Tab3',childs:[]}
				];
				this.setData( {tabs:tmp} );
			}

			//create tabs
			var tabs = this.data.tabs;
			for( var i=0; i< tabs.length; i++ ) {
				var tab = tabs[i];
				this.createTab( tab );
			}

			this.addTab.click(function(){
				$editor = $(this).closest('div.content-block').data('editor');
				var tabId = $editor.createTab( {label:'Tab-'+($editor.nextId)} );
				$editor.showTab( tabId );
				$editor.block.trigger('cb-updated');
				return false;
			});


			$firstTab = this.header.children().eq(0);
			this.showTab( $firstTab.data('tabId') );

			this.header.sortable({
				axis:'x',
				handle:'span',
				items: "li.tab-link",
				cancel:'li:not(.active)',
				start:function( event, ui ) {
					var $block = $(this).closest('.content-block');
					var $builder = $block.data('builder');
					$builder.addClass('cb-drag-mode');
					$builder.trigger('cb-dragging', [ true ] );
				},
				stop : function( event, ui ) {
					var $block = $(this).closest('.content-block');
					var $builder = $block.data('builder');
					$builder.removeClass('cb-drag-mode');
					$builder.trigger('cb-dragging', [ false ] );
				},
				update:function(event, ui ) {
					$editor = $(this).closest('.content-block').data('editor');
					$editor.block.trigger('cb-updated');
				}
			});



		},

		/**
		 * Builder Edit Mode handle
		 * @param event
		 * @param status
		 */
		onEditHandle : function( event, status ) {

			if( status ) {
				this.locker.show();
				this.header.fadeTo(0,0.5);
				$('.tab-control', this.content ).css('visibility','hidden');
			} else {
				this.locker.hide();
				this.header.fadeTo(0,1);
				$('.tab-control', this.content ).css('visibility','visible');
			}

			//if active current block select active image
			var $currTarget = $(event.target);
			if( status && $currTarget.attr('id') == this.id ) {
				this.header.fadeTo(0,1);
			}

		},

		editTab : function( tabId ) {

			if( ContentBuilder.isEditMode( this.builder ) ) {
				return false;
			}

			//create form && asign action
			if( this.form.length != 1 ) {
				this.form = ContentBuilder.FormCreate( this.id+'-Form', this.config.form );
				this.form.insertAfter( this.header );
				this.form.bind('cb-control', $.proxy( this.onFormHandle, this ) );
			}

			$link = $('#'+this.id+'-TabLink-'+tabId);
			$link.addClass('editing');
			this.locker.show();

			var label = $('span', $link ).text();
			var tabData = {label:label};
			this.form[0].reset();
			this.form.data('tabId', tabId).data('tabData', tabData );
			ContentBuilder.FormData( this.form, tabData );

			//set form position
			$arrow = $('span.arrow', this.form );
			$arrow.css('left', ($link.position().left+10)+'px' );
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
		onFormHandle : function( event, action ) {
			$form = $(event.currentTarget);
			$link = $('#'+this.id+'-TabLink-'+$form.data('tabId') );
			$content = $('#'+this.id+'-TabItem-'+$form.data('tabId') );
			switch( action ) {
				case 'submit':
					var formData = ContentBuilder.FormData( $form );
					$('span', $link ).text( formData.label );
					$placeholder = $content.children('.cb-layout-placeholder:first');
					$('em', $placeholder ).text('tab: ('+formData.label+')');
					$form.hide();
					$link.removeClass('editing');
					this.locker.hide();
					$editor.block.trigger('cb-editing',[false]);
					$editor.block.trigger('cb-updated');
					return false;
				break;
				case 'cancel':
					$form.hide();
					$link.removeClass('editing');
					this.locker.hide();
					this.block.trigger('cb-editing',[false]);
				break;
			}
		},

		showTab : function( tabId ) {

			this.header.children().removeClass('active');
			this.content.children().removeClass('active');
			$('#'+this.id+'-TabLink-'+tabId).addClass('active');
			$('#'+this.id+'-TabItem-'+tabId).addClass('active');

		},

		removeTab : function( tabId ) {

			var index = $('#'+this.id+'-TabLink-'+tabId ).index();
			$('#'+this.id+'-TabLink-'+tabId).remove();
			$('#'+this.id+'-TabItem-'+tabId).remove();
			var prevId = this.header.children().eq(0).data('tabId');
			if( index-1 >= 0 ) {
				var $prevLink = this.header.children('.tab-link').eq( index-1 );
				prevId = $prevLink.data('tabId');
			}
			this.showTab( prevId );

		},

		createTab : function( tab ) {

			var tabId = this.nextId;

			$link = $('<li class="tab-link"><a href="#"><span></span></a><em title="Change Tab Label"></em></li>');
			$('span', $link ).text( tab.label);
			$link.data('editor', this );
			$link.data('tabId', tabId ).attr('id', this.id+'-TabLink-'+tabId );
			$link.insertBefore( this.addTab );
			$('em', $link ).click(function(){
				var $link = $(this).closest('li.tab-link');
				var $editor = $link.data('editor');
				$editor.editTab( $link.data('tabId') );
			});
			$content = $('<div class="tab-pane"></div>');
			$content.data('tabId', tabId).attr('id', this.id+'-TabItem-'+tabId );
			this.content.append( $content );

			$control = $('<div class="tab-control cb-clearfix"></div>');
			$content.append( $control );
			$remove = $('<a href="#" title="Remove Current Tab"></a>');
			$control.append( $remove );
			$remove.data('editor', this ).click(function(){
				$editor = $(this).data('editor');
				var $tabItem = $(this).closest('div.tab-pane');
				$editor.removeTab( $tabItem.data('tabId') );
				$editor.block.trigger('cb-updated');
				return false;
			});

			var $childs = $('<div class="tab-childs"></div>');
			$content.append($childs);

			var $placeholder = $('<div class="cb-layout-placeholder"><div><span><em>drag & drop here</em></span></div></div>');
			$placeholder.disableSelection();
			$content.append($placeholder);
			$('em', $placeholder).text('tab: ('+(tab.label)+')');

			//add tab childs
			if( tab.childs != undefined ) {
				ContentBuilder.createBlocks( $childs, tab.childs );
			}

			//init link
			$link.click(function(){
				$link = $(this);
				$editor = $link.data('editor');
				if( !$link.hasClass('active') ) {
					$editor.showTab( $(this).data('tabId') );
				}
				return false;
			}).droppable({
					accept : 'div.content-block, li.toolbar-item',
					hoverClass: "tab-link-drop",
					tolerance: "pointer",
					drop: function( event, ui ) {
						var $target = $(event.target);
						var $editor = $target.data('editor');
						var containerId = $target.attr('id').replace('-TabLink-','-TabItem-');
						ContentBuilder.PlaceholderDropHandle( ui.draggable, $('.tab-childs', '#'+containerId ), 'prepend' );
						$editor.showTab( $target.data('tabId') );
					}
			}).disableSelection();

			//init placeholder
			$('div', $placeholder ).data('container', $childs ).droppable({
				accept : 'div.content-block, li.toolbar-item',
				hoverClass: "placeholder-over",
				tolerance: "pointer",
				drop: function( event, ui ) {
					var $target = $(event.target);
					if( $target.parent().hasClass('cb-layout-placeholder') ) {
						ContentBuilder.PlaceholderDropHandle( ui.draggable, $(this).data('container'), 'append' );
					}
				}
			});

			this.nextId++;
			return( tabId );
		},

		setData : function( data ) {

			this.data = data;
			if( data.tabs == undefined ) {
				this.data = null;
			}

		},

		getData : function() {
			var tabs = [];
			var data = {};
			this.header.children('li.tab-link').each(function(){
				$content = $('#'+ $(this).attr('id').replace('-TabLink-','-TabItem-') );
				var tab = {};
				tab.label = $('span', $(this) ).text();//$content.children('.tab-label').find('input[name="label"]').val();
				tab.childs = [];
				var childs = $content.children('.tab-childs').eq(0);
				tab.childs = ContentBuilder.getContainerData( $(childs) );
				tabs.push( tab );
			});
			data.tabs = tabs;
			return( data );
		}

	};
})(jQuery);