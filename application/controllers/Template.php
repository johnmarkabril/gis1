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
}
