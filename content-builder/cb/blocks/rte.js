(function($) {

	window.ContentBlockRedactor = {

		block	: {},
		id		: '',
		type	: '',
		config 	: {},
		data	: {},


		elem	: {},
		content	: {},
		toolbar : {},
		form	: {},
		source	: {},
		editor	: {},
		builder : null,

		/**
		 * Create
		 * @param {jQuery} $block
		 */
		create : function( $block ) {

			this.block 	= $block;
			this.type 	= this.block.data('type');
			this.id 	= this.block.attr('id');
			this.config = this.block.data('config');


			$elem = $('<div class="cb-redactor cb-clearfix"></div>');
			this.block.append( $elem );
			this.elem = $elem;

			this.toolbar = $('<div id="'+this.id+'-Toolbar" class="toolbar"></div>');
			$toolbarWrap = $('<div class="toolbar-wrap"></div>');
			$toolbarWrap.append( this.toolbar );
			this.elem.append( $toolbarWrap );
			this.toolbar.hide();

			this.content = $('<div id="'+this.id+'-Content" class="content"><p>Content</p></div>');
			this.elem.append( this.content );
			this.elem.append('<div class="cb-clear"></div>');
			this.content.show();
			this.content.click( $.proxy( this.open, this ) );

			return( this );
		},

		/**
		 * Initialize
		 */
		init : function() {

			this.builder = this.block.data('builder');

		},

		setData : function( data ) {
			this.data = data;
			this.content.attr('class','content');
			if( this.data['content'] !== undefined ) {
				this.content.html( this.data['content'] );
			}
			if( this.data['class'] !== undefined ) {
				this.content.addClass( this.data['class'] );
			}
		},

		getData : function(){
			var data = this.data;
			return( data );

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
			}

			//assign form data
			ContentBuilder.FormData( this.form, this.data );

			//create editor
			if( $.browser.msie ) {
				var html = this.content.html();
				this.content.empty();
			}

			this.content.redactor({
				air: false,
				callback : function( redactor ) {
					var toolbar = redactor.$editor.attr('id').replace('-Content', '-Toolbar');
					$('#'+toolbar).append( redactor.$toolbar).show();
					if( $.browser.msie ) {
						redactor.setCode(html);
					}
				},
				buttons: ['html', '|', 'formatting', '|', 'bold', 'italic', 'deleted', '|',
							'unorderedlist', 'orderedlist', '|',
							'customImage', 'table', 'customLink', '|',
							'fontcolor', 'backcolor', '|',
							'alignleft', 'aligncenter', 'alignright', 'justify'],
				buttonsCustom: {
					customImage: {
						title: 'Insert Image...',
						callback: ContentBlockRedactor.onImageInsertCall
					},
					customLink: {
						title: 'Insert link',
						func: 'show',
						dropdown: {
							link: {
								title: 'Insert Link...',
								func: 'showLinkCustom'
							},
							unlink: {
								title: 'Unlink',
								exec: 'unlink'
							}
						}
					}
				},
				cleanup: true
			});

			if ( ! Redactor.prototype.hasOwnProperty('showLinkCustom') ) {
				Redactor.prototype.showLinkCustom = function(e) {
					//$('.content').eq(0).data('redactor');
					var redactorObj = e.view.$active.data('editor').content.data('redactor');
					Redactor.prototype.showLink.call(redactorObj);
					var $urlField = $('#redactor_link_url');
					$urlField.autocomplete({
						source: ContentBuilder.ajaxUrl + '?action=pagelist'
					});
					$urlField.before('<span style="font-style: italic;">Enter post title to autocomplete</span>');
				};
			}

			//set form position
			$f = $('div.cb-form', this.form );
			var left = this.elem.offset().left - this.builder.offset().left;
			$f.css('left', (left*-1)+'px');
			$f.width( this.builder.width() );
			this.toolbar.css('left', (left*-1)+'px').css('width', this.block.width()+'px' );
			this.toolbar.width( this.builder.width() );
			this.form.show();

			this.block.trigger('cb-editing',[true]);

		},

		onImageInsertCall : function( obj, event, key ) {
			Redactor.prototype.saveSelection.call(obj);
			ContentBuilder.ModalWindow('image-library', function(type, data, action){
				if( typeof data.source === 'string' ) {
					var image = '<img class="'+ data.extra.className +'" alt="'+ data.alt +'" src="' + data.source + '" />';
					Redactor.prototype._imageSet.call(obj, image, true);
				}
			},'rte-insert');
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
					value.content = this.content.getCode();
					value['class'] = formData['class'];
					this.setData( value );
					this.close();
					//this.update();
					this.block.trigger('cb-updated');
				break;
				case 'cancel':
					this.close();
					this.setData( this.data );
					//this.update();
				break;
			}

		},

		close : function() {

			this.content.destroyEditor();
			this.form.hide();
			this.toolbar.empty().hide();
			//this.content.attr('contenteditable', 'false').blur();
			this.block.trigger('cb-editing',[false]);

		}

	};

})(jQuery);