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
        $target_file = $_SERVER['DOCUMENT_ROOT'] . base_url() . 'data/' .$_FILES["fileToUpload"]["name"] ;
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
                                <div style="padding-top: 5px;">Total Crime Incidents: </div>
                                <div style="padding-top: 5px;">Total Clusters: </div>
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
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['radius_distance'] . '</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Total Crimes</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['size'] . '</td>
                                                <td style="width: 25%;border-top: 1px solid black"></td>
                                                <td style="width: 25%;border-top: 1px solid black"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;border-top: 1px solid black"></td>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Latitude</td>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Longhitude</td>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Actual Location</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;border-top: 1px solid black; font-weight: 700;">Cluster Center</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['lat'] . '</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . $obj['lng'] . '</td>
                                                <td style="width: 25%;border-top: 1px solid black">' . ( !empty($obj['address']) ? $obj['address'] : 'cannot get the address' ) . '</td>
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
}
