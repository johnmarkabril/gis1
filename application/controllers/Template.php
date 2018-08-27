<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->curpage = "Template"; 
    }

    public function index()
    {
        $details = array (
        );

        $data['content']    =   $this->load->view('home/homecontent', $details, TRUE);
        $data['curpage']    =   $this->curpage;
        $this->load->view('template', $data);
    }

    public function get_all_json_file()
    {
        $array      = array();

        foreach (glob('data/*.json') as $file) {
            $json_decode    =   json_decode(file_get_contents($file));

            $array  =  array_merge((array) $array, (array) $json_decode);
        }

        print_r(json_encode($array));
    }

    public function get_all_police_station()
    {
        $array      = array();

        foreach (glob('police_station/*.json') as $file) {
            $json_decode    =   json_decode(file_get_contents($file));

            $array  =  array_merge((array) $array, (array) $json_decode);
        }

        print_r(json_encode($array));
    }

    public function upload_json_file()
    {
        $target_file = $_SERVER['DOCUMENT_ROOT'] . base_url() . 'data/' . $_FILES["fileToUpload"]["name"] ;
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
        redirect('/');
    }

    public function generate_report()
    {
        $select_city    =   $this->input->post('select_city');
        $select_crime   =   $this->input->post('select_crime');
        $select_month   =   $this->input->post('select_month');
        $select_day     =   $this->input->post('select_day');
        $select_year    =   $this->input->post('select_year');
        $time1          =   $this->input->post('time1');
        $time2          =   $this->input->post('time2');
        $object         =   $this->input->post('new_data_process');
        $all_markers    =   $this->input->post('all_markers');
        $value1         =   $this->input->post('value1');
        $value2         =   $this->input->post('value2');
        $recommend      =   $this->input->post('recommend');

        $template   =   '
                            <center style="font-size: 20px; font-weight: 700;">
                                <div>CAMANAVA CRIME HOTSPOTS</div>
                                <div>SYSTEM REPORT</div>
                            </center>

                            <div style="padding-top: 20px;">Search Criteria</div>
                            <div style="padding: 0 0 0 30px;">
                                <div style="padding-top: 5px;">Location: ' . $select_city . '</div>
                                <div style="padding-top: 5px;">Crime: ' . $select_crime . '</div>
                                <div style="padding-top: 5px;">Year: ' . $select_year . '</div>
                                <div style="padding-top: 5px;">Month: ' . $select_month . '</div>
                                <div style="padding-top: 5px;">Day: ' . $select_day . '</div>
                                <div style="padding-top: 5px;">Time Range: ' . $time1 . ' - ' . $time2 . '</div>
                            </div>

                            <div style="padding-top: 20px;">Results</div>
                            <div style="padding: 0 0 0 30px;">
                                <div style="padding-top: 5px;">Total Crime Incidents: <b>'. $value2 . '</b> </div>
                                <div style="padding-top: 5px;">Total Clusters: <b>'. $value1 . '</b></div>
                                <div style="padding-top: 5px;">Recommendatory Action: <b>'. $recommend . '</b></div>
                            </div>

                            <div style="padding-top: 20px;">
                                <table cellpadding="5" cellspacing="0" style="width: 100%;">
                                    <tbody>
            ';

                                        foreach ( $object as $key => $obj ) {
        $template   .=  '
                                            <tr style="font-weight: 700;">
                                                <td style="width: 25%;border-top: 1px solid black">Cluster #</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . ( $key+1 ) . '</td>
                                                <td style="width: 25%;border-top: 1px solid black">Radius Size</td>
                                                <td style="width: 25%;border-top: 1px solid black"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Total Crimes</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['size'] . '</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['radius_distance'] . ' KM </td>
                                                <td style="width: 25%;border-top: 1px solid black"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;border-top: 1px solid black"></td>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Latitude</td>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Longitude</td>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Actual Location</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Cluster Center</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['lat'] . '</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['lng'] . '</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . ( !empty($obj['address']) ? $obj['address'] : 'Geocoder not able to retrieve address due to API constraint' ) . '</td>
                                            </tr>
                        ';
                                        }      
        $template   .=  '
                                    </tbody>
                                </table>
                            </div>
                        ';
        
        $template       .=  'CRIME OCCURENCES:
                                <div style="padding-top: 20px;">
                                    <table cellpadding="5" cellspacing="0" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <th style="width: 10%;border-bottom: 1px solid black;">#</th>
                                                <th style="width: 20%;border-bottom: 1px solid black;">Latitude</th>
                                                <th style="width: 20%;border-bottom: 1px solid black;">Longitude</th>
                                                <th style="width: 25%;border-bottom: 1px solid black;">Date</th>
                                                <th style="width: 25%;border-bottom: 1px solid black;">Time</th>
                                            </tr>
               ';

                                        foreach ( $all_markers as $key => $marker ) {
        $template   .=  '
                                            <tr>
                                                <td style="width: 20%;border: 1px solid black;">' . ( $key+1 ) . '</td>
                                                <td style="width: 20%;border: 1px solid black;">' . $marker['lat'] . '</td>
                                                <td style="width: 20%;border: 1px solid black;">' . $marker['lng'] . '</td>
                                                <td style="width: 20%;border: 1px solid black;">' . $marker['date'] . '</td>
                                                <td style="width: 20%;border: 1px solid black;">' . $marker['time'] . '</td>
                                            </tr>
                        ';
                                        }      
        $template   .=  '
                                        </tbody>
                                    </table>
                                </div>
                            ';

        echo $template;
    }

    public function generate_report_inside()
    {

        $select_city            =   $this->input->post('select_city');
        $select_crime           =   $this->input->post('select_crime');
        $select_month           =   $this->input->post('select_month');
        $select_day             =   $this->input->post('select_day');
        $select_year            =   $this->input->post('select_year');
        $time1                  =   $this->input->post('time1');
        $time2                  =   $this->input->post('time2');
        $object                 =   $this->input->post('new_data_process');
        $total_marker           =   $this->input->post('total_marker');
        $total_cluster_length   =   $this->input->post('total_cluster_length');
        $clickedClusterRadius   =   $this->input->post('clickedClusterRadius');
        $recommend      =   $this->input->post('recommend');

        $template   =   '
                            <center style="font-size: 20px; font-weight: 700;">
                                <div>CAMANAVA CRIME HOTSPOTS</div>
                                <div>SYSTEM REPORT</div>
                            </center>

                            <div style="padding-top: 20px;">Search Criteria</div>
                            <div style="padding: 0 0 0 30px;">
                                <div style="padding-top: 5px;">Location: ' . $select_city . '</div>
                                <div style="padding-top: 5px;">Crime: ' . $select_crime . '</div>
                                <div style="padding-top: 5px;">Year: ' . $select_year . '</div>
                                <div style="padding-top: 5px;">Month: ' . $select_month . '</div>
                                <div style="padding-top: 5px;">Day: ' . $select_day . '</div>
                                <div style="padding-top: 5px;">Time Range: ' . $time1 . ' - ' . $time2 . '</div>
                            </div>

                            <div style="padding-top: 20px;">Results</div>
                            <div style="padding: 0 0 0 30px;">
                                <div style="padding-top: 5px;">Total Crime Incidents: <b>'. (sizeof($object) - 1) . '</b> </div>
                                <div style="padding-top: 5px;">Total Clusters: <b>1</b></div>
                                <div style="padding-top: 5px;">Recommendatory Action: <b>'. $recommend . '</b></div>
                            </div>

                            <div style="padding-top: 20px;">
                                <table cellpadding="5" cellspacing="0" style="width: 100%;">
                                    <tbody>
            ';

        
        foreach ( $object as $key => $obj ) {
            if ( $key == 0 ) {
                $template   .=  '
                                        <tr style="font-weight: 700;">
                                            <td style="width: 25%;border-top: 1px solid black">Cluster #</td>
                                            <td style="width: 25%;border-top: 1px solid black">' . ( $key + 1 ) . '</td>
                                            <td style="width: 25%;border-top: 1px solid black">Radius Size</td>
                                            <td style="width: 25%;border-top: 1px solid black"> ' . $clickedClusterRadius . ' KM </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Total Crimes</td>
                                            <td style="width: 25%;border-top: 1px solid black">' . (sizeof($object) - 1) . '</td>
                                            <td style="width: 25%;border-top: 1px solid black"></td>
                                            <td style="width: 25%;border-top: 1px solid black"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;border-top: 1px solid black"></td>
                                            <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Latitude</td>
                                            <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Longitude</td>
                                            <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Actual Location</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Cluster Center</td>
                                            <td style="width: 25%;border-top: 1px solid black">' . $obj['lat'] . '</td>
                                            <td style="width: 25%;border-top: 1px solid black">' . $obj['lng'] . '</td>
                                            <td style="width: 25%;border-top: 1px solid black">' . $obj['address'] . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <div style="padding-top: 20px;">
                                <table cellpadding="5" cellspacing="0" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <th style="width: 10%;border-bottom: 1px solid black;">#</th>
                                            <th style="width: 20%;border-bottom: 1px solid black;">Latitude</th>
                                            <th style="width: 20%;border-bottom: 1px solid black;">Longitude</th>
                                            <th style="width: 20%;border-bottom: 1px solid black;">Actual Location</th>
                                            <th style="width: 25%;border-bottom: 1px solid black;">Date</th>
                                            <th style="width: 25%;border-bottom: 1px solid black;">Time</th>
                                        </tr>
                ';
            } else {

        
                $template   .=  '
                                        <tr>
                                            <td style="width: 20%;border: 1px solid black;">' . $key . '</td>
                                            <td style="width: 20%;border: 1px solid black;">' . $obj['lat'] . '</td>
                                            <td style="width: 20%;border: 1px solid black;">' . $obj['lng'] . '</td>
                                            <td style="width: 20%;border: 1px solid black;">' . ( !empty($obj['address']) ? $obj['address'] : 'Geocoder not able to retrieve address due to API constraint' ) . '</td>
                                            <td style="width: 20%;border: 1px solid black;">' . $obj['date'] . '</td>
                                            <td style="width: 20%;border: 1px solid black;">' . $obj['time'] . '</td>
                                        </tr>
                       ';
            }
        }

        $template   .=  '
                                    </tbody>
                                </table>
                            </div>
        ';

        echo $template;
    }

}
