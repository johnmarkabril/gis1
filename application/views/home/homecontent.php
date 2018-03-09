<div class="row h-100 no-gutters">
    <div class="col-md-9 order-12">
        <a id="btn-back-map">Back</a>
        <div class="map" id="map"></div>
    </div>
    <div class="col order-1">
        
        <div class="padding-large h-100">
            <label>SELECT LOCATION:</label>
            <select class="form-control" id="select-city">
                <option value="">CAMANAVA</option>
                <option value="Kalookan City">Caloocan</option>
                <option value="Malabon">Malabon</option>
                <option value="Navotas">Navotas</option>
                <option value="Valenzuela">Valenzuela</option>
            </select>

            <label>SELECT CRIME:</label>
            <select class="form-control" id="select-crime">
                <option value="CRIME">ALL</option>
                <option value="CARNAPPING">Carnapping</option>
                <option value="DRUG RELATED INCIDENT (RA 9165)">Drug Related Incident</option>
                <option value="HOMICIDE">Homicide</option>
                <option value="MURDER">Murder</option>
                <option value="PHYSICAL INJURIES">Physical Injuries</option>
                <option value="RAPE (Art. 266-A RC & R.A.8353)">Rape</option>
                <option value="ROBBERY">Robbery</option>
                <option value="THEFT">Theft</option>
                <option value="VEHICULAR TRAFFIC ACCIDENT">Vehicular Traffic</option>
            </select>


            <div class="padding-top-small">
            <label>YEAR:</label>
                <select class="form-control" id="select-year">
                    <option value="All">ALL</option>
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>/
                    <option value="2017">2017</option>
                    <option value="2016">2016</option>
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                    <option value="2013">2013</option>
                    <option value="2012">2012</option>
                    <option value="2011">2011</option>
                    <option value="2010">2010</option>
                </select>
            </div>

            <div class="padding-top-small">
			<label>MONTH:</label>			
                <select class="form-control" id="select-month">
                    <option value="All">ALL</option>
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
                    <option value="Dec">December</option>
                </select>
            </div>

            <div class="padding-top-small">
            <label>DAY:</label>            
                <select class="form-control" id="select-day">
                    <option value="All">ALL</option>
                    <option value="Mon">Monday</option>
                    <option value="Tue">Tuesday</option>
                    <option value="Wed">Wednesday</option>
                    <option value="Thu">Thursday</option>
                    <option value="Fri">Friday</option>
                    <option value="Sat">Saturday</option>
                    <option value="Sun">Sunday</option>
                </select>
            </div>

            <div class="padding-top-small">
                <label>TIME RANGE: </label> <span class="slider-time">12:00 AM</span> - <span class="slider-time2">11:59 PM</span>
                <div class="sliders_step1 padding-top-small">
                    <div id="slider-range"></div>
                </div>
            </div>

            <div class="padding-top-normal">
                <input type="button" id="btn-filter" class="btn btn-default" value="Search" />
            </div>
            
            <div class="padding-top-small">
                <button type="button" id="btn_report" class="btn btn-default-2">Generate Report</button>
            </div>
			
			<div class="padding-top-small">
                <button type="button" id="modal_heatmap_link" class="btn btn-default-2">Show Heatmap</button>
			</div>

            <div class="padding-top-small">
                <button type="button" class="btn btn-default-2" data-toggle="modal" data-target="#modal_upload_json">Upload File</button>
            </div>

            <div class="result w-100">
                <div id="result-body" class="text-center"></div>
            </div>
        </div>


    </div>
</div>



<div class="modal fade" id="modal_upload_json" tabindex="-1" role="dialog" aria-labelledby="modal_upload_json">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header align-center">
                <div class="w-100 align-center">Upload JSON File</div>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(); ?>template/upload_json_file" method="post" enctype="multipart/form-data">
                    <input type="file" class="" name="fileToUpload" id="fileToUpload">
                    <input type="submit" class="btn btn-default margin-top-large" value="Upload" name="submit">
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="report_modal" tabindex="-1" role="dialog" aria-labelledby="report_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-center">
                <div class="w-100 align-center">CRIME REPORT</div>
            </div>
            <div class="modal-body">
               
                <button class="btn btn-default margin-bottom-normal" id="btn_print_all_cluster">PRINT ALL REPORT</button>
                
                <div id="report_body">
                </div>
            </div>
        </div>
    </div>
</div>