var Theme = {
	init:function($){
		this._initMobileMenu();
		this._initScrollTop($);
		this.initSelectpicker();
		this.initBootstrapTabcollapse();

		$('[data-toggle="tooltip"]').tooltip();

		this.FormValidationHelper.init();
	},

	initGoogleMap: function(cfg){
		if ( 'undefined' == typeof(cfg) ) {
			return;
		}

		var mapElement = document.getElementById(cfg.element_id);

		if ( ! mapElement ){
			return;
		}

		var jMap = jQuery(mapElement);
		jMap.height(cfg.height);

		if (cfg.full_width) {
			var on_resize_hander = function(){
				jMap.width(jQuery(window).outerWidth())
					.offset({left:0});
				if (map) {
					//google.maps.event.trigger(map, 'resize');
					if (mapLang) {
						map.setCenter(mapLang);
					}
				}
			};
			on_resize_hander();
			jQuery(window).on('resize', on_resize_hander);
		}

		var mapLang = new google.maps.LatLng(parseFloat(cfg.coordinates[0]), parseFloat(cfg.coordinates[1])),
			map = new google.maps.Map(mapElement,{
				scaleControl: true,
				center: mapLang,
				zoom: cfg.zoom,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				scrollwheel: false
			}),
			marker = new google.maps.Marker({
				map: map,
				position: map.getCenter()
			});

		if (cfg.address) {
			var infowindow = new google.maps.InfoWindow();
			infowindow.setContent(cfg.address);
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map, marker);
			});
		}

		// fix display map in bootstrap tabs and accordion
		if ( cfg.is_reset_map_fix_for_bootstrap_tabs_accrodion ) {
			jQuery(document).on('shown.bs.collapse shown.bs.tab', '.panel-collapse, a[data-toggle="tab"]', function () {
				google.maps.event.trigger(map, 'resize');
				map.setCenter(mapLang);
			});
		}
	},

	/**
	 * Initialization modile menu.
	 * @use jquery.slicknav.js, slicknav.css
	 * @return void
	 */
	_initMobileMenu:function(){
		var mainBtn,
			closeClass = 'slicknav_btn--close',
			itemClass = 'slicknav_item',
			itemOpenClass= 'slicknav_item--open';

		jQuery('#navigation').slicknav({
			label:'',
			prependTo:'.header__content',
			openedSymbol: '',
			closedSymbol: '',
			beforeOpen : function(target){
				if( target.length ){
					if( target[0] == mainBtn ){
						target.addClass(closeClass);
					}else if( target.hasClass(itemClass) ){
						target.addClass(itemOpenClass);
					}
				}
			},
			beforeClose : function(target){
				if( target.length ){
					if( target[0] == mainBtn ){
						target.removeClass(closeClass);
					}else if( target.hasClass(itemClass) ){
						target.removeClass(itemOpenClass);
					}
				}
			},
		});

		mainBtn = jQuery('.slicknav_btn');
		mainBtn = mainBtn.length ? mainBtn[0] : null;
	},

	/**
	 * Initialization custom select box.
	 * @use bootstrap.min.js, bootstrap-select.min.js, bootstrap-select.min.css
	 * @return void
	 */
	initSelectpicker : function(){
		jQuery('select.selectpicker')
			.add('.widget select')
			.add('select.orderby') // woocommerce, shop page > orderby selector
			.selectpicker();
	},

	/**
	 * Initilizes responsive  for bootstrap tabs via transformation them into accordion for small devices.
	 *
	 * @return void
	 */
	initBootstrapTabcollapse : function(){
		var element = jQuery('.tours-tabs .nav');
		if(0 == element.length){
			return;
		}

		// class tabs-accordion need for customize accordion
		element.tabCollapse({
			tabsClass: 'hidden-xs',
			accordionClass: 'visible-xs tabs-accordion'
		});
	},

	/**
	 * Created swiper sliders.
	 *
	 * @param numSlides config
	 */
	makeSwiper: function( config ){
		var cfg = jQuery.extend( {
			containerSelector:'',
			slidesNumber:4,
			navPrevSelector:'',
			navNextSelector:'',
			sliderElementSelector:'.swiper-slider',
			slideSelector: '.swiper-slide',
			widthToSlidesNumber:function(windowWidth, slidesPerView) {
				var result = slidesPerView;
				if (windowWidth > 992) {

				} else if(windowWidth > 768) {
					//result = Math.max(3, Math.ceil(slidesPerView / 2));
					result = Math.ceil(slidesPerView / 2);
				} else if (windowWidth > 670) {
					result = 2;
				} else {
					result = 1;
				}

				return result;
			}
		}, config || {} );
		if( !cfg.containerSelector ){
			return null;
		}

		var numSlides = cfg.slidesNumber,
			container = jQuery(cfg.containerSelector),
			sliderElement = container.find( cfg.sliderElementSelector ),
			realSlidesNumber = sliderElement.find( cfg.slideSelector ).length,
			swiper = new Swiper ( sliderElement, {
				slidesPerView: numSlides,
				spaceBetween: 30,
				loop: numSlides < realSlidesNumber
				//,loopedSlides: 0
			});

		var navButtons = null,
			naviPrev = null,
			naviNext = null;
		if(cfg.navPrevSelector){
			naviPrev = container.find(cfg.navPrevSelector);
			if ( naviPrev.length ) {
				naviPrev.on('click', function(e){
					e.preventDefault();
					swiper.slidePrev();
				});
				navButtons = jQuery(naviPrev);
			}
		}
		if(cfg.navNextSelector){
			naviNext = container.find(cfg.navNextSelector);
			if (naviNext.length) {
				naviNext.on('click', function(e){
					e.preventDefault();
					swiper.slideNext();
				});
				navButtons = navButtons ? navButtons.add(naviNext) : jQuery(naviNext);
			}
		}

		var isFirstCall = true,
			_resizeHandler = function(){
				var slidesPerView = numSlides;

				if ( cfg.widthToSlidesNumber && 'function' == typeof cfg.widthToSlidesNumber ) {
					slidesPerView = cfg.widthToSlidesNumber(jQuery(window).width(), numSlides);
				}

				var isNewValue = swiper.params.slidesPerView != slidesPerView;

				if ( isFirstCall || isNewValue ) {
					if (isNewValue) {
						swiper.params.slidesPerView = slidesPerView;
						swiper.update();
					}

					if ( navButtons ) {
						if (slidesPerView < realSlidesNumber && realSlidesNumber > 1) {
							navButtons.show();
						} else {
							navButtons.hide();
						}
					}
					if (isFirstCall) {
						isFirstCall = false;
					}
				}
			};
		jQuery(window).on('resize', _resizeHandler);//.trigger('resize');
		_resizeHandler();
	},

	initParallax : function(selector){
		if ( !selector ) {
			selector = '.parallax-image';
		}

		jQuery(selector).each(function(){
			var element = jQuery(this),
				speed = element.data('parallax-speed');
			element.parallax("50%", speed ? speed : 0.4);
		});
	},

	// Page FAQ bootstrap accordion changes icon
	// @use bootstrap.min.js
	faqAccordionCahgesIcon : function(){
		var accordion = jQuery('.faq__accordion'),
			panels = '.faq__accordion__item',
			panelsClassOpen = 'faq__accordion__item--open',
			icon = '.faq__accordion__heading i',
			iconClassUp = 'fa-info',
			iconClassDown = 'fa-question';

		accordion.each(function(){
			var el = jQuery(this);

			el.find(panels).find(icon).addClass(iconClassDown);

			el.find(panels).on({
				'show.bs.collapse':function () {
					jQuery(this)
						.addClass(panelsClassOpen)
						.find(icon)
							.removeClass(iconClassDown)
							.addClass(iconClassUp);
				},
				'hide.bs.collapse':function () {
					jQuery(this)
						.removeClass(panelsClassOpen)
						.find(icon)
							.removeClass(iconClassUp)
							.addClass(iconClassDown);
				}
			});
		});
	},

	_initScrollTop: function($){
		var document = $('body, html'),
			link = $('.footer__arrow-top'),
			windowHeight = $(window).outerHeight(),
			documentHeight = $(document).outerHeight();

		if(windowHeight >= documentHeight){
			link.hide();
		}

		link.on('click', function(e){
			e.preventDefault();

			document.animate({
				scrollTop : 0
			}, 800);
		});
	},

	init_faq_question_form: function(formSelector){
		var form = jQuery(formSelector),
			form_content = jQuery('.form-block__content'),
			form_el_msg_success = jQuery('.form-block__validation-success');

		if (form.length < 1) {
			return;
		}

		var notice_wrapper = form.find('.form-block__validation-error'),
			resetFormErrors = function() {
				form.find('.field-error-msg').remove();
				notice_wrapper.html('');
			};

		Theme.FormValidationHelper.init();

		form.on('submit', function(e){
			//e.preventDefault();
			var dataArray = form.serializeArray(),
				formData = {};

			jQuery.each(dataArray, function(i, item){
				formData[item.name] = item.value
			});

			jQuery.ajax({
				url: form.attr('action'),
				data: formData,
				method:'POST',
				error:function(responseXHR){
					resetFormErrors();
					Theme.FormValidationHelper.formReset(formSelector);
					var res = responseXHR.responseJSON ? responseXHR.responseJSON : {};
					if (res.field_errors) {
						jQuery.each(res.field_errors, function(fieldKey, message){
							Theme.FormValidationHelper.itemMakeInvalid(form.find('[name*="['+ fieldKey + ']"]'), message);
						});
					}

					if (res.error) {
						notice_wrapper.html('<i class="fa fa-exclamation-triangle"></i>' + res.error);
					}
				},
				success:function(res){
					resetFormErrors();
					Theme.FormValidationHelper.formReset(formSelector);
					if(res.message){
						form_content.fadeOut(400, function(){
							form_el_msg_success.html(res.message);
						});
					}
					if (res.success) {
						form[0].reset();
					}
				},
			})

			return false;
		});
	},

	/**
	 * Initilize sharrre buttions.
	 * @param  object config
	 * @return void
	 */
	initSharrres: function(config){
		if (!config || typeof config != 'object' || !config.itemsSelector) {
			//throw 'Parameters error.';
			return;
		}

		var curlUrl = config.urlCurl ? config.urlCurl : '',
			elements = jQuery(config.itemsSelector);

		if (elements.length < 1) {
			return;
		}

		var initSharreBtn = function(){
			var el = jQuery(this),
				url = el.parent().data('urlshare'),
				curId = el.data('btntype'),
				curConf = {
					urlCurl: curlUrl,
					enableHover: false,
					enableTracking: true,
					url: ('' != url) ? url : document.location.href,
					share: {},
					click: function(api, options){
						api.simulateClick();
						api.openPopup(curId);
					}
				};

			curConf.share[curId] = true;
			el.sharrre(curConf);
		};
		elements.each(initSharreBtn);

		// to prevent jumping to the top of page on click event
		setTimeout(function(){
			jQuery('a.share,a.count', config.itemsSelector).attr('href','javascript:void(0)');
		},1500);
	},

	/**
	 * Initilize Search Form in popup.
	 * @use jquery.magnific-popup.min.js magnific-popup.css
	 * @return void
	 */
	initSerchFormPopup: function( config ){
		var classHide = 'search-form-popup--hide',
			cfg = jQuery.extend({
				placeholder_text: 'Type in your request...'
			}, config || {});

		jQuery('.popup-search-form').magnificPopup({
			type: 'inline',
			preloader: false,
			focus: '#s',
			//closeMarkup: '<button title="%title%" type="button" class="mfp-close"><i class="fa fa-times"></i></button>',
			showCloseBtn: false,
			removalDelay: 500, //delay removal by X to allow out-animation
			fixedContentPos: false,
			callbacks: {
				beforeOpen: function(){
					this.st.mainClass = this.st.el.attr('data-effect');
				},
				open: function() {
					this.content.removeClass(classHide);
					jQuery('.mfp-close').on('click', function(){
						jQuery.magnificPopup.close();
					});
				},
				close : function(){
					this.content.addClass(classHide);
				},
			},
			midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
		});

		if ( cfg.placeholder_text ) {
			jQuery('.search-form-popup')
				.find('input[type="text"]')
				.attr('placeholder', cfg.placeholder_text);
		}
	}
}

/**
 * Gallery plugin.
 * Enables filtering and pagination functionalities.
 *
 * @param {jQuery|selector} container
 * @param {Oject}           config
 */
Theme.Gallery = function(container, config){
	if (config) {
		jQuery.extend(this, config);
	}

	this.cnt = jQuery(container);

	this._init();
};

Theme.Gallery.prototype = {

	paginationSl : '.pagination',
	imagesContainerSl:'.gallery__items',
	filterButtonsSl : '.gallery__navigation a',
	filterButtonActionClass : 'gallery__navigation__item-current',
	aminationClass : 'animated',
	_jPager:null,

	/**
	 * Settings for jPages plugin
	 *
	 * @see initPagination
	 * @type {Object}
	 */
	paginationConfig:{
		// container: '#galleryContatiner1 .gallery__items',
		perPage : 9,
		animation:'fadeIn',
		previous: '',
		next: '',
		minHeight: false
	},

	getPagerEl:function(){
		return this.paginationSl ? this.cnt.find(this.paginationSl) : null;
	},

	getImagesContEl:function(){
		return this.cnt.find(this.imagesContainerSl);
	},

	/**
	 * Initilize gallery.
	 * @use jquery.swipebox.js, swipebox.css, jPages.js
	 *
	 * @return void
	 */
	_init : function(contSelector){
		if(this.cnt.length < 1){
			// throw 'configuration error';
			return;
		}

		this.cnt.find('.swipebox').swipebox({
			useSVG : true,
			hideBarsDelay : 0
		});

		this._initPagination();
		this._initFilter();
	},

	/**
	 * Initilize gallery pagination.
	 *
	 * @use jPages.js
	 * @return void
	 */
	_initPagination:function(){
		var paginationEl = this.getPagerEl();

		if( ! paginationEl || paginationEl.length < 1 ){
			return;
		}

		if(this._jPager){
			this._jPager.jPages('destroy');
		}

		this._jPager = paginationEl.jPages(
			jQuery.extend({
					container : this.getImagesContEl()
				},
				this.paginationConfig
			)
		);
	},

	/**
	 * Initilize gallery filter.
	 * @param container selector, wrap gallery
	 * @param filterButtons selector
	 * @return void
	 */
	_initFilter:function(container, filterButtons){
		var filterButtonsEl = this.filterButtonsSl ? this.cnt.find(this.filterButtonsSl) : null;
		if ( !filterButtonsEl && !filterButtonsEl.length ) {
			return;
		}

		var self = this,
			items = this.getImagesContEl().children();

		/**
		 * Items animation use jPages animation, when pagination off.
		 */
		var _itemsAnimation = function(){
			if( self._jPager ){
				return;
			}

			var customAnimationClass = self.paginationConfig.animation;
			if(!customAnimationClass){
				return;
			}

			var animationClasses = self.aminationClass + ' ' + customAnimationClass;
			items.addClass(animationClasses);
			setTimeout( function(){
				items.removeClass(animationClasses);
			}, 600 );
		};

		_itemsAnimation();

		filterButtonsEl.on('click', function(e){
			e.preventDefault();
			var idFilter = jQuery(this).data('filterid'),
				btnActiveClass = self.filterButtonActionClass;

			filterButtonsEl.parent()
				.removeClass(btnActiveClass);

			jQuery(this).parent()
				.addClass(btnActiveClass);

			if(!idFilter){
				idFilter = 'all';
			}

			var filtered = idFilter == 'all' ? items : items.filter('[data-filterid*="'+idFilter+'"]'),
				needShow = filtered,// filtered.filter(':not(:visible)'),
				needHide = items.not(filtered);//.filter(':visible');

			if ( !needShow.length && !needHide.length ) {
				return; // nothing to do
			}

			_itemsAnimation();

			needHide.hide();
			needShow.show();

			if ( self._jPager ) {
				self._initPagination();
			}
		});
	}
};

/**
 * Form validation helper.
 * @use bootstrap.min.js, bootstrap-custom.css
 */
Theme.FormValidationHelper = {
	options: {
		itemsValidationClass : 'form-validation-item',
		titleFillfiled : 'Fill in the required field.',
		titleEmailInvalid : 'Email invalid.',
		emailValidationRegex : /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
	},

	init: function(){
		this.initTooltip(
			jQuery( '.' + this.options.itemsValidationClass )
		);
		this.initContactForm7CustomValidtion();
	},

	/**
	 * Initialization tooltips.
	 * @param selector|jQuery items
	 * @return void
	 */
	initTooltip: function(items){
		if ( typeof items == 'string') {
			items = jQuery(items);
		}
		if( items.length < 1 ){
			return;
		}

		items
			.tooltip({
				trigger : 'manual',
				animation : false,
				delay : 0
			})
			.on('focus', function(){
				jQuery(this).tooltip('hide');
			});
		return items;
	},

	/**
	 * Form items hide tooltip.
	 * @param selector wrap
	 * @return void
	 */
	formReset: function(wrap){
		var wrap = jQuery(wrap);

		if(0 == wrap.length){
			return null;
		}

		wrap.find('.' + this.options.itemsValidationClass)
			.tooltip('hide')
			.attr('data-original-title', '')
			.attr('title', '');
	},

	/**
	 * Item show tooltip with error.
	 * @parm jQuery object item
	 * @parm  string title
	 * @return void
	 */
	itemMakeInvalid: function(item, title){
		item
			.attr('data-original-title', title)
			.tooltip('show');
	},

	/**
	 * Validation items.
	 * @param object items
	 * @return integer errors count
	 */
	itemsValidation: function(items){
		var self = this,
			errorsCount = 0;

		jQuery.each(items, function(i, item){
			var item = jQuery(item),
				itemVal = item.val(),
				itemName = item.attr('name'),
				itemType = item.attr('type');

			if( ! itemVal.trim() ) {
				errorsCount++;
				self.itemMakeInvalid(item, self.options.titleFillfiled);
			} else if('email' == itemType || 'email' == itemName || item.hasClass('yks-mc-input-email-address')){
				if( ! self.options.emailValidationRegex.test( itemVal ) ) {
					errorsCount++;
					self.itemMakeInvalid( item, self.options.titleEmailInvalid );
				}
			}
		});

		return errorsCount;
	},

	/**
	 * Initialization custom validation for plugin contact form 7.
	 * @return void
	 */
	initContactForm7CustomValidtion: function(){
		var self = this,
			wrapForm = jQuery('.wpcf7'),
			itemsValidationClass = this.options.itemsValidationClass;

		wrapForm.each(function(){
			var wrapFromId = jQuery(this).attr('id'),
			wrapFormEl = jQuery('#' + wrapFromId);

			if(wrapFormEl.length < 1){
				return;
			}

			var items = wrapFormEl
				.find('.wpcf7-validates-as-required')
				.addClass(itemsValidationClass);

			self.initTooltip( items );

			wrapFormEl.find('form').on('ajaxComplete', function(e){
				jQuery(this).find('.wpcf7-not-valid').each(function(i, item){
					var item = jQuery(item),
						itemErrorText = item.siblings('.wpcf7-not-valid-tip').text();

					switch(itemErrorText){
					case 'Please fill in the required field.':
						itemErrorText = self.options.titleFillfiled;
						break;
					case 'Email address seems invalid.':
						itemErrorText = self.options.titleEmailInvalid;
						break;
					}

					self.itemMakeInvalid(item, itemErrorText);
				});
			});
		});
	},

	/**
	 * Initialization custom validation for plugin Easy MailChimp Forms.
	 *
	 * @param selector wrapFormId
	 * @return void
	 */
	initMailChimpCustomValidtion: function(wrapFormId){
		var self = this,
			itemsValidationClass = this.options.itemsValidationClass,
			wrapForm = jQuery('#' + wrapFormId);

		if(wrapForm.length < 1){
			return;
		}

		var items = wrapForm.find('.yks-require')
			.addClass(itemsValidationClass);

		this.initTooltip( items );

		wrapForm.find('form')
			.find('[type="submit"], [type="image"]')
			.on('click', function(e){
				if( self.itemsValidation( items ) > 0 ){
					e.preventDefault();
				}
			});
	},

	/**
	 * Initialization custom validation for forms.
	 *
	 * @param  selector wrapFormId
	 * @return void
	 */
	initValidationForm: function(wrapFormId){
		var self = this,
			itemsValidationClass = this.options.itemsValidationClass,
			wrapForm = jQuery('#' + wrapFormId);

		if(0 == wrapForm.length){
			return;
		}

		this.initTooltip(
			wrapForm.find('.' + this.options.itemsValidationClass)
		);

		wrapForm.find('form').on('submit', function(e){
//			e.preventDefault();
			self.formReset(wrapForm);

			var items = wrapForm.find('.' + itemsValidationClass),
				formErrors = 0;

			formErrors = self.itemsValidation(items);

			// validation success
			if(0 == formErrors){
//TODO complete
			}
		});
	}
};

/**
 * Namespace for processing tour booking form.
 *
 * @type Object
 */
Theme.tourBookingForm = {
	/**
	 * Tour booking form selector.
	 *
	 * @type String
	 */
	formSelector:'#tourBookingForm',

	dateFormat: 'yy-mm-dd',

	/**
	 * Contains set of dates available for tour booking.
	 * Date used as a key, value - is count of booking available for that date.
	 *
	 * @type Object
	 */
	availableDates:{},

	/**
	 * Inits function.
	 *
	 * @param  Object config
	 * @return void
	 */
	init:function( config ){
		if ( config ) {
			jQuery.extend( this, config );
		}
		this._initDateSelector();
	},

	/**
	 * Returns numner of available tickers for particular date.
	 *
	 * @param  String  date
	 * @return Integer
	 */
	getAvailableTickets:function(date){
		if ( ! this.availableDates ) {
			return 0;
		}
		var formattedDate = jQuery.datepicker.formatDate('yy-mm-dd', date);
		return parseInt( this.availableDates[formattedDate], 10 );
	},

	/**
	 * @return jQuery
	 */
	getForm:function(){
		return jQuery(this.formSelector);
	},

	/**
	 * Inits date selector field for the tour booking.
	 *
	 * @return void
	 */
	_initDateSelector:function(){
		if ( ! jQuery.fn.datepicker) {
			return;
		}
		var self = this;
		this.getForm().find('[name$="[date]"]').datepicker({
			dateFormat: this.dateFormat ? this.dateFormat : 'yy-mm-dd',
			beforeShowDay: function(date){
				return [ self.getAvailableTickets(date) > 0 ];
			}
		});
		jQuery('#ui-datepicker-div').hide(); // To fix issue with generated mockup that visible under footer.
	}
}

jQuery(function($){
	Theme.init($);
});