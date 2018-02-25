<script src="<?php echo base_url();?>public/js/jquery-3.2.1/jquery-3.2.1.min.js"></script>
<script src="<?php echo base_url();?>public/js/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo base_url();?>public/js/tether.min.js"></script>
<script src="<?php echo base_url();?>public/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>public/js/clusters/markerclusterplus.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkKxF3e7vBDbxac115uVrQvHH2-2hmWnE&libraries=visualization,geometry&callback=initMap"></script>

<script>
	$(document).ready(function(){
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 1440,
            step: 60,
            values: [0, 1440],
            slide: function (e, ui) {
                var hours1 = Math.floor(ui.values[0] / 60);
                var minutes1 = ui.values[0] - (hours1 * 60);

                if (hours1.length == 1) hours1 = '0' + hours1;
                if (minutes1.length == 1) minutes1 = '0' + minutes1;
                if (minutes1 == 0) minutes1 = '00';
                if (hours1 >= 12) {
                    if (hours1 == 12) {
                        hours1 = hours1;
                        minutes1 = minutes1 + " PM";
                    } else {
                        hours1 = hours1 - 12;
                        minutes1 = minutes1 + " PM";
                    }
                } else {
                    hours1 = hours1;
                    minutes1 = minutes1 + " AM";
                }
                if (hours1 == 0) {
                    hours1 = 12;
                    minutes1 = minutes1;
                }

                $('.slider-time').html(hours1 + ':' + minutes1);

                var hours2 = Math.floor(ui.values[1] / 60);
                var minutes2 = ui.values[1] - (hours2 * 60);

                if (hours2.length == 1) hours2 = '0' + hours2;
                if (minutes2.length == 1) minutes2 = '0' + minutes2;
                if (minutes2 == 0) minutes2 = '00';
                if (hours2 >= 12) {
                    if (hours2 == 12) {
                        hours2 = hours2;
                        minutes2 = minutes2 + " PM";
                    } else if (hours2 == 24) {
                        hours2 = 11;
                        minutes2 = "59 PM";
                    } else {
                        hours2 = hours2 - 12;
                        minutes2 = minutes2 + " PM";
                    }
                } else {
                    hours2 = hours2;
                    minutes2 = minutes2 + " AM";
                }

                $('.slider-time2').html(hours2 + ':' + minutes2);
            }
        });

	});

    let markers         =   [];
    let locations       =   [];
    let infoWindows     =   [];
    let infoWindows2    =   [];
    let infoWindows3    =   [];
    let markerLatLong   =   [];

    let global_crime_data_object, map, markerCluster, labels, mcOptions, globalObject, geocoder, address_from_latlng, heatmap, map_modal_heatmap, styledMapType;

	function initMap() {
        $.ajax({
            url: '<?php echo base_url(); ?>template/get_all_json_file',
            method: "POST",
            data: {
            },success:function(data){
                let crime_data_object   =   JSON.parse(data);

                let create_object       =   [];
                if ( crime_data_object.length > 0 ) {
                    $.each(crime_data_object, function(key,crime_data) {
                        let process_object  =   {
                            lat: parseFloat(crime_data.lat),
                            lng: parseFloat(crime_data.lng)
                        }
                        create_object.push(process_object);
                    });
                }
                
                locations       =   create_object;
                // MAKE THE OBJECT GLOBAL
                globalObject    =   crime_data_object;

                map = new google.maps.Map(document.getElementById('map'), {
                    maxZoom: 12,
                    minZoom:12,
                    zoom: 12,
                    navigationControl: true,
                    mapTypeControl: true,
                    scaleControl: true,
                    center: {lat: 14.6756139, lng: 120.9953632},
                    mapTypeControlOptions: {
                        mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain', 'styled_map']
                    }
                });

                styledMapType = new google.maps.StyledMapType(
                    [
                      {
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#ebe3cd"
                          }
                        ]
                      },
                      {
                        "elementType": "labels.text.fill",
                        "stylers": [
                          {
                            "color": "#523735"
                          }
                        ]
                      },
                      {
                        "elementType": "labels.text.stroke",
                        "stylers": [
                          {
                            "color": "#f5f1e6"
                          }
                        ]
                      },
                      {
                        "featureType": "administrative",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "color": "#c9b2a6"
                          }
                        ]
                      },
                      {
                        "featureType": "administrative.land_parcel",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "color": "#dcd2be"
                          }
                        ]
                      },
                      {
                        "featureType": "administrative.land_parcel",
                        "elementType": "labels.text.fill",
                        "stylers": [
                          {
                            "color": "#ae9e90"
                          }
                        ]
                      },
                      {
                        "featureType": "landscape.natural",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#dfd2ae"
                          }
                        ]
                      },
                      {
                        "featureType": "poi",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#dfd2ae"
                          }
                        ]
                      },
                      {
                        "featureType": "poi",
                        "elementType": "labels.text.fill",
                        "stylers": [
                          {
                            "color": "#93817c"
                          }
                        ]
                      },
                      {
                        "featureType": "poi.park",
                        "elementType": "geometry.fill",
                        "stylers": [
                          {
                            "color": "#a5b076"
                          }
                        ]
                      },
                      {
                        "featureType": "poi.park",
                        "elementType": "labels.text.fill",
                        "stylers": [
                          {
                            "color": "#447530"
                          }
                        ]
                      },
                      {
                        "featureType": "road",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#f5f1e6"
                          }
                        ]
                      },
                      {
                        "featureType": "road.arterial",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#fdfcf8"
                          }
                        ]
                      },
                      {
                        "featureType": "road.highway",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#f8c967"
                          }
                        ]
                      },
                      {
                        "featureType": "road.highway",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "color": "#e9bc62"
                          }
                        ]
                      },
                      {
                        "featureType": "road.highway.controlled_access",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#e98d58"
                          }
                        ]
                      },
                      {
                        "featureType": "road.highway.controlled_access",
                        "elementType": "geometry.stroke",
                        "stylers": [
                          {
                            "color": "#db8555"
                          }
                        ]
                      },
                      {
                        "featureType": "road.local",
                        "elementType": "labels.text.fill",
                        "stylers": [
                          {
                            "color": "#806b63"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.line",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#dfd2ae"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.line",
                        "elementType": "labels.text.fill",
                        "stylers": [
                          {
                            "color": "#8f7d77"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.line",
                        "elementType": "labels.text.stroke",
                        "stylers": [
                          {
                            "color": "#ebe3cd"
                          }
                        ]
                      },
                      {
                        "featureType": "transit.station",
                        "elementType": "geometry",
                        "stylers": [
                          {
                            "color": "#dfd2ae"
                          }
                        ]
                      },
                      {
                        "featureType": "water",
                        "elementType": "geometry.fill",
                        "stylers": [
                          {
                            "color": "#b9d3c2"
                          }
                        ]
                      },
                      {
                        "featureType": "water",
                        "elementType": "labels.text.fill",
                        "stylers": [
                          {
                            "color": "#92998d"
                          }
                        ]
                      }
                    ],
                    {name: 'Styled Map'}
                );

                map.mapTypes.set('styled_map', styledMapType);
                map.setMapTypeId('styled_map');

                var opt = { maxZoom: 12 };
                map.setOptions(opt);

                let marker = locations.map(function(location, i) {
                    return new google.maps.Marker({
                       position: location,
                       label: '1',
                       zIndex: 1
                    });
                });

                markers.push(marker);

                // LOAD ALL POLICE STATION
                // LoadAllPoliceStation();
            },error:function(){
                console.log('ERROR')
            }
        });
    }

    // THIS FUNCTION IS FOR LOADING ALL THE POLICE STATION IN POLICE_STATION FOLDER
    let LoadAllPoliceStation    =   function() {
        let markerPolice, policeLocations = [];

        $.ajax({
            url: '<?php echo base_url(); ?>template/get_all_police_station',
            method: "POST",
            data: {
            },success:function(data){
                let police_stations_data   =   JSON.parse(data);
                if ( police_stations_data.length > 0 ) {
                    $.each(police_stations_data, function(key, police_station) {
                        let process_object  =   {
                            lat: parseFloat(police_station.lat),
                            lng: parseFloat(police_station.lng),
                            address: police_station.address
                        }
                        policeLocations.push(process_object);
                    });
                }

                $.each(policeLocations, function(key, police_station) {
                    markerPolice = new google.maps.Marker({
                        position: new google.maps.LatLng(police_station.lat, police_station.lng),
                        icon:   "<?php echo base_url(); ?>public/img/police-station.png",
                        map: map,
                        zIndex: 1
                    });
                });

            },error:function(){
                console.log('ERROR')
            }
        });
    }

    // FUNCTION FOR CHECKING IF THE VALUE IS NULL, EMPTY
    let IsEmpty = function(value) {
        return ( $.trim( value ) == '' );
    }

    //  FUNCTION FOR SHOWING HIDING THE LOADING OVERLAY
    let LoadingOverlay  = function(status) {
        if ( status == 'show' ) {
            $('.loading').show();
        } else {
            $('.loading').hide();
        }
    }

    let setMapOnAll     =   function(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[0][i].setMap(map);
        }
    }

    //  DELETE ALL MARKER IN GOOGLE MAP EXCLUDED POLICE STATION
    let deleteMarkers   =   function() {
        if ( !IsEmpty(markerCluster) ) {
            markerCluster.clearMarkers();
        }
        markers = [];
        setMapOnAll(null);
    }

    let DaySelector = function(dayaCc) {
        switch (dayaCc) {
            case "Mon":
                return "Monday";
                break;
            case "Tue":
                return "Tuesday";
                break;
            case "Wed":
                return "Wednesday";
                break;
            case "Thu":
                return "Thursday";
                break;
            case "Fri":
                return "Friday";
                break;
            case "Sat":
                return "Saturday";
                break;
            case "Sun":
                return "Sunday";
                break;
        }
    }

    let MonthSelector = function(monthaCc) {
        switch (monthaCc) {
            case "Jan":
                return "January";
                break;
            case "Feb":
                return "February";
                break;
            case "Mar":
                return "March";
                break;
            case "Apr":
                return "April";
                break;
            case "May":
                return "May";
                break;
            case "Jun":
                return "June";
                break;
            case "Jul":
                return "July";
                break;
            case "Aug":
                return "August";
                break;
            case "Sep":
                return "September";
                break;
            case "Oct":
                return "October";
                break;
            case "Nov":
                return "November";
                break;
            case "Dec":
                return "December";
                break;
        }
    }

    //  FUNCTION FOR FILTERING CRIME CITY
    let FilterCrimeCityLocation = function(objectValues, select_city) {
        select_city     =   select_city.toLowerCase();
        return objectValues.filter(
            function(data){ 
                if ( !IsEmpty(data.location) ) {
                    let location_name    =   data.location.toLowerCase();
                    return location_name.indexOf(select_city) > -1;
                }
            }
        );
    }
    
    //  FUNCTION FOR FILTERING CRIME NAME
    let FilterCrimeName = function(objectValues, category) {
        return objectValues.filter(
            // function(data){ return data.crime == category }
            function(data) {
                return data.crime.indexOf(category) > -1;
            }
        );
    }
    
    //  FUNCTION FOR FILTERING CRIME DATE - CUSTOMDATE TO BE SPECIFIC
    let FilterCrimeDate = function(objectValues, date_value) {
        return objectValues.filter(
            function(data){ 
                if ( !IsEmpty(data.customdate) ) {
                    let custdate    =   data.customdate;
                    return custdate.indexOf(date_value) > -1;
                }
            }
        );
    }

    //  FUNCTION FOR FILTERING THE TIME RANGE
    let FilterCrimeTimeRange = function(objectValues, time1, time2) {
        let startTime   =   convertTo24Hour(time1) + ':00';
        let endTime     =   convertTo24Hour(time2) + ':00';
    
        return objectValues.filter(
            function(data){ 
                if ( !IsEmpty(data.time) ) {
                    let crime_time    =   data.time;
                    if ( crime_time >= startTime && crime_time < endTime )  {
                        return data;
                    }
                }
            }
        );
    }

    //  FUNCTION FOR CONVERTING THE TIME FROM 12 HOURS 12:00 AM TO 24 HOURS 00:00:00
    let convertTo24Hour     =   function(time) {
        var time    =   time.toLowerCase();
        var hours   =   parseInt(time.substr(0, 2));

        if(time.indexOf('am') != -1 && hours == 12) {
            time = time.replace('12', '00');
        }
        if(time.indexOf('pm')  != -1 && hours < 12) {
            time = time.replace(hours, (hours + 12));
        }

        var timesplit   =   time.split(':');
        if ( timesplit[0] >= 1 && timesplit[0] <= 9 ) {
            timesplit[0] = '0' + timesplit[0];
        }

        time    =   timesplit.join(':');

        return time.replace(/( am| pm)/, '');
    }

    let CloseAllInfoWindows = function() {
        for (var i=0;i<infoWindows.length;i++) {
            infoWindows[i].close();
        }
    }

    //  FUNCTION FOR FILTERING ALL THE POSSIBLE SCENARIO
    let FilterCrimeAll  =   function(){
        $('#btn-back-map').hide();
        deleteMarkers();

        global_crime_data_object    =   '';

        map = new google.maps.Map(document.getElementById('map'), {
            maxZoom:12,
            minZoom:12,
            zoom: 12,
            disableDefaultUI: false,
            scrollwheel: true,
            navigationControl: true,
            mapTypeControl: false,
            scaleControl: true,
            center: {lat: 14.6756139, lng: 120.9953632},
            mapTypeControlOptions: {
                mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain',
                        'styled_map']
            }
        });

        map.mapTypes.set('styled_map', styledMapType);
        map.setMapTypeId('styled_map');

        var opt = { maxZoom: 12 };
        map.setOptions(opt);

        let select_city         =   $('#select-city').val();
        let select_crime        =   $('#select-crime').val();
        let select_month        =   $('#select-month').val();
        let select_day          =   $('#select-day').val();
        let select_year         =   $('#select-year').val();
        let time1               =   $('.slider-time').html();
        let time2               =   $('.slider-time2').html();
        let crime_data_object   =   globalObject;
        let create_object       =   [];

        if ( !IsEmpty(select_city) && crime_data_object.length > 0 ) {
            crime_data_object   =   FilterCrimeCityLocation(crime_data_object, select_city);
        }

        if ( !IsEmpty(select_crime) && crime_data_object.length > 0 ) {
            if ( select_crime != 'CRIME' ) {
                crime_data_object   =   FilterCrimeName(crime_data_object, select_crime);
            }
        }

        if (crime_data_object.length > 0) {
            crime_data_object   =   FilterCrimeTimeRange(crime_data_object, time1, time2);
        }

        if ( !IsEmpty(select_month) && crime_data_object.length > 0 ) {
            if ( select_month != 'All' ) {
                crime_data_object   =   FilterCrimeDate(crime_data_object, select_month);
            }
        }

        if ( !IsEmpty(select_day) && crime_data_object.length > 0 ) {
            if ( select_day != 'All' ) {
                crime_data_object   =   FilterCrimeDate(crime_data_object, select_day);
            }
        }

        if ( !IsEmpty(select_year) && crime_data_object.length > 0 ) {
            if ( select_year != 'All' ) {
                crime_data_object   =   FilterCrimeDate(crime_data_object, select_year);
            }
        }

        $('#result-body').html(crime_data_object.length + ' incidents');
        marker          =   [];
        markerLatLong   =   [];



        let infowindow  =   new google.maps.InfoWindow({maxWidth: 350});
        let infowindow2 =   new google.maps.InfoWindow({maxWidth: 350});

        global_crime_data_object    =   crime_data_object;

        // marker
        $.each(crime_data_object, function(key, crime_object) {

            var iconPath        =   "";
            var crime_name_uc   =   crime_object.crime.toUpperCase();

            if ( crime_name_uc.indexOf("CARNAPPING") > -1 ) { 
                iconPath   =   "<?php echo base_url(); ?>public/img/carnapping.png";
            } else if ( crime_name_uc.indexOf("DRUG RELATED INCIDENT (RA 9165)") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/drug.png";
            } else if ( crime_name_uc.indexOf("HOMICIDE") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/homicide.png";
            } else if ( crime_name_uc.indexOf("MURDER") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/murder.png";
            } else if ( crime_name_uc.indexOf("PHYSICAL INJURIES") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/physicalinjury.png";
            } else if ( crime_name_uc.indexOf("RAPE") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/rape.png";
            } else if ( crime_name_uc.indexOf("ROBBERY") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/robbery.png";
            } else if ( crime_name_uc.indexOf("THEFT") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/theft.png";
            } else if ( crime_name_uc.indexOf("VEHICULAR TRAFFIC ACCIDENT") > -1 ) {
                iconPath   =   "<?php echo base_url(); ?>public/img/vta.png";
            }

            var nMarker     =   new google.maps.Marker({
                position: new google.maps.LatLng(crime_object.lat, crime_object.lng),
                icon:   iconPath,
                data_custom: crime_object
            });
            marker.push(nMarker);

                var content     =   'CRIME: '   +   crime_object.crime   +   '</br>';
                content         +=  'DATE: ' +   crime_object.customdate   +   '</br>';
                content         +=  'TIME: ' +   crime_object.customtime   +   '</br>';
                content         +=  'LATITUDE, LONGITUDE: '    +   crime_object.lat + ", " + crime_object.lng +  '</br>';
                content         +=  'LOCATION: '    +   crime_object.location   +   '</br>';
                content         +=  'MODUS: '   +   crime_object.modus   +   '</br>';

                google.maps.event.addListener(nMarker,'mouseover', (function(marker,content,infowindow){
                    CloseAllInfoWindows();
                    return function() {
                        infowindow.setContent(content); //set infowindow content to titles
                        infowindow.open(map, nMarker);
                        infoWindows.push(infowindow);
                    };

                })(nMarker,content,infowindow));

            markerLatLong.push(nMarker.position);
        });

        if ( !IsEmpty(select_city) ) {
            if ( select_city == 'Malabon' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/Malabon.kml',
                    map: map,
                    zoom: 12
                });
            } else if ( select_city == 'Valenzuela' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/Valenzuela.kml',
                    map: map,
                    zoom: 12
                });
            } else if ( select_city == 'Navotas' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/Navotassdfg.kml',
                    map: map,
                    zoom: 12
                });
            } else if ( select_city == 'Kalookan City' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/Caloocan.kml',
                    map: map,
                    zoom: 12
                });
            }   
        } else {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/All.kml',
                    map: map
                });

        }

        markers.push(marker);

        mcOptions = {
            gridSize: 40,
            maxZoom: 12,
            imagePath: "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
            zoomOnClick: true,
            averageCenter: true,
            ignoreHidden: true 
        };
        markerCluster = new MarkerClusterer(map, marker, mcOptions);


        google.maps.event.addListener(markerCluster, "mouseover", function (cluster) {
          let clusterMarker   =   cluster.getMarkers();
          var currentDistance =   0;

            $.each(clusterMarker, function(key, data) {
                var nMarker     =    new google.maps.Marker({
                    position: new google.maps.LatLng(data.position.lat(), data.position.lng()),
                });
                markerPath.push(nMarker.getPosition());            

                var centerCluster   =   cluster.getMarkers()[0].position;
                var markerPosition  =   nMarker.getPosition();
                var distance        =   google.maps.geometry.spherical.computeDistanceBetween(centerCluster, markerPosition);

                if ( currentDistance < distance ) {
                    currentDistance = distance;
                }
            });

            var kilometers =  (currentDistance * 0.001);

            if ( cluster.getSize() >= 2 ) {

                console.log('CLUSTER CENTER: ' + cluster.getCenter().lat() + ', ' + cluster.getCenter().lng());

                var content     =   '';
                var info = new google.maps.MVCObject;
                info.set('position', cluster.center_);

                geocoder = new google.maps.Geocoder();
                var latlng = new google.maps.LatLng(cluster.getCenter().lat(), cluster.getCenter().lng());
                geocoder.geocode({
                    'latLng': latlng
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            address_from_latlng     =   results[0].formatted_address;
                        } else {
                            address_from_latlng     =   'address not found';
                        }
                    }
                });

                var fullmonth   =   MonthSelector(select_month);
                var fullday     =   DaySelector(select_day);

                content     +=  '<div style="width:100%;padding:10px;font-size:16px;color:white;font-weight:bold;background-color:#4695F0;">RECOMMENDATION</div>';
                content     +=  '<div style="text-align:center;font-size:16px;">';
                content     +=  '   <div style="padding-top:20px;">' +  ( cluster.getSize() >= 11 && cluster.getSize() <= 17 ? "<span style='color:orange'>HIGH </span>" : ( cluster.getSize() <= 10 ? "" : "<span style='color:red'>VERY HIGH </span>" ) )  + select_crime + ' PRONE AREA </div>';
                content     +=  '   <div style="padding-top:20px;">INCREASE POLICE VISIBILITY IN THE ' + kilometers.toFixed(2) + 'KM RADIUS CENTERED AT </div>' ;
                content     +=  '   <div>' + address_from_latlng + '</div>';
                                    if ( time1 != '12:00 AM' || time2 != '11:59 PM' ) {
                content     +=  '       DURING ' + time1 + ' - ' + time2; 
                                    }
                                    if ( select_day != 'All' ) {
                content     +=  '       EVERY ' + fullday.toUpperCase();
                                    }
                                    if ( select_month != 'All' ) {
                content     +=  '       DURING THE MONTH OF ' + fullmonth.toUpperCase();
                                    }
                content     +=  '</div>';

                infowindow.setContent(content); //set infowindow content to titles
                infowindow.open(map, info);
                infoWindows.push(infowindow);
            }
        });

        var markerPath  =   [];

        google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {

            // console.log('CLUSTER CENTER: ' + cluster.getCenter().lat() + ', ' + cluster.getCenter().lng());
            $('#btn-back-map').show();
            $('#modal_heatmap_link').hide();
            let clusterMarker   =   cluster.getMarkers();

            for ( var x = 0; x < clusterMarker.length; x++ ) {
                console.log( (x+1) + ". " + clusterMarker[x].data_custom.crime + " " + clusterMarker[x].data_custom.lat + " - " + clusterMarker[x].data_custom.lng + " " + clusterMarker[x].data_custom.customdate + " " + clusterMarker[x].data_custom.customtime + " " + clusterMarker[x].data_custom.modus);
            }

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                disableDefaultUI: false,
                scrollwheel: true,
                navigationControl: true,
                mapTypeControl: true,
                scaleControl: true,
                center: {lat: cluster.getCenter().lat(), lng: cluster.getCenter().lng()},
                mapTypeControlOptions: {
                    mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain', 'styled_map']
                }
            });

            map.mapTypes.set('styled_map', styledMapType);
            map.setMapTypeId('styled_map');          

            CloseAllInfoWindows();

            var currentDistance =   0;

            $('#result-body').html(clusterMarker.length + ' incidents');
            $.each(clusterMarker, function(key, data) {

                var iconPath        =   "";
                var crime_name_uc   =   data.data_custom.crime.toUpperCase();

                if ( crime_name_uc.indexOf("CARNAPPING") > -1 ) { 
                    iconPath   =   "<?php echo base_url(); ?>public/img/carnapping.png";
                } else if ( crime_name_uc.indexOf("DRUG RELATED INCIDENT (RA 9165)") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/drug.png";
                } else if ( crime_name_uc.indexOf("HOMICIDE") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/homicide.png";
                } else if ( crime_name_uc.indexOf("MURDER") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/murder.png";
                } else if ( crime_name_uc.indexOf("PHYSICAL INJURIES") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/physicalinjury.png";
                } else if ( crime_name_uc.indexOf("RAPE") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/rape.png";
                } else if ( crime_name_uc.indexOf("ROBBERY") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/robbery.png";
                } else if ( crime_name_uc.indexOf("THEFT") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/theft.png";
                } else if ( crime_name_uc.indexOf("VEHICULAR TRAFFIC ACCIDENT") > -1 ) {
                    iconPath   =   "<?php echo base_url(); ?>public/img/vta.png";
                }

                var nMarker     =    new google.maps.Marker({
                    position: new google.maps.LatLng(data.position.lat(), data.position.lng()),
                    icon: iconPath,
                    map: map
                });
                markerPath.push(nMarker.getPosition());

                var content     =   'CRIME: '   +   data.data_custom.crime   +   '</br>';
                content         +=  'DATE: ' +   data.data_custom.customdate   +   '</br>';
                content         +=  'TIME: ' +   data.data_custom.customtime   +   '</br>';
                content         +=  'LATITUDE, LONGITUDE: '    +   data.data_custom.lat + ", " + data.data_custom.lng +  '</br>';
                content         +=  'LOCATION: '    +   data.data_custom.location   +   '</br>';
                content         +=  'MODUS: '   +   data.data_custom.modus   +   '</br>';

                google.maps.event.addListener(nMarker,'mouseover', (function(marker,content,infowindow){
                    return function() {
                        infowindow2.setContent(content);
                        infowindow2.open(map, nMarker);
                        infoWindows2.push(infowindow2);
                    };
                })(nMarker,content,infowindow2));

                var centerCluster   =   cluster.getMarkers()[0].position;
                var markerPosition  =   nMarker.getPosition();
                var distance        =   google.maps.geometry.spherical.computeDistanceBetween(centerCluster, markerPosition);

                if ( currentDistance < distance ) {
                    currentDistance = distance;
                }

                //console.log((key+1) + " - " + distance);
            });
            var centerIcon = "<?php echo base_url(); ?>public/img/police2.png"
            var nMarker2     =    new google.maps.Marker({
                    position: cluster.getCenter(),
                    icon: centerIcon,
                    map: map
                });
            markerPath.push(nMarker2.getPosition());

            var kilometers  =  (currentDistance * 0.001);
            //console.log('Kilometer: ' + kilometers.toFixed(2) + " - " + currentDistance );
            var cityCircle = new google.maps.Circle({
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.25,
                map: map,
                center: cluster.getCenter(),
                radius: currentDistance
            });

        });

        // ShowHeatMap();
    }

    var toggleStatusHeatmap =  "off";
    $(document).on('click', '#modal_heatmap_link', function(){

        if ( toggleStatusHeatmap == "off" ) {
            for ( var x = 0; x < marker.length; x++ ) {
                marker[x].setVisible(false);
            }
            ShowHeatMap();
            $('#modal_heatmap_link').text("Hide Heatmap");
            toggleStatusHeatmap =  "on";
        } else { 
            for ( var x = 0; x < marker.length; x++ ) {
                marker[x].setVisible(true);
            }
            toggleStatusHeatmap =  "off";
            $('#modal_heatmap_link').text("Show Heatmap");
            heatmap.setMap(heatmap.getMap() ? null : map);
        }

        markerCluster.repaint();       
    });

    $("#modal_heatmap").on("shown.bs.modal", function () {
        google.maps.event.trigger(map_modal_heatmap, "resize");
    });

    let ShowHeatMap = function() {
        heatmap = new google.maps.visualization.HeatmapLayer({
            data: markerLatLong,
            map: map,
            dissipating:true,
            maxIntensity:10,
            zoom: 12
        });

        heatmap.set('radius', heatmap.get('radius') ? null : 20);
        heatmap.set('opacity', heatmap.get('opacity') ? null : 1);
    }

    //  USER ACTION CHANGE
    $(document).on('click', '#btn-filter, #btn-back-map', function(){
        LoadingOverlay('show');
        toggleStatusHeatmap =  "off";
        $('#modal_heatmap_link').show();
        $('#btn_report').show();
        $('#modal_heatmap_link').text("Show Heatmap");
        // LoadAllPoliceStation();
        FilterCrimeAll();
        setTimeout(function() { 
            LoadingOverlay('hide'); 

            var mc_cluster_len      =   [];
            var mc_cluster_object   =   markerCluster.clusters_;

            $.each(mc_cluster_object, function(key, data) {
                if ( data.markers_.length >= 2 ) {
                    mc_cluster_len.push(data);
                } 
            });
            console.log("Number of clusters: " + mc_cluster_len.length);
            console.log(markerCluster);
        }, 1500);
    });

    $(document).on('click', '#btn_report', function(){
        let select_city         =   $('#select-city').val();
        let select_crime        =   $('#select-crime').val();
        let select_month        =   $('#select-month').val();
        let select_day          =   $('#select-day').val();
        let select_year         =   $('#select-year').val();
        let time1               =   $('.slider-time').html();
        let time2               =   $('.slider-time2').html();
        let mc_cluster_len      =   [];
        let mc_cluster_object   =   markerCluster.clusters_;
        let new_data_process    =   [];
        LoadingOverlay('show');

        $.each(mc_cluster_object, function(key, data) {

            let currentDistance =   0;
            let centerCluster   =   data.center_;
            let kilometers      =   0;
            let address_from_latlng     =   '';

            $.each(data.markers_, function(key, datas) {     
                let markerPosition  =   datas.getPosition();
                let distance        =   google.maps.geometry.spherical.computeDistanceBetween(centerCluster, markerPosition);

                if ( currentDistance < distance ) {
                    currentDistance = distance;
                }

                kilometers =  (currentDistance * 0.001);
            });

            geocoder    = new google.maps.Geocoder();
            var latlng  = centerCluster;
            geocoder.geocode({
                'latLng': latlng
            }, function(results, status) {
                if ( status == "OK" ) {
                    if (results[0]) {
                        address_from_latlng     =   results[0].formatted_address;
                    } else {
                        address_from_latlng     =   'address not found';
                    }
                }
                let process_object  =   {
                    lat             :   parseFloat(data.center_.lat()),
                    lng             :   parseFloat(data.center_.lng()),
                    size            :   data.markers_.length,
                    radius_distance :   kilometers,
                    address         :   address_from_latlng
                }
                new_data_process.push(process_object);

                if ( (key+1) == mc_cluster_object.length ) {
                    setTimeout(function() { 
                        LoadingOverlay('hide');

                        $.ajax({
                            url: '<?php echo base_url(); ?>template/generate_report',
                            method: "POST",
                            data: {
                                new_data_process    :   new_data_process,
                                select_city         :   select_city,
                                select_crime        :   select_crime,
                                select_month        :   select_month,
                                select_day          :   select_day,
                                select_year         :   select_year,
                                time1               :   time1,
                                time2               :   time2
                            },success:function(data){
                                $('#report_body').html(data);
                                $('#report_modal').modal('toggle');
                            },error:function(){
                            },complete:function(){
                            }
                        });

                        console.log(new_data_process);
                    }, 1000);
                }

            });

            

        });


        
        

        // 
    });

    $(document).on('click', '#btn_print', function(){
        var divToPrint=document.getElementById("report_body");
       newWin= window.open("");
       newWin.document.write(divToPrint.outerHTML);
       newWin.print();
       newWin.close();
    });

</script>