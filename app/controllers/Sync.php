<?php
class Sync extends CI_Controller {
    private $bitcoin;
    public function __construct() {
        parent::__construct();
    }
    public function get_some() {
        $this->load->helper('bitcoin');
    }
}
