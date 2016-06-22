<?php
class Sync extends CI_Controller {
    private $bitcoin;
    public function __construct() {
        parent::__construct();
    }
    public function get_some() {
        $last_tid = intval($this->uri->segment(3));
        if ($last_tid<1) die(json_encode(array('status'=>-1)));
        $this->load->model('Blockchain');
        
        $result['status']=0;
        $result['topid']=$this->Blockchain->getMax();
        $bc=$this->Blockchain->fetch100($last_tid);
        if (empty($bc)) {
            $result['count']=0;
            die(json_encode($result));
        }
        
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
    public function pending() {
        $tids = $_POST['tid'];
        if (!is_array($tids)) die(json_encode(array('status'=>-1,'msg'=>'array required')));
        foreach ($tids as $k=>$v) if (!is_numeric($v)) die(json_encode(array('status'=>-1,'msg'=>'need numbers')));
        
        $this->load->model('Blockchain');
        $r=$this->Blockchain->getPending($tids);
        if (empty($r)) die(json_encode(array('status'=>0,'msg'=>'no change')));
        $result['status']=1;
        foreach ($r as $row) {
            if (!empty($result['content'])) {
                $result['content'] .="||";
            }
            $result['content'] .= "{$row->id}|{$row->acc_time}";
        }
        die(json_encode($result));
    }
}
