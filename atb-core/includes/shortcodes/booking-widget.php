<?php

function booking_widget_shortcode( $atts, $content = null ) {
    $booking_page_url = site_url().'/booking';
    $html = '<div class="widget-booking-form-wrapper">
        <div id="booking-tabs">
        <ul class="nav clearfix">
            <li>
                <h4 class="atb-hbf-h4">INSTANT FARE QUOTE</h4>
            </li>
        </ul>
        <!-- BEGIN #tab-one-way -->
        <div id="tab-one-way">
            <!-- BEGIN .booking-form-1 -->
            <form id="formOneWay" action="'.$booking_page_url.'" class="booking-form-1" method="post">
            <div id="formOneWay-mh1">
                <div class="booking-form-time">
                    <label>Pick up date & time</label>
                </div>
                <div class="booking-form-hour-min-wrap">
                    <div class="booking-form-date">
                        <input type="text" name="pickup-date" class="datepicker pickup-date1" value="" placeholder="Pick Up Date" />
                    </div>
                    <div class="booking-form-hour">
                        <div class="select-wrapper">
                            <select name="time-hour" class="time-hour1">
                                '.time_input_hours().'	
                            </select>
                        </div>
                    </div>
                    <div class="booking-form-min">
                        <div class="select-wrapper">
                            <select name="time-min" class="time-min1">
                                <option value="00">00</option>
                                <option value="05">05</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="35">35</option>
                                <option value="40">40</option>
                                <option value="45">45</option>
                                <option value="50">50</option>
                                <option value="55">55</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wpt-input-box">
                    <input type="text" name="pickup-address" id="pickup-address1" class="pickup-address" value="" placeholder="Pick Up Address" />
                    <a id="onward-add-via" href="javascript:void(0);" class="add_button" title="Add waypoint">
                        <img src="'.ATT_URL.'assets/images/plus.svg"/>
                    </a>
                </div>
                <div class="pickup-via-input">
                    <div class="via-wrapper" id="via-wrapper"></div>
                    <div id="waypointsTotalCount" style="display: none">Something</div>
                </div>

                <div class="wpt2-input-box">
                    <input type="text" name="dropoff-address" id="dropoff-address1" class="dropoff-address" value="" placeholder="Drop Off Address" />
                </div>

                <div class="wpt-btn-box">
                    <button type="button" id="bookingstep1next" class="bookingstepto atb-bw-btn">Continue</button>
                </div>

                <div class="clear"></div>
                <div class="route-content">
                <div id="display-route-distance" class="left-col-distance"></div>
                <div id="display-route-time" class="right-col-time"></div>
                </div>
                <div class="clear"></div>
                <input type="hidden" name="route-distance-string" id="route-distance-string" />
                <input type="hidden" name="route-distance" id="route-distance" />
                <input type="hidden" name="route-time" id="route-time" />
                <div id="atbMap"></div>
            </div>
            <div id="formOneWay-mh2" style="display:none;">
                <div class="booking-form-pasbags">
                    <div class="booking-form-select-passengers">
                        <label for="num-passengers">Passengers</label>
                        <select name="num-passengers" id="num-passengers" class="num-passengers">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </div>
                    <div class="booking-form-select-bags">
                    <label for="num-bags">Bags</label>
                        <select name="num-bags" id="num-bags" class="num-bags">
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </div>
                </div>
                <div class="select-wrapper">
                    <select name="return-journey" id="return-journey">
                        <option value="false">One Way</option>
                        <option value="true">Return</option>
                    </select>
                </div>
                <div class="clear"></div>
                <!-- Added by PG. -->
                <div class="return-block" style="display: none;">
                    <label class="return-journey-details-title">RETURN JOURNEY DETAILS:</label>
                    <div class="wpt-input-box">
                        <input type="text" name="return-address" id="return-address" class="pickup-address" value="" placeholder="Return Pickup Address" />
                        <a id="return-add-via" href="javascript:void(0);" class="add_button" title="Add waypoint">
                            <img src="'.ATT_URL.'assets/images/plus.svg"/>
                        </a>
                    </div>
                    <div class="pickup-via-input">
                        <div class="via_wrapper" id="return-via-wrapper"></div>
                    </div>
                    <input type="text" name="return-dropoff" id="return-dropoff" class="return-dropoff" placeholder="Return Dropoff" />
                    
                    <div class="route-content">
                        <div id="display-return-route-distance" class="left-col-distance"></div>
                        <div id="display-return-route-time" class="right-col-time"></div>
                    </div>

                    <div class="clear"></div>
                    <div id="atbReturnMap"></div>
                    <input type="hidden" name="return-route-distance-string" id="return-route-distance-string" />
                    <input type="hidden" name="return-route-distance" id="return-route-distance" />
                    <input type="hidden" name="return-route-time" id="return-route-time" />

                    <div class="booking-form-time">
                        <label>Return Up Date &amp; Time</label>
                    </div>
                    <div class="booking-form-hour-min-wrap">
                        <div class="booking-form-date">
                            <input type="text" name="return-date" class="datepicker return-date1" value="" placeholder="Return Date" />
                        </div>
                        <div class="booking-form-hour">
                            <div class="select-wrapper">
                                
                                <select name="return-time-hour" class="return-time-hour1">
                                    '.time_input_hours().'
                                </select>
                            </div>
                        </div>
                        <div class="booking-form-min">
                            <div class="select-wrapper">
                                <select name="return-time-min" class="return-time-min1" id="return-time-min1">
                                    <option value="00">00</option>
                                    <option value="05">05</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="25">25</option>
                                    <option value="30">30</option>
                                    <option value="35">35</option>
                                    <option value="40">40</option>
                                    <option value="45">45</option>
                                    <option value="50">50</option>
                                    <option value="55">55</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div name="step2buttons1" id="step2buttons1">
                    <div class="step2buttons1-left01">
                        <button type="button" id="atb-hf-btn12" class="bookingstepback"><i class="fas fa-arrow-left"></i>Back</button>
                    </div>
                    <div class="step2buttons1-right01">
                        <button type="button" class="bookingbutton2">See Price <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
                <!-- END .return-block -->
                <input type="hidden" name="booking_reference" value="--value from widget--" />
                <input type="hidden" name="form_type" value="one_way" />
                <input type="hidden" name="external_form" value="true" />
                <input type="hidden" name="pcity" id="pcity">
                <input type="hidden" name="mwt-1" id="mwt-1">
                <input type="hidden" name="mwt-2" id="mwt-2">
                <input type="hidden" name="rcity" id="rcity">
            </div>
            <!-- END .booking-form-1 -->
            </form>
            <!-- END #tab-one-way -->
        </div>
        </div>
        <!-- END .widget-booking-form-wrapper -->
    </div>';
    return $html;	
}

add_shortcode( 'booking-widget', 'booking_widget_shortcode' );