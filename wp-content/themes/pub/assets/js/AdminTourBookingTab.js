/**
 * Namespace for the tour booking periods management widget processing.
 *
 * @type Object
 */
var AdminTourBookingTab = {
	init:function(){
		var self = this;

		this._errorsRenderer = new ThemeTools.ErrorsRendererSet({
			containerSelector:'#tour_booking_rows_cont',
			itemSelector:'.tour-booking-row',
			rowRendererConfig:{
				errorWraper:{
					className: 'field-error-msg'
				},
				_getErrorWraper:function( fieldKey, onlyIfExists ){
					var field = this._getField( fieldKey ),
						errorWrapperCont = field.parent();

					if ( 'days' == fieldKey) {
						errorWrapperCont = field.parents('.tour-booking-row__days');
					}

					var errorWraperEl = errorWrapperCont.find( '.' + this.errorWraper.className );
					if ( errorWraperEl.length < 1 && !onlyIfExists ) {
						var errorWraperTypeOutput = this.errorWraper.typeOutput,
							errorWraperTemplateHtml = '<' + this.errorWraper.tag + ' class="' + this.errorWraper.className + '"></' + this.errorWraper.tag + '>';

						errorWraperEl = jQuery( errorWraperTemplateHtml ).appendTo( errorWrapperCont );
					}

					return errorWraperEl;
				}
			}
		});

		this.getAddButton().click(function(event){
			event.preventDefault();
			self.addRow();
		});

		this.getSaveButton().click(function(event){
			event.preventDefault();
			self.saveRows();
		});

		this.getPreviewCalendarButton().click(function(event){
			event.preventDefault();
			this.is_on = !this.is_on;

			var btn = jQuery(this),
				alt_text = btn.data('togletext');
			if (alt_text) {
				var origin_text = btn.data('origintext');
				if ( ! origin_text && this.is_on ) {
					origin_text = btn.text();
					btn.data('origintext', origin_text);
				}
				if (origin_text) {
					btn.text(this.is_on ? alt_text : origin_text);
				}
			}

			self.previewCalendar(this.is_on);
		});

		this.getRows().each(function(){
			self._initRow(jQuery(this));
		});

		this.getCont().on('change', 'input,select,textarea', function(){
			jQuery(self).trigger('change');
		});
	},

	/**
	 * Adds new row into management form.
	 */
	addRow:function(){
		var btn = this.getAddButton();

		var newRow = jQuery(btn.data('row'));
		newRow.appendTo(this.getRowsCont());
		this._initRow(newRow);
		this._reworkFieldIndexes();
	},

	saveRows:function(){
		var self = this,
			render_errors = function(response) {
				if ( response && response.errors ) {
					self._errorsRenderer.render( response.errors );
				} else {
					alert( 'Unknown error. Please contact support.' );
				}
			};

		jQuery.ajax({
			url:ajaxurl + '?action=save_tour_booking_periods',
			method:'POST',
			data:this._getPeriodsQueryString(),
			dataType:'json',
			success:function(response){
				if (response && response.success) {
					// changes have been saved
					self._errorsRenderer.render();
				} else {
					render_errors(response);
				}
			},
			error:function(response){
				render_errors(response);
			}
		});
	},

	_getPeriodsQueryString:function(){
		var inputs = this.getCont().find('input,select,textarea'), //inputs = this.getRows().find('input,select,textarea'),
			dataRow = inputs.serialize();
		return dataRow;
	},

	/**
	 * Show/hide preview calendar.
	 *
	 * @param  boolean show
	 * @return void
	 */
	previewCalendar:function(show){
		if ( ! show ) {
			if ( this._dp) {
				this._dp.hide();
			}
			return;
		}

		if ( ! this._dp) {
			this._loadAvailableTickets();
			var self = this;
			this._dp = jQuery('<div class="tour-booking-preview-calendar"></div>').datepicker({
				// firstDay: 1, //to start from Mon
				beforeShowDay: function(date){
					var days = self._previewGetAvailableTickets(date);
					return [days > 0, '', days > 0 ? 'Left ' + days + ' ticket(s)' : ''];
				}
			}).insertBefore(this.getPreviewCalendarButton());

			jQuery(this).on('change', function(){
				self._redrawPreviewCalendar();
			});
		} else {
			this._loadAvailableTickets();
			this._dp.datepicker('refresh');
			if (!this._dp.is(':visible')) {
				this._dp.show();
			}
		}
	},

	/**
	 * Removes particular row.
	 *
	 * @param  jQuery row
	 * @return void
	 */
	removeRow:function(row){
		//var row = this.getRows().filter(jQuery(btn).parents('tr'));
		if (row.length) {
			row.remove();
			this._reworkFieldIndexes();
		}
	},

	/**
	 * Returns button that should be used for new row creation.
	 *
	 * @return jQuery
	 */
	getAddButton:function(){
		return this.getCont().find('.add_row_btn');
	},

	/**
	 * Returns button that should be used saving information about tour booking periods.
	 *
	 * @return jQuery
	 */
	getSaveButton:function(){
		return this.getCont().find('.save_ranges_btn');
	},

	/**
	 * Returns button that should be used to show/hide calendar with available tickets information.
	 *
	 * @return jQuery
	 */
	getPreviewCalendarButton:function(){
		return this.getCont().find('.preview_btn');
	},

	/**
	 * Returns set of the row each of that contains fields for single period details management.
	 *
	 * @return jQuery
	 */
	getRows:function(){
		return this.getRowsCont().find('tr');
	},

	/**
	 * Returns rows container.
	 *
	 * @return jQuery
	 */
	getRowsCont:function(){
		return this.getCont().find('#tour_booking_rows_cont');
	},

	/**
	 * Returns global widget container.
	 *
	 * @return jQuery
	 */
	getCont:function(){
		return jQuery('#tour_booking_tab');
	},

	/**
	 * Inits all handlers related on the row.
	 *
	 * @param  jQuery row
	 * @return jQuery
	 */
	_initRow:function(row){
		var self = this;
		row.find('[data-role=remove-row]').click(function(event){
			event.preventDefault();
			if ( confirm('Are you sure want to remove this item?') ) {
				self.removeRow(row);
			}
		});
		var datepickers = row.find('.dateselector');

		if ( datepickers.length ) {
			var start = datepickers.filter('[name$="[from]"]'),
				end = datepickers.filter('[name$="[to]"]');

			start.datepicker({
				beforeShow:function(el, ev){
					var end_date = end.val();
					jQuery(el).datepicker('option', 'maxDate',end_date ? new Date(end_date) : null);
				}
			});

			end.datepicker({
				beforeShow:function(el, ev){
					var start_date = start.val();
					jQuery(el).datepicker('option', 'minDate', start_date ? new Date(start_date) : null);
				}
			});
		}

		return row;
	},

	/**
	 * Updates row related field names with the right row index.
	 * Should be called after any changes in row set (add/remove/reorder).
	 *
	 * @return void
	 */
	_reworkFieldIndexes:function(){
		var self = this;
		this.getRows().each(function(index, el){
			self._setFieldIndexTo(jQuery(this), index);
		});
	},

	/**
	 * Set row related inputs index to some particular value.
	 *
	 * @param jQuery  row
	 * @param integer newIndex
	 */
	_setFieldIndexTo:function(row, newIndex){
		row.find('[name^="tour-booking-row["]').each(function(){
			var input = jQuery(this),
				newName = input.attr('name').replace(/\[\d+\]/, '['+newIndex+']');
			input.attr('name', newName);
		});
	},

	/**
	 * Refresh preview calendar.
	 *
	 * @return void
	 */
	_redrawPreviewCalendar:function(){
		if (this._dp && this._dp.is(':visible')) {
			this._loadAvailableTickets();
			this._dp.datepicker('refresh');
		}
	},

	/**
	 * Returns number of tickets available for specific date.
	 *
	 * @param  string date
	 * @return int
	 */
	_previewGetAvailableTickets:function(date){
		if ( ! this._previewAvailableDates ) {
			return 0;
		}
		var formattedDate = jQuery.datepicker.formatDate('yy-mm-dd', date);
		return parseInt( this._previewAvailableDates[formattedDate], 10 );
	},

	/**
	 * Loads information about available dates for preview calendar.
	 *
	 * @return void
	 */
	_loadAvailableTickets:function(){
		jQuery.ajax({
			async:false,
			url:ajaxurl + '?action=preview_booking_periods',
			method:'POST',
			data:this._getPeriodsQueryString(),
			dataType:'json',
			success:function(response){
				if (response && response.success && response.data) {
					this._previewAvailableDates = response.data;
				} else {
					this._previewAvailableDates = null;
				}
			},
			error:function(response){
				this._previewAvailableDates = null;
			},
			context:this
		});
	}
};

jQuery(function(){
	AdminTourBookingTab.init();
});
