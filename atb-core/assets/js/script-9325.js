jQuery(document).ready(function ($) {
	"use strict";
	toastr.options = {
		'closeButton': true,
		'debug': false,
		'newestOnTop': true,
		'progressBar': true,
		'positionClass': 'toast-top-right',
		'preventDuplicates': true,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "3500",
		"extendedTimeOut": "1000",
		'showEasing': 'swing',
		'hideEasing': 'linear',
		'showMethod': 'fadeIn',
		'hideMethod': 'fadeOut',
	}
	function chauffeur_email_validation(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
	function check_min_wait_time(pick_city) {
		//var city = $('#pcity').val();
		var cityData = {
			action: 'atb_min_wait_times',
			city : pick_city,
		};
		$.post(AJAX_URL, cityData, function(response) {
			var ajaxdata = JSON.parse(response);
			var data1 = ajaxdata.data1;
			var data2 = ajaxdata.data2;
			$('#mwt-1').val(data1);
			$('#mwt-2').val(data2);
		});
	}
	$('#atb-supplier-register').submit(function (e) {
		var pass = $('#password').val();
		var cpass = $('#confirm_password').val();
		if(pass.length < 8 || cpass.length < 8){
			alert('Password must be 8 characters long.');
			return false;
		}else if(pass == cpass){
			return true;
		}else{
			alert('Password does not match.');
			return false;
		}
	});
	function check_booking_time(booking_h, booking_m, booking_date) {

		// Get current date and time and format it
		var d = new Date();
		var curr_date = d.getDate();
		var curr_month = d.getMonth() + 1;
		var curr_year = d.getFullYear();
		var curr_hour = d.getHours();
		var curr_min = d.getMinutes();
		var curr_date_full = curr_year + "/" + curr_month + "/" + curr_date + " " + curr_hour + ":" + curr_min;


		// Detect date format and format current date and time accordingly
		if (chauffeur_datepicker_format == 'yy/mm/dd') {
			var data = booking_date;
			var arr = data.split('/');
			var booking_date_full = arr[0] + "/" + arr[1] + "/" + arr[2] + " " + booking_h + ":" + booking_m;
		}

		if (chauffeur_datepicker_format == 'dd/mm/yy') {
			var data = booking_date;
			var arr = data.split('/');
			var booking_date_full = arr[2] + "/" + arr[1] + "/" + arr[0] + " " + booking_h + ":" + booking_m;
		}

		if (chauffeur_datepicker_format == 'mm/dd/yy') {
			var data = booking_date;
			var arr = data.split('/');
			var booking_date_full = arr[2] + "/" + arr[9] + "/" + arr[1] + " " + booking_h + ":" + booking_m;
		}

		// Convert strings to dates
		var startTime = new Date(curr_date_full);
		var endTime = new Date(booking_date_full);

		// Calculate time difference
		var difference = endTime.getTime() - startTime.getTime();
		var resultInMinutes = Math.round(difference / 60000);
		
		var hours_before_booking_minimum1 = $('#mwt-1').val();

		console.log(resultInMinutes +' -=- '+ hours_before_booking_minimum1);
		// Check if enough time notice given
		if (resultInMinutes <= hours_before_booking_minimum1) {
			// Time notice given, can book
			return true;
		} else {
			// Not enough time notice, cannot book
			return false;
		}

		/*
		var city = $('#pcity').val();
		var cityData = {
			action: 'atb_min_wait_times',
			city : city,
		};
		$.post(AJAX_URL, cityData, function(response) {
			var ajaxdata = JSON.parse(response);
			var data1 = ajaxdata.data1;
			var data2 = ajaxdata.data2;
			console.log(resultInMinutes +' '+ data1 +' '+ data2);
			if (resultInMinutes <= data1) {
				console.log(111);
				return true;
			}
		});
		//return false;
		*/
	}

	// Datepicker
	$(".datepicker").datepicker({
		minDate: 0,
		dateFormat: chauffeur_datepicker_format
	});

	// Set Datepicker value as todays date
	var todaysDate = $.datepicker.formatDate(chauffeur_datepicker_format, new Date());
	$(".datepicker").val(todaysDate);

	// Hide booking form until JS loads
	$(".header-booking-form-wrapper, .body-booking-form-wrapper, .widget-booking-form-wrapper").fadeIn().css("display", "block");

	// Disable datepicker user input
	$('.datepicker').keydown(function (e) {
		e.preventDefault();
		return false;
	});

	// Load tabs
	$("#booking-tabs, #booking-tabs-2").tabs();

						// Add selected vehicle data in hidden fields
	$(".select-vehicle-wrapper").on('click', '.vehicle-section', function () {
		$('.vehicle-section').removeClass("selected-vehicle");
		$(this).toggleClass("selected-vehicle");
		$('.vehicle-section .vehicle-book-now-button').text("Select");
		$(this).find('.vehicle-book-now-button').text("Selected");
		$('.selected-vehicle-price').val($(this).attr('data-price'));
		$('.selected-vehicle-name').val($(this).attr('data-title'));

		$('.pickup-price').val($(this).attr('price-p'));
		$('.return-price').val($(this).attr('price-r'));

		$('.mtgt-price-p').val($(this).attr('mtgt-p'));
		$('.mtgt-price-r').val($(this).attr('mtgt-r'));

		$('#first-journey-greet option[value="true"]').text('Yes (+£'+$(this).attr('mtgt-p')+')');
		$('#return-journey-greet option[value="true"]').text('Yes (+£'+$(this).attr('mtgt-r')+')');

		$('.selected-vehicle-bags').val($(this).attr('data-bags'));
		$('.selected-vehicle-passengers').val($(this).attr('data-passengers'));

	});

	// Remove any content on first page load
	$("#pickup-address1").val("");
	$("#dropoff-address1").val("");
	$("#pickup-address2").val("");
	$("#dropoff-address2").val("");

	$(document).on('click', '#tab1', function (e) {
		chauffeur_active_tab = 'distance';
	});

	$(document).on('click', '#tab2', function (e) {
		chauffeur_active_tab = 'hourly';
	});

	$(document).on('click', '#tab3', function (e) {
		chauffeur_active_tab = 'flat_rate';
	});
	var chauffeur_active_tab = 'distance';
	var pickup_1 = false;
	var dropoff_1 = false;

	var pickup_2 = false;
	var dropoff_2 = false;

	/** Added by Pooja G. */
	var pickupViaList = []
	var returnPickupViaList = []

	var returnAdd;
	var returnVia;
	var returnDropoff;

	/**
	 * Convert Seconds to Hours
	 * @param {int} totalSeconds
	 */
	function secToHR(totalSeconds) {
		var hours = Math.floor(totalSeconds / 3600);
		var minutes = Math.ceil((totalSeconds % 3600) / 60);
		var timeString;
		if (hours == 0) {
			timeString = '';
		} else if (hours == 1) {
			timeString = hours + " hour ";
		} else {
			timeString = hours + " hours ";
		}
		timeString += (minutes > 0) ? minutes + " mins" : '';
		return timeString;
	}

	function computeTotalDistance(result, routeDisplayElement, routeInputElement, routeInputStringElement, timeDisplayElement, timeInputElement) {
		var total = 0;
		var time = 0;
		var myroute = result.routes[0];
		for (var i = 0; i < myroute.legs.length; i++) {
			total += myroute.legs[i].distance.value;
			time += myroute.legs[i].duration.value;
		}

		total = total / 1609.344; //Convert meters to miles
		total = total.toFixed(2); //Round up to 2 decimal places
		routeDisplayElement.innerHTML = total + ' miles';
		routeInputStringElement.value = total + ' miles';
		routeInputElement.value = total;
		timeDisplayElement.innerHTML = secToHR(time);
		timeInputElement.value = secToHR(time);
	}

	/** END - Added by Pooja G. */

	function initialize_autosuggest(form_tab) {

		if (Google_AutoComplete_Country != 'ALL_COUNTRIES') {
			var options = {
				componentRestrictions: {
					country: Google_AutoComplete_Country
				}
			};
		} else {
			var options = '';
		}

		if (form_tab == 'distance') {

			/** Added by PG. */
			//Condition to show return fields when return-journey is set to Return
			$('#return-journey').change(function () {
				if ($(this).val() === 'true') {

					// Show return block
					$('.return-block').css('display', 'block');
					//$('#step2buttons1').css('display', 'none');

					returnAdd = false;
					returnVia = false;

					// Return Address Autocomplete

					var returnAdd_input = document.getElementById('return-address');
					var returnAdd_autocomplete = new google.maps.places.Autocomplete(returnAdd_input, options);

					google.maps.event.addListener(returnAdd_autocomplete, 'place_changed', function () {
						var returnAdd_place = returnAdd_autocomplete.getPlace();
						if (typeof returnAdd_place.adr_address === 'undefined') {
							returnAdd = false;
						} else {
							returnAdd = true;
						}
					});

					// Return Via Autocomplete

					var returnVia_input = document.getElementById('return-via');
					if (returnVia_input) {
						var returnVia_autocomplete = new google.maps.places.Autocomplete(returnVia_input, options);

						google.maps.event.addListener(returnVia_autocomplete, 'place_changed', function () {
							var returnVia_place = returnVia_autocomplete.getPlace();
							if (typeof returnVia_place.adr_address === 'undefined') {
								returnVia = false;
							} else {
								returnVia = true;
							}
						});
					}

					// Return Dropoff Autocomplete

					var returnDropoff_input = document.getElementById('return-dropoff');
					var returnDropoff_autocomplete = new google.maps.places.Autocomplete(returnDropoff_input, options);

					google.maps.event.addListener(returnDropoff_autocomplete, 'place_changed', function () {
						var returnDropoff_place = returnDropoff_autocomplete.getPlace();
						if (typeof returnDropoff_place.adr_address === 'undefined') {
							returnDropoff = false;
						} else {
							returnDropoff = true;
						}
					});

					if (document.getElementById('atbReturnMap') !== null) {

						var map = new google.maps.Map(document.getElementById('atbReturnMap'), {
							mapTypeControl: false,
							streetViewControl: false,
							center: {
								lat: 53.0219186,
								lng: -2.2297829
							},
							zoom: 8
						});

						var return_pickup_address_input = document.getElementById('return-address');
						var return_dropoff_address_input = document.getElementById('return-dropoff');
						var return_pickup_via_input = document.getElementById('return-via');
						var return_route_distance_label = document.getElementById('display-return-route-distance');
						var return_route_distance_string_input = document.getElementById('return-route-distance-string');
						var return_route_distance_input = document.getElementById('return-route-distance');
						var return_route_time_label = document.getElementById('display-return-route-time');
						var return_route_time_input = document.getElementById('return-route-time');

						new AutocompleteDirectionsHandler(map, return_pickup_address_input, return_dropoff_address_input, return_pickup_via_input, return_route_distance_label, return_route_distance_input, return_route_distance_string_input, return_route_time_label, return_route_time_input, true);

					}
				if (!$('.return-time-min1').val()) {
					toastr.error('Please select Pickup Time.');
					return false;
				}else if (!$('.return-time-min1').val()) {
					toastr.error('Please select Pickup Time.');
					return false;
				}
				} else {
					$('.return-block').css('display', 'none');
					$('#step2buttons1').css('display', 'block');		// MediaHeads

					returnAdd = undefined;
					returnVia = undefined;
					returnDropoff = undefined;
				}

			}); // return journey

			/** END - Added by PG. */

			if (document.getElementById('atbMap') !== null) {

				var map = new google.maps.Map(document.getElementById('atbMap'), {
					mapTypeControl: false,
					streetViewControl: false,
					center: {
						lat: 53.0219186,
						lng: -2.2297829
					},
					zoom: 8
				});

				var pickup_address_input = document.getElementById('pickup-address1');
				var dropoff_address_input = document.getElementById('dropoff-address1');
				var pickup_via_input = document.getElementById('pickup-via1');
				var first_route_distance_label = document.getElementById('display-route-distance');
				var first_route_distance_string_input = document.getElementById('route-distance-string');
				var first_route_distance_input = document.getElementById('route-distance');
				var first_route_time_label = document.getElementById('display-route-time');
				var first_route_time_input = document.getElementById('route-time');

				new AutocompleteDirectionsHandler(map, pickup_address_input, dropoff_address_input, pickup_via_input, first_route_distance_label, first_route_distance_input, first_route_distance_string_input, first_route_time_label, first_route_time_input, false);


			} // atbMap

		} // form_tab == distance

		/** Start */
		/**
		 * @constructor
		 */
		function AutocompleteDirectionsHandler(map, originInput, destinationInput, waypointInput, routeDisplayElement, routeInputElement, routeInputStringElement, timeDisplayElement, timeInputElement, returnFlag) {

			var adh = this;

			// Store input params locally.
			this.map = map;

			var originInput = originInput;
			var destinationInput = destinationInput;
			var waypointInputList = waypointInput;

			this.routeDisplayElement = routeDisplayElement;
			this.routeInputElement = routeInputElement;
			this.routeInputStringElement = routeInputStringElement;
			this.timeDisplayElement = timeDisplayElement;
			this.timeInputElement = timeInputElement;

			this.originPlaceId = null;
			this.destinationPlaceId = null;
			this.waypointPlaceIdList = [];
			this.travelMode = 'DRIVING';

			this.directionsService = new google.maps.DirectionsService;
			this.directionsDisplay = new google.maps.DirectionsRenderer;
			this.directionsDisplay.setMap(map);

			var originAutocomplete = new google.maps.places.Autocomplete(originInput, options);
			var destinationAutocomplete = new google.maps.places.Autocomplete(destinationInput, options);

			google.maps.event.addListener(originAutocomplete, 'place_changed', function() {
				var placeOrigin = originAutocomplete.getPlace();
				for (var i = 0; i < placeOrigin.address_components.length; i++) {
				  for (var j = 0; j < placeOrigin.address_components[i].types.length; j++) {
					if (placeOrigin.address_components[i].types[j] == "locality") {
						var pick_city = placeOrigin.address_components[i].long_name;
						break;
					}
					if (placeOrigin.address_components[i].types[j] == "administrative_area_level_1") {
						var pick_state = placeOrigin.address_components[i].short_name;
						break;
					}
				  }
				}
				if(returnFlag){
					if(typeof(pick_city) != "undefined" && pick_city !== null){
						$('#rcity').val(pick_city);
					}else /*if(typeof(pick_state) != "undefined" && pick_state !== null){
						$('#rcity').val(pick_state);
					}else */{
						$('#rcity').val('');
					}
				}else{					
					if(typeof(pick_city) != "undefined" && pick_city !== null){
						$('#pcity').val(pick_city);
						check_min_wait_time(pick_city);
					}else /*if(typeof(pick_state) != "undefined" && pick_state !== null){
						$('#pcity').val(pick_state);
					}else */{
						$('#pcity').val('');
					}
				}
			})

			var returnFlag = returnFlag;
			var addButton = (returnFlag) ? $('#return-add-via') : $('#onward-add-via');
			var viaWrapper = (returnFlag) ? $('#return-via-wrapper') : $('#via-wrapper');

			var waypointsAutocomplete = [];
			if (waypointInputList) {
				waypointInputList.forEach(function (wpInput) {
					waypointsAutocomplete.push(new google.maps.places.Autocomplete(wpInput, options));
				});
			}

			this.getPickupViaFieldName = function () {
				return (returnFlag) ? 'return-pickup-via-' : 'pickup-via-';
			};

			this.getPickupViaFieldInputName = function () {
				return (returnFlag) ? 'return-pickup-via[]' : 'pickup-via[]';
			};

			this.setupPlaceChangedListener = function (autocomplete, mode) {
				var me = this;

				autocomplete.bindTo('bounds', this.map);

				autocomplete.addListener('place_changed', function () {
					var place = autocomplete.getPlace();

					// Get the id of the input field.
					var fieldId = null;

					// fieldId is set only for Waypoints.
					if (mode === 'WAYPT') {
						fieldId = this.get('fieldId');
					}

					function checkPlaceAddress(adr) {
						if (typeof adr === 'undefined') {
							window.alert("Please select an option from the dropdown list.");
							return false;
						} else {
							return true;
						}
					}

					function getFieldIdIx(fieldId) {
						var fieldIx = -1;
						if (typeof fieldId !== 'undefined') {
							var fieldIdParts = fieldId.split('-');
							var fieldIdPartIx = (returnFlag) ? 3 : 2;
							var fieldIxInt = parseInt(fieldIdParts[fieldIdPartIx], 10);
							if (!isNaN(fieldIxInt)) {
								fieldIx = fieldIxInt;
							}
						}
						return fieldIx;
					}

					switch (mode) {
						case 'ORIG':
							if (returnFlag) {
								returnAdd = checkPlaceAddress(place.adr_address);
								if (returnAdd) me.originPlaceId = place.place_id;
								$('#return-pickup-address-lat').val(place.geometry.location.lat());
								$('#return-pickup-address-lng').val(place.geometry.location.lng());
							} else {
								pickup_1 = checkPlaceAddress(place.adr_address);
								if (pickup_1) me.originPlaceId = place.place_id;
								$('#pickup-address-lat').val(place.geometry.location.lat());
								$('#pickup-address-lng').val(place.geometry.location.lng());
							}
							break;

						case 'DEST':
							if (returnFlag) {
								returnDropoff = checkPlaceAddress(place.adr_address);
								if (returnDropoff) me.destinationPlaceId = place.place_id;
								$('#return-dropoff-address-lat').val(place.geometry.location.lat());
								$('#return-dropoff-address-lng').val(place.geometry.location.lng());
							} else {
								dropoff_1 = checkPlaceAddress(place.adr_address);
								if (dropoff_1) me.destinationPlaceId = place.place_id;
								$('#dropoff-address-lat').val(place.geometry.location.lat());
								$('#dropoff-address-lng').val(place.geometry.location.lng());
							}
							break;

						case 'WAYPT':
							var waypoint_1 = checkPlaceAddress(place.adr_address);
							if (waypoint_1) {
								var wpNew = {
									location: place.formatted_address,
									stopover: true
								};

								if (typeof fieldId === 'undefined') {
									// Adding a new waypoint.
									me.waypointPlaceIdList.push(wpNew);
									me.addToPickupViaList(false);
								} else {
									// Update the existing list.
									var wpIx = getFieldIdIx(fieldId);
									me.waypointPlaceIdList[wpIx] = wpNew;
									me.updatePickupViaList(wpIx);
								}
							}

							if (returnFlag) {
								$('#return-pickup-via-lat').val(place.geometry.location.lat());
								$('#return-pickup-via-lng').val(place.geometry.location.lng());
							} else {
								$('#pickup-via-lat').val(place.geometry.location.lat());
								$('#pickup-via-lng').val(place.geometry.location.lng());
							}

							break;
					} // switch

					me.route(false);

				}); // place_changed
			};

			this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
			this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');

			/*
			waypointsAutocomplete.forEach(function (wpAutocomplete) {
				this.setupPlaceChangedListener(wpAutocomplete, 'WAYPT');
			});
      		*/

			var self = this;

			this.addToPickupViaList = function (pickupPoint) {
				if (returnFlag) {
					returnPickupViaList.push(pickupPoint);
				} else {
					pickupViaList.push(pickupPoint);
				}
			};

			this.updatePickupViaList = function (index) {
				if (returnFlag) {
					returnPickupViaList[index] = true;
				} else {
					pickupViaList[index] = true;
				}
			};

			function getPickupViaCount() {
				return viaWrapper.find('.pickup-via-container .pickup-via').length;
			}

			this.initHandlers = function () {
				var me = this;

				addButton.on('click', function () {

					var pickupViaCount = getPickupViaCount();

					var fieldId = me.getPickupViaFieldName() + pickupViaCount;
					var fieldInputName = me.getPickupViaFieldInputName();
					var placeholder = returnFlag ? 'Return Pick Up Via Address' : 'Pick Up Via Address';

					var fieldHTML = '<div class="pickup-via-container"><input type="text" id="' + fieldId +
						'" class="pickup-via" name="'+ fieldInputName +'" required placeholder="' + placeholder + '" waypointIndexCount=' +
						pickupViaCount + ' /><a href="javascript:void(0);" class="remove_button" title="Remove waypoint"><img src="' +
						path_vars.image_dir_path + '/remove-icon.png"/></a></div>';

					viaWrapper.append(fieldHTML);

					document.getElementById('waypointsTotalCount').innerHTML = pickupViaCount;
					var viaElement = document.getElementById(fieldId);
					var viaElementAutocomplete = new google.maps.places.Autocomplete(viaElement, options);
					viaElementAutocomplete.set('fieldId', fieldId);
					self.setupPlaceChangedListener(viaElementAutocomplete, 'WAYPT');

					me.addToPickupViaList(false);

					pickupViaCount++;
					if (pickupViaCount >= 6) {
						addButton.hide();
					}
				});

				function updateWaypointFields(removedIndex) {
					// Find all the .pickup-via-container child elements of [return-]via-wrapper.
					var newIndex = removedIndex;
					viaWrapper.find('.pickup-via-container').each(function (index, elem) {
						// If the input field is before the removed element, do nothing.
						if (index < removedIndex) {} else {
							// Reorder the field attributes to match the array index.

							// Find the input text field.
							var waypointField = $(elem).find('.pickup-via');

							// Set the id for this field using the new index value.
							var fieldId = me.getPickupViaFieldName() + newIndex;
							waypointField.attr('id', fieldId);

							// Update the waypointindexcount attribute.
							waypointField.attr('waypointindexcount', newIndex);

							newIndex++;
						}
					});

					updateWaypointFieldIds();
				}

				function updateWaypointFieldIds() {
					// Find all the .pickup-via-container child elements of [return-]via-wrapper.
					me.waypointsAutocomplete = [];

					if (returnFlag) {
						returnPickupViaList = [];
					} else {
						pickupViaList = [];
					}

					var pickupViaCount = 0;
					viaWrapper.find('.pickup-via-container .pickup-via').each(function (index, elem) {
						// Get the HTML element from the jQuery object.
						var viaElement = $(this)[0];

						// Create the autocomplete object.
						var viaElementAutocomplete = new google.maps.places.Autocomplete(viaElement, options);

						// Get the fieldId using the name and pickup field count.
						var fieldId = me.getPickupViaFieldName() + pickupViaCount;
						// Store the fieldId in the autocomplete object.
						viaElementAutocomplete.set('fieldId', fieldId);

						// Add the autocomplete to the list.
						me.waypointsAutocomplete.push(viaElementAutocomplete);

						// Add the change listener for this autocomplete object.
						self.setupPlaceChangedListener(viaElementAutocomplete, 'WAYPT');
					});
				}

				// Remove waypoint button click handler.
				viaWrapper.on('click', '.remove_button', function (e) {
					e.preventDefault();

					var pickupViaCount = getPickupViaCount();

					var indexToBeRemovedStr = $(this).prev('.pickup-via').attr('waypointIndexCount');
					var indexToBeRemoved = parseInt(indexToBeRemovedStr, 10);

					// Check if there is an autocomplete address at this index.
					// If the field has an input value, update the route.
					var hasInputValue = false;
					var fieldName = me.getPickupViaFieldName();
					var inputField = $('#' + fieldName + indexToBeRemoved);
					if (inputField) {
						var inputValue = inputField.val();
						if (inputValue && inputValue.length > 0) {
							hasInputValue = true;
						}
					}

					// Remove the entry form the list of waypoints.
					me.waypointPlaceIdList.splice(indexToBeRemoved, 1);

					var pickupViaListCount = 0;
					// PickupViaList is filled even if the input value is empty.
					if (returnFlag) {
						returnPickupViaList.splice(indexToBeRemoved, 1);
						pickupViaListCount = returnPickupViaList.length;
					} else {
						pickupViaList.splice(indexToBeRemoved, 1);
						pickupViaListCount = pickupViaList.length;
					}

					// Remove the waypoint input field.
					$(this).parent('div').remove();

					if (pickupViaListCount > 0) {
						// If there are waypoints remaining,
						// update the indices for all waypoints following the deleted waypoint.
						if (indexToBeRemoved <= pickupViaCount) {
							updateWaypointFields(indexToBeRemoved);
						}
					}

					pickupViaCount--;
					if (pickupViaCount <= 5) {
						addButton.show();
					}

					document.getElementById('waypointsTotalCount').innerHTML = pickupViaCount;

					// console.log('pickupViaList: ', pickupViaList);
					// console.log('waypointPlaceIdList: ', me.waypointPlaceIdList);

					// If there is a value in this field, update the routes on the map.
					if (hasInputValue) {
						self.route(false);
					}

					return false;
				});
			};

			this.initHandlers();

		} // AutocompleteDirectionsHandler

		AutocompleteDirectionsHandler.prototype.route = function (checkWaypoints) {

			if (!this.originPlaceId || !this.destinationPlaceId) {
				return;
			}
			var me = this;

			var directions = {
				origin: {
					'placeId': me.originPlaceId
				},
				destination: {
					'placeId': me.destinationPlaceId
				},
				travelMode: me.travelMode
			};

			if (me.waypointPlaceIdList.length > 0) {

				function checkArray(my_arr) {
					for (var i = 0; i < my_arr.length; i++) {
						if (my_arr[i] === undefined)
							return false;
					}
					return true;
				}

				function clearEmpty(my_arr) {
					var new_arr = [];
					for (var i = 0; i < my_arr.length; i++) {
						if (my_arr[i] !== undefined) {
							new_arr.push(my_arr[i]);
						}
					}
					return new_arr;
				}

				if (checkWaypoints) {
					if (checkArray(me.waypointPlaceIdList)) {
						directions.waypoints = me.waypointPlaceIdList;
					} else {
						alert('Can\'t calculate route as one or more waypoints are empty !');
					}
				} else {
					// Remove the empty waypoints
					directions.waypoints = clearEmpty(me.waypointPlaceIdList);
				}
			} // me.waypointPlaceIdList

			me.directionsService.route(directions, function (response, status) {
				if (status === 'OK') {
					me.directionsDisplay.setDirections(response);
					computeTotalDistance(me.directionsDisplay.getDirections(), me.routeDisplayElement, me.routeInputElement, me.routeInputStringElement, me.timeDisplayElement, me.timeInputElement);
				} else {
					window.alert('Directions request failed due to ' + status);
				}
			});

		}; // route
		/** END */

		// if(form_tab == 'hourly') {

		// 	// Pick up address
		// 	var pickup_input2 = document.getElementById('pickup-address2');
		// 	var pickup_autocomplete2 = new google.maps.places.Autocomplete(pickup_input2,options);

		// 	google.maps.event.addListener(pickup_autocomplete2, 'place_changed', function() {
		// 		var pickup_place2 = pickup_autocomplete2.getPlace();
		// 		if (typeof pickup_place2.adr_address==='undefined') {
		// 			pickup_2 = false;
		// 	  	} else {
		// 			pickup_2 = true;
		// 		}
		// 	});

		// 	// Drop off address
		// 	var dropoff_input2 = document.getElementById('dropoff-address2');
		// 	var dropoff_autocomplete2 = new google.maps.places.Autocomplete(dropoff_input2,options);

		// 	google.maps.event.addListener(dropoff_autocomplete2, 'place_changed', function() {
		// 		var dropoff_place2 = dropoff_autocomplete2.getPlace();
		// 		if (typeof dropoff_place2.adr_address==='undefined') {
		// 			dropoff_2 = false;
		// 	  	} else {
		// 			dropoff_2 = true;
		// 		}
		// 	});

		// }

	} // initialize_autosuggest


	if (typeof google != 'undefined') {
		// initialize_autosuggest('distance');
		//initialize_autosuggest('hourly');
		google.maps.event.addDomListener(window, 'load', initialize_autosuggest('distance'));
		// google.maps.event.addDomListener(window, 'load', initialize_autosuggest('hourly'));
	}

	// MediaHeads - Booking step 1 validation
	$(document).on("click", '#bookingstep1next', function (e) {
		var chauffeur_form_submit = new Array();
		
		// Google autocomplete validation
		if ((pickup_1 == false && dropoff_1 == false) || (pickupViaList.length > 0 && $.inArray(false, pickupViaList) !== -1)) {
			// Do not submit			
			toastr.error(chauffeur_autocomplete);
			chauffeur_form_submit.push(false);
		} else {
			if (pickup_1 == dropoff_1) {
				// Submit
				chauffeur_form_submit.push(true);
			} else {
				// Do not submit
				toastr.error(chauffeur_autocomplete);
				chauffeur_form_submit.push(false);
			}
		}
		
		// Minimum notice time validation
		if (check_booking_time($(".time-hour1").val(), $(".time-min1").val(), $(".pickup-date1").val()) == true) {
			//toastr.error(chauffeur_min_time_before_booking_error);
			var mwt2 = $('#mwt-2').val();
			var chauffeur_min_time_before_booking_error1 = 'Sorry we do not accept same day online bookings less than '+ mwt2 +' in advance of the pick up time';
			toastr.error(chauffeur_min_time_before_booking_error1);
			chauffeur_form_submit.push(false);
			return false;
		}
		
		if ($.inArray(false, chauffeur_form_submit) == -1) {
			// OK for Next
			jQuery("#formOneWay-mh1").hide();
			jQuery("#formOneWay-mh2").show();
		}
	});
	$(document).on("click", '#atb-hf-btn12', function (e) {
		$('#formOneWay-mh2').hide();
		$('#formOneWay-mh1').show();
	});

	$(document).on("click", '.bookingbutton2, .bookingbutton1', function (e) {

		var chauffeur_form_submit = new Array();

		// Booking step 1 button
		if ($(".first_booking_step").val() == '1' || $(this).attr("class") == 'bookingbutton2') {
			
			
			// Validate distance tab form if selected
			if (chauffeur_active_tab == 'distance') {

				// Google autocomplete validation
				if ((pickup_1 == false && dropoff_1 == false) || (pickupViaList.length > 0 && $.inArray(false, pickupViaList) !== -1)) {
					toastr.error(chauffeur_autocomplete);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					if (pickup_1 == dropoff_1) {
						chauffeur_form_submit.push(true);
					} else {
						toastr.error(chauffeur_autocomplete);
						chauffeur_form_submit.push(false);
						return false;
					}
				}

				// Pick up and drop off address empty validation
				if ($("#pickup-address1").val() == '' || $("#dropoff-address1").val() == '') {
					toastr.error(chauffeur_pickup_dropoff_error);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}
				
				// Minimum notice time validation
				if (check_booking_time($(".time-hour1").val(), $(".time-min1").val(), $(".pickup-date1").val()) == true) {
					//toastr.error(chauffeur_min_time_before_booking_error);
					var mwt2 = $('#mwt-2').val();
					var chauffeur_min_time_before_booking_error1 = 'Sorry we do not accept same day online bookings less than '+ mwt2 +' in advance of the pick up time';
					toastr.error(chauffeur_min_time_before_booking_error1);
					chauffeur_form_submit.push(false);
					return false;
				}

				// Return trip
				if ($('#return-journey').val() === 'true') {

					// Google autocomplete validation
					if ((returnAdd == false && returnDropoff == false) || (returnPickupViaList.length > 0 && $.inArray(false, returnPickupViaList) !== -1)) {
						toastr.error(chauffeur_autocomplete);
						chauffeur_form_submit.push(false);
						return false;
					} else {
						if (returnAdd == returnDropoff) {
							chauffeur_form_submit.push(true);
						} else {
							toastr.error(chauffeur_autocomplete);
							chauffeur_form_submit.push(false);
							return false;
						}
					}

					// Pick up and drop off address empty validation
					if ($("#return-address").val() == '' || $("#return-dropoff").val() == '') {
						toastr.error(chauffeur_pickup_dropoff_error);
						chauffeur_form_submit.push(false);
						return false;
					} else {
						chauffeur_form_submit.push(true);
					}

					// Minimum notice time validation
					if (check_booking_time($(".return-time-hour1").val(), $(".return-time-min1").val(), $(".return-date1").val()) == true) {
						//toastr.error(chauffeur_min_time_before_booking_error);
						var mwt2 = $('#mwt-2').val();
						var chauffeur_min_time_before_booking_error1 = 'Sorry we do not accept same day online bookings less than '+ mwt2 +' in advance of the pick up time';
						toastr.error(chauffeur_min_time_before_booking_error1);
						chauffeur_form_submit.push(false);
						return false;
					} else {
						chauffeur_form_submit.push(true);
					}

				} // return_trip is true

			} // distance.

			if (!$('.time-hour1').val()) {
				toastr.error('Please select Pickup Time.');
				return false;
			}else if (!$('.time-min1').val()) {
				toastr.error('Please select Pickup Time.');
				return false;
			}
			// Validate hourly tab form if selected
			if (chauffeur_active_tab == 'hourly') {

				// Google autocomplete validation
				if (pickup_2 == false && dropoff_2 == false) {
					toastr.error(chauffeur_autocomplete);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					if (pickup_2 == dropoff_2) {
						chauffeur_form_submit.push(true);
					} else {
						toastr.error(chauffeur_autocomplete);
						chauffeur_form_submit.push(false);
						return false;
					}
				}

				// Pick up and drop off address empty validation
				if ($("#pickup-address2").val() == '' || $("#dropoff-address2").val() == '') {
					toastr.error(chauffeur_pickup_dropoff_error);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

				// Minimum notice time validation
				if (check_booking_time($(".time-hour2").val(), $(".time-min2").val(), $(".pickup-date2").val()) == true) {
					//toastr.error(chauffeur_min_time_before_booking_error);
					var mwt2 = $('#mwt-2').val();
					var chauffeur_min_time_before_booking_error1 = 'Sorry we do not accept same day online bookings less than '+ mwt2 +' in advance of the pick up time';
					toastr.error(chauffeur_min_time_before_booking_error1);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

				// Validate hourly
				if (parseInt($('.ch-num-hours').val()) < parseInt(hourly_minimum)) {
					toastr.error(ch_minimum_hourly_alert);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

			}

			// Validate flat rate tab form if selected
			if (chauffeur_active_tab == 'flat_rate') {

				// Minimum notice time validation
				if (check_booking_time($(".time-hour3").val(), $(".time-min3").val(), $(".pickup-date3").val()) == true) {
					//toastr.error(chauffeur_min_time_before_booking_error);
					var mwt2 = $('#mwt-2').val();
					var chauffeur_min_time_before_booking_error1 = 'Sorry we do not accept same day online bookings less than '+ mwt2 +' in advance of the pick up time';
					toastr.error(chauffeur_min_time_before_booking_error1);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

			}

			// Submit
			if ($.inArray(false, chauffeur_form_submit) == -1) {

				if (chauffeur_active_tab == 'distance') {
					$("#formOneWay").trigger('submit');
				}

				if (chauffeur_active_tab == 'hourly') {
					$("#formHourly").trigger('submit');
				}

				if (chauffeur_active_tab == 'flat_rate') {
					$("#formFlat").trigger('submit');
				}

			}

		}

		// AJAX booking process
		if ($(this).attr("class") == 'bookingbutton1') {
			

			if ($(".booking-step-2-form").val() == '1') {
				// Validate vehicle selection
				if ($(".selected-vehicle-name").val() == '') {
					// Do not submit
					toastr.error(chauffeur_select_vehicle);
					return false;
				}

				// Validate form fields
				var ch_validation_error = false;

				$('.required-form-field').each(function () {
					if ($.trim($(this).val()) == '') {
						ch_validation_error = true;
					}
				});

				if (ch_validation_error == true) {
					toastr.error(chauffeur_complete_required);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

				// Email validation
				if (chauffeur_email_validation($(".form-email-address").val()) == false) {
					toastr.error(chauffeur_valid_email);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

				// Phone number validation
				if ($.isNumeric($(".form-phone-number").val())) {
					chauffeur_form_submit.push(true);
				} else {
					toastr.error(chauffeur_valid_phone);
					chauffeur_form_submit.push(false);
					return false;
				}

				// Max bags validation
				if (Number($(".num-bags").val()) > Number($(".selected-vehicle-bags").val())) {
					toastr.error(chauffeur_valid_bags);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

				// Max passengers validation
				if (Number($(".num-passengers").val()) > Number($(".selected-vehicle-passengers").val())) {
					toastr.error(chauffeur_valid_passengers);
					chauffeur_form_submit.push(false);
					return false;
				} else {
					chauffeur_form_submit.push(true);
				}

				if (chauffeur_terms_set == 'true') {

					if ($('.terms_and_conditions').is(':checked') == false) {
						toastr.error(chauffeur_terms);
						return false;
					}

				}

			}

			// Submit
			if ($.inArray(false, chauffeur_form_submit) !== -1) {

				// Do not submit

			} else {
				var $form1 = $(this).closest('.booking-form-1');
				var formData1 = $form1.serializeArray();
				formData1.push({
					name: this.name,
					value: this.value
				});

				// Post form via AJAX
				$.ajax({
					type: 'POST',
					url: AJAX_URL,
					data: formData1,
					dataType: 'json',
					success: function (response) {

						// Fade divs and add loading image between booking steps
						$('.widget-booking-form-wrapper, .booking-step-intro, .full-booking-wrapper-3, .select-vehicle-wrapper, .trip-details-wrapper').css('opacity', '1');

						// AJAX success response
						if (response.status == 'success') {
							$('.booking-form-1')[0].reset();
						}

						// Display outside in divs
						$('.booking-step-wrapper').html(response.booking_step_wrapper);
						$('.booking-form-content').html(response.booking_form_content);

						// Load prettyPhoto in response
						//$("a[data-gal^='prettyPhoto']").prettyPhoto();

						// Scroll to top for each booking step
						$('html,body').animate({
							scrollTop: $(".booking-step-wrapper").offset().top
						});

						// Add selected vehicle data in hidden fields
						$(".select-vehicle-wrapper").on('click', '.vehicle-section', function () {
							$('.vehicle-section').removeClass("selected-vehicle");
							$(this).toggleClass("selected-vehicle");
							$('.vehicle-section .vehicle-book-now-button').text("Select");
							$(this).find('.vehicle-book-now-button').text("Selected");
							$('.selected-vehicle-price').val($(this).attr('data-price'));
							$('.selected-vehicle-name').val($(this).attr('data-title'));

							$('.pickup-price').val($(this).attr('price-p'));
							$('.return-price').val($(this).attr('price-r'));

							$('.mtgt-price-p').val($(this).attr('mtgt-p'));
							$('.mtgt-price-r').val($(this).attr('mtgt-r'));

							$('#first-journey-greet option[value="true"]').text('Yes (+£'+$(this).attr('mtgt-p')+')');
							$('#return-journey-greet option[value="true"]').text('Yes (+£'+$(this).attr('mtgt-r')+')');

							$('.selected-vehicle-bags').val($(this).attr('data-bags'));
							$('.selected-vehicle-passengers').val($(this).attr('data-passengers'));

						});
						$(".atb-coupon-box a").click(function(){
							$(".atb-coupon-box-form-inside").toggle('slow');
						});
						$(".atb-coupon-box-form-inside button").click(function(){
							$('.atb-coupon-box-form-inside').addClass('box-disabled');
							var coupon = $('.atb-coupon-box-form-inside input').val();
							var price = $('input[name="atb-actual-price"]').val();
							if(coupon == ''){
								$('.atb-coupon-box-form-inside').removeClass('box-disabled');
								toastr.error('Please enter coupon code.');
							}else{
								var couponData = {
									action: 'atb_coupon_action',
									coupon : coupon,
									price : price
								};
								$.post(AJAX_URL, couponData, function(response) {
									$('.atb-coupon-box-form-inside').removeClass('box-disabled');
									var ajaxdata = JSON.parse(response);
									var valid = ajaxdata.valid;
									var coupon = ajaxdata.coupon;
									var price = ajaxdata.price;
									if(valid == 'yes'){
										var total = parseFloat(price).toFixed(2);
										toastr.success(coupon);
										$('.total-price-inner span.atb-actual-price').addClass('text-cross');
										$('.total-price-inner span.atb-discounted-price').show();
										$('.total-price-inner span.atb-discounted-price').html('£'+total);
										$('.atb-coupon-notice').show();
										$('.atb-coupon-notice').html(coupon);
										$('.atb-coupon-box-form-inside').hide();
										$('.atb-coupon-box-form-inside input').val('');
										$('input[name="selected-vehicle-price"]').val(total);
									}else{
										toastr.error(coupon);
									}
								});
							}
						});


					}

				});

				// Fade divs and add loading image between booking steps
				$('.widget-booking-form-wrapper, .booking-step-intro, .full-booking-wrapper-3, .select-vehicle-wrapper, .trip-details-wrapper').css('opacity', '0.3');

			}

		}

	});
/*
	var dt = new Date();
	var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
	var getHours = String(dt.getHours()).padStart(2, '0');
	var getMinutes = dt.getMinutes();
	var counts = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55];

	var closest = counts.reduce(function(prev, curr) {
		return (Math.abs(curr - getMinutes) < Math.abs(prev - getMinutes) ? curr : prev);
	});

	var closeMin = String(closest).padStart(2, '0');

	if(closeMin <= 55){
		var closeMin1 = parseInt(closeMin) + parseInt(5);
	}else{
		var closeMin1 = closeMin;
	}
	if (getHours > 0) {
		$(".time-hour1, .return-time-hour1").val(parseInt(getHours) + 1);
		$(".time-min1, .return-time-min1").val(parseInt(closeMin1));
	}else {
		$(".time-hour1, .return-time-hour1").val(parseInt(getHours));
	}
	*/
	var dt = new Date();
	dt.setHours( dt.getHours() + 4 );
	
	var getHours = String(dt.getHours()).padStart(2, '0');
	var getMinutes = dt.getMinutes();
	var counts = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55];

	var closest = counts.reduce(function(prev, curr) {
		return (Math.abs(curr - getMinutes) < Math.abs(prev - getMinutes) ? curr : prev);
	});

	var closeMin = String(closest).padStart(2, '0');
	if(closeMin <= 55){
		var closeMin1 = parseInt(closeMin) + parseInt(5);
	}else{
		var closeMin1 = closeMin;
	}
	$(".time-hour1, .return-time-hour1").val(getHours);
	//$(".time-min1, .return-time-min1").val(parseInt(closeMin1));


});