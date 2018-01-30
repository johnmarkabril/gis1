<script src="<?php echo base_url();?>public/js/jquery-3.2.1/jquery-3.2.1.min.js"></script>
<script src="<?php echo base_url();?>public/js/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo base_url();?>public/js/tether.min.js"></script>
<script src="<?php echo base_url();?>public/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>public/js/clusters/markerclusterplus.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkKxF3e7vBDbxac115uVrQvHH2-2hmWnE&libraries=visualization&callback=initMap"></script>

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

    let map, markerCluster, labels, mcOptions, globalObject, geocoder, address_from_latlng, heatmap, map_modal_heatmap;
    let markerLatLong   =   [];

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
                    maxZoom: 13,
                    minZoom:13,
                    zoom: 13,
                    navigationControl: false,
                    mapTypeControl: false,
                    scaleControl: false,
                    center: {lat: 14.6756139, lng: 120.9953632},
                });

                var opt = { maxZoom: 13 };
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
                LoadAllPoliceStation();
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

    //  FUNCTION FOR FILTERING CRIME NAME
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

    function getPoints() {
        return [
          new google.maps.LatLng(14.6859, 121.02499999999998),
          new google.maps.LatLng(14.6861, 121.02600000000007),
          new google.maps.LatLng(14.6857, 121.02600000000007)
        ];
    }

    //  FUNCTION FOR FILTERING ALL THE POSSIBLE SCENARIO
    let FilterCrimeAll  =   function(){
        $('#btn-back-map').hide();
        deleteMarkers();

        map = new google.maps.Map(document.getElementById('map'), {
            maxZoom:13,
            minZoom:13,
            zoom: 13,
            disableDefaultUI: true,
            scrollwheel: false,
            navigationControl: false,
            mapTypeControl: false,
            scaleControl: false,
            center: {lat: 14.6756139, lng: 120.9953632},
        });

        var opt = { maxZoom: 13 };
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
            if ( select_crime != 'All' ) {
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

        // marker
        $.each(crime_data_object, function(key, crime_object) {
            var nMarker     =   new google.maps.Marker({
                position: new google.maps.LatLng(crime_object.lat, crime_object.lng),
                label:   "1",
                data_custom: crime_object
            });
            marker.push(nMarker);

                var content     =   'CRIME: '   +   crime_object.crime   +   '</br>';
                // content         +=  'CRIME TYPE: '  +   crime_object.crimetype   +   '</br>';
                content         +=  'DATE: ' +   crime_object.customdate   +   '</br>';
                content         +=  'TIME: ' +   crime_object.customtime   +   '</br>';
                content         +=  'LATITUDE, LONGITUDE: '    +   crime_object.lat + ", " + crime_object.lng +  '</br>';
                content         +=  'LOCATION: '    +   crime_object.location   +   '</br>';
                content         +=  'MODUS: '   +   crime_object.modus   +   '</br>';
                // content         +=  'TIME: '    +   crime_object.time   +   '</br>';

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
                    zoom: 13
                });
            } else if ( select_city == 'Valenzuela' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/Valenzuela.kml',
                    map: map,
                    zoom: 13
                });
            } else if ( select_city == 'Navotas' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/Navotassdfg.kml',
                    map: map,
                    zoom: 13
                });

                console.log(ctaLayer);
            } else if ( select_city == 'Kalookan City' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/Caloocan.kml',
                    map: map,
                    zoom: 13
                });
            }   
        } else {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/All.kml',
                    map: map
                });

        }

        markers.push(marker);
        console.clear();
        for ( var x = 0; x < marker.length; x++ ) {
            console.log((x+1) + " " + marker[x].data_custom.lat + ", " + marker[x].data_custom.lng);
        }

        var imagepath_url   =   "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m";

        if ( select_crime == "CARNAPPING" ) { 
            // imagepath_url   =   "";
        } else if ( select_crime == "DRUG RELATED INCIDENT (RA 9165)" ) {
            // imagepath_url   =   "";
        } else if ( select_crime == "HOMICIDE" ) {
            // imagepath_url   =   "";
        } else if ( select_crime == "MURDER" ) {
            // imagepath_url   =   "";
        } else if ( select_crime == "PHYSICAL INJURIES" ) {
            // imagepath_url   =   "";
        } else if ( select_crime == "RAPE (Art. 266-A RC & R.A.8353)" ) {
            // imagepath_url   =   "";
        } else if ( select_crime == "ROBBERY" ) {
            // imagepath_url   =   "";
        } else if ( select_crime == "THEFT" ) {
            imagepath_url   =   "<?php echo base_url(); ?>public/img/theft";
        } else if ( select_crime == "Vehicular Traffic Accident" ) {
            // imagepath_url   =   "";
        }










        mcOptions = {
            gridSize: 30,
            maxZoom: 13,
            imagePath: imagepath_url,
            zoomOnClick: true,
            // ignoreHiddenMarkers: true,
            averageCenter: true,ignoreHidden: true 
        };
        markerCluster = new MarkerClusterer(map, marker, mcOptions);



        google.maps.event.addListener(markerCluster, "mouseover", function (cluster) {
            if ( cluster.getSize() >= 4 ) {
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

                content     +=  '<div style="width:100%;padding:10px;font-size:16px;color:white;font-weight:bold;background-color:#4695F0;">RECOMMENDATION</div>';
                content     +=  '<div style="text-align:center;font-size:16px;">';
                content     +=  '   <div style="padding-top:20px;">' +  ( cluster.getSize() >= 11 && cluster.getSize() <= 17 ? "<span style='color:orange'>HIGH </span>" : ( cluster.getSize() <= 10 ? "" : "<span style='color:red'>VERY HIGH </span>" ) )  + select_crime + ' PRONE AREA </div>';
                content     +=  '   <div style="padding-top:20px;">INCREASE POLICE ACTIVITY IN THE 3KM RADIUS CENTERED AT </div>' ;
                content     +=  '   <div>' + address_from_latlng + '</div>';
                                    if ( time1 != '12:00 AM' || time2 != '11:59 PM' ) {
                content     +=  '       DURING ' + time1 + ' - ' + time2; 
                                    }
                                    
                                    if ( select_month != 'All' ) {
                content     +=  '       OF THE MONTH OF ' + select_month.toUpperCase();
                                    }
                content     +=  '</div>';

                infowindow.setContent(content); //set infowindow content to titles
                infowindow.open(map, info);
                infoWindows.push(infowindow);
            }
        });

        google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {

            $('#btn-back-map').show();
            console.clear();
            let clusterMarker   =   cluster.getMarkers();

            for ( var x = 0; x < clusterMarker.length; x++ ) {
                console.log( (x+1) + ". " + clusterMarker[x].data_custom.crime + " " + clusterMarker[x].data_custom.lat + " - " + clusterMarker[x].data_custom.lng + " " + clusterMarker[x].data_custom.customdate + " " + clusterMarker[x].data_custom.customtime + " " + clusterMarker[x].data_custom.modus);
            }

            map = new google.maps.Map(document.getElementById('map'), {
                // maxZoom:13,
                // minZoom:13,
                zoom: 16,
                disableDefaultUI: true,
                scrollwheel: true,
                navigationControl: false,
                mapTypeControl: false,
                scaleControl: false,
                center: {lat: cluster.getCenter().lat(), lng: cluster.getCenter().lng()}
            });

            CloseAllInfoWindows();

            $.each(clusterMarker, function(key, data) {
                var nMarker     =    new google.maps.Marker({
                    position: new google.maps.LatLng(data.position.lat(), data.position.lng()),
                    label:   "1",
                    map: map
                });

                var content     =   'CRIME: '   +   data.data_custom.crime   +   '</br>';
                // content         +=  'CRIME TYPE: '  +   data.data_custom.crimetype   +   '</br>';
                content         +=  'DATE: ' +   data.data_custom.customdate   +   '</br>';
                content         +=  'TIME: ' +   data.data_custom.customtime   +   '</br>';
                content         +=  'LATITUDE, LONGITUDE: '    +   data.data_custom.lat + ", " + data.data_custom.lng +  '</br>';
                content         +=  'LOCATION: '    +   data.data_custom.location   +   '</br>';
                content         +=  'MODUS: '   +   data.data_custom.modus   +   '</br>';
                // content         +=  'TIME: '    +   data.data_custom.time   +   '</br>';

                google.maps.event.addListener(nMarker,'mouseover', (function(marker,content,infowindow){
                    return function() {
                        infowindow2.setContent(content);
                        infowindow2.open(map, nMarker);
                        infoWindows2.push(infowindow2);
                    };
                })(nMarker,content,infowindow2));
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
            toggleStatusHeatmap =  "on";
        } else { 
            for ( var x = 0; x < marker.length; x++ ) {
                marker[x].setVisible(true);
            }
            toggleStatusHeatmap =  "off";
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
            zoom: 13
        });

        heatmap.set('radius', heatmap.get('radius') ? null : 25);
        heatmap.set('opacity', heatmap.get('opacity') ? null : 1);
    }

    //  USER ACTION CHANGE
    $(document).on('click', '#btn-filter, #btn-back-map', function(){
        LoadingOverlay('show');
        LoadAllPoliceStation();
        FilterCrimeAll();
        setInterval(function() { LoadingOverlay('hide'); }, 1500);
    });

</script>