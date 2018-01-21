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
                    zoom: 14,
                    disableDefaultUI: true,
                    scrollwheel: false,
                    navigationControl: false,
                    mapTypeControl: false,
                    scaleControl: false,
                    center: {lat: 14.6756139, lng: 120.9953632},
                });

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
                        zIndex: 99
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
            function(data){ return data.crime == category }
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
                    if ( crime_time >= startTime && crime_time <= endTime )  {
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

        deleteMarkers();

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            disableDefaultUI: true,
            scrollwheel: false,
            navigationControl: false,
            mapTypeControl: false,
            scaleControl: false,
            center: {lat: 14.6756139, lng: 120.9953632},
        });

        let select_city         =   $('#select-city').val();
        let select_crime        =   $('#select-crime').val();
        let select_month        =   $('#select-month').val();
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

        if ( !IsEmpty(select_year) && crime_data_object.length > 0 ) {
            if ( select_year != 'All' ) {
                crime_data_object   =   FilterCrimeDate(crime_data_object, select_year);
            }
        }

        if ( crime_data_object.length == 0 ) {
            create_object   =   [];
        } else {
            $.each(crime_data_object, function(key,crime_data) {
                let process_object  =   {
                    lat: parseFloat(crime_data.lat),
                    lng: parseFloat(crime_data.lng)
                }
                create_object.push(process_object);
            });
        }



        $('#result-body').html(crime_data_object.length + ' incidents');
        marker          =   [];
        markerLatLong   =   [];

        $.each(create_object, function(key, crime_object) {
            var nMarker     =   new google.maps.Marker({
                position: new google.maps.LatLng(crime_object.lat, crime_object.lng),
                label:   "1"
            });
            marker.push(nMarker);

            markerLatLong.push(nMarker.position);
        });

        if ( !IsEmpty(select_city) ) {
            if ( select_city == 'Malabon' ) {
                var ctaLayer = new google.maps.KmlLayer({
                    url: 'https://teko.ph/public/css/malabon.kml',
                    map: map
                });
            }
            
        }

        markers.push(marker);



        // heatmap.setMap(heatmap.getMap() ? null : map);
        // console.log(getPoints());
        // console.log(markerLatLong);

        mcOptions = {
            gridSize: 30,
            maxZoom: 15,
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
            zoomOnClick: true,
            // ignoreHiddenMarkers: true,
            averageCenter: true
        };
        markerCluster = new MarkerClusterer(map, marker, mcOptions);

        var infowindow = new google.maps.InfoWindow();

        console.log(markerCluster);

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
                content     +=  '   <div style="padding-top:20px;">' +select_crime + ' PRONE AREA </div>';
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
            let clusterMarker   =   cluster.getMarkers();

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 17,
                disableDefaultUI: true,
                scrollwheel: false,
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


                console.log( (key + 1) + '. ' + data.position.lat() + ' - ' + data.position.lng() );
            });
        });

        // ShowHeatMap();
    }

    $(document).on('click', '#modal_heatmap_link', function(){
        $('#modal_heatmap').modal('toggle');

        map_modal_heatmap   =   new google.maps.Map(document.getElementById('map_heatmap'), {
            zoom: 13,
            center: {lat: 14.6960358, lng: 120.9789405},
        });
        ShowHeatMap();


    });

    $("#modal_heatmap").on("shown.bs.modal", function () {
        google.maps.event.trigger(map_modal_heatmap, "resize");
    });

    let ShowHeatMap = function() {
        heatmap = new google.maps.visualization.HeatmapLayer({
            data: markerLatLong,
            map: map_modal_heatmap
        });

        var gradient = [
          'rgba(0, 255, 255, 0)',
          'rgba(0, 255, 255, 1)',
          'rgba(0, 191, 255, 1)',
          'rgba(0, 127, 255, 1)',
          'rgba(0, 63, 255, 1)',
          'rgba(0, 0, 255, 1)',
          'rgba(0, 0, 223, 1)',
          'rgba(0, 0, 191, 1)',
          'rgba(0, 0, 159, 1)',
          'rgba(0, 0, 127, 1)',
          'rgba(63, 0, 91, 1)',
          'rgba(127, 0, 63, 1)',
          'rgba(191, 0, 31, 1)',
          'rgba(255, 0, 0, 1)'
        ]
        heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
        heatmap.set('radius', heatmap.get('radius') ? null : 20);
        // heatmap.set('opacity', heatmap.get('opacity') ? null : 1);
    }

    //  USER ACTION CHANGE
    $(document).on('click', '#btn-filter', function(){
        LoadingOverlay('show');
        FilterCrimeAll();
        setInterval(function() { LoadingOverlay('hide'); }, 1500);
    });

</script>