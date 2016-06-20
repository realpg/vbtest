<?php
class Sync extends CI_Controller {
    private $bitcoin;
    public function __construct() {
        parent::__construct();
    }
    public function get_some() {
        $last_tid = intval($this->input->post('last_tid'));
        if ($last_tid<1) die(json_encode(array('status'=>-1)));
        $this->load->model('Blockchain');
        $bc=$this->Blockchain->fetch100($last_tid);
        if (empty($bc)) {
            die(json_encode(array('status'=>0,'count'=>0)));
        }
        $result['status']=0;
        $result['count']=count($bc);
        $result['content']='';
        foreach ($bc as $row) {
            if (!empty($result['content'])) {
                $result['content'] .="||";
            }
            $farr=str_split($row->f160);
            $tarr=str_split($row->t160);
            $f="";$t="";
            for ($i=0;$i<20;$i++) {
                $f .= sprintf("%02X",ord($farr[$i]));
                $t .= sprintf("%02X",ord($tarr[$i]));
            }
            $result['content'] .= "{$row->id}|{$f}|{$t}|{$row->amount}|{$row->req_time}|{$row->acc_time}";
        }
        die(json_encode($result));
    }
}
