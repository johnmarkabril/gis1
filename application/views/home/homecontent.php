<div class="row h-100 no-gutters">
    <div class="col-md-9 order-12">
        <div class="map" id="map"></div>
    </div>
    <div class="col order-1">
        
        <div class="padding-large h-100">
            <label>Search Address</label>
            <select class="form-control" id="select-city">
                <option value="">CAMANAVA</option>
                <option value="Kalookan City">Caloocan</option>
                <option value="Malabon">Malabon</option>
                <option value="Navotas">Navotas</option>
                <option value="Valenzuela">Valenzuela</option>
            </select>

            <label>Crime</label>
            <select class="form-control" id="select-crime">
                <option value="All">All</option>
                <option value="ANTI-CARNAPPING ACT (R.A. 6539) MC">Carnapping</option>
                <option value="Drug Related Incident (RA 9165)">Drug Related Incident</option>
                <option value="HOMICIDE">Homicide</option>
                <option value="MURDER">Murder</option>
                <option value="PHYSICAL INJURIES">Physical Injuries</option>
                <option value="RAPE (Art. 266-A RC & R.A.8353)">Rape</option>
                <option value="ROBBERY">Robbery</option>
                <option value="THEFT">Theft</option>
                <option value="Vehicular Traffic Accident">Vehicular Traffic</option>
            </select>

            <div class="padding-top-normal">
                <label>Time range: </label> <span class="slider-time">12:00 AM</span> - <span class="slider-time2">11:59 PM</span>
                <div class="sliders_step1 padding-top-small">
                    <div id="slider-range"></div>
                </div>
            </div>

            <div class="padding-top-large">
                <select class="form-control" id="select-month">
                    <option value="All">All months</option>
                    <option value="Jan">January</option>
                    <option value="Feb">February</option>
                    <option value="Mar">March</option>
                    <option value="Apr">April</option>
                    <option value="May">May</option>
                    <option value="Jun">June</option>
                    <option value="Jul">July</option>
                    <option value="Aug">August</option>
                    <option value="Sep">September</option>
                    <option value="Oct">October</option>
                    <option value="Nov">November</option>
                    <option value="Dec">Dec</option>
                </select>
            </div>

            <div class="padding-top-normal">
                <select class="form-control" id="select-year">
                    <option value="All">All years</option>
                    <option value="2017">2017</option>
                    <option value="2016">2016</option>
                </select>
            </div>

            <div class="padding-top-normal">
                <input type="button" id="btn-filter" class="btn btn-default" value="Search" />
            </div>

            <a id="modal_heatmap_link">Show Heatmap</a>

            <div class="result w-100">
                <!-- <div class="recommendation-box"> -->
                    <div class="result-title">Recommendation</div>

                    <div id="result-body" class="text-center"></div>
                <!-- </div> -->
            </div>
        </div>


    </div>
</div>



<div class="modal fade" id="modal_heatmap" tabindex="-1" role="dialog" aria-labelledby="modal_heatmap">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="map map-modal" id="map_heatmap"></div>
            </div>
        </div>
    </div>
</div>