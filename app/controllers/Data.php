<?php
class Data extends CI_Controller {
    public function ins() {
        for ($i=1;$i<1000;$i++) {
            $r1=sha1(microtime() . rand(1,99999999));
            $r2=sha1(microtime() . rand(1,99999999));
            $a=rand(1,9999);
            $t1=time();
            $t2=time()+rand(1,3600);
            $sql = "INSERT INTO qpc_trans (f160,t160,amount,req_time,acc_time) VALUES (0x{$r1}, 0x{$r2}, {$a}, {$t1}, {$t2})";
            $this->db->query($sql);
        }
        die("OK:{$i}");
    }
    public function show() {
        $this->db->limit(100);
        $query=$this->db->get('qpc_trans');
        foreach ($query->result() as $row) {
            echo "[{$row->f160}]<br>";
        }
    }
}