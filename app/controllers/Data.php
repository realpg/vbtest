<?php
class Data extends CI_Controller {
    public function ins() {
        $sql = "INSERT INTO qpc_trans (f160,t160,amount,req_time,acc_time) VALUES (?, ?, ?, ?, ?)";
        for ($i=1;$i<1000;$i++) {
            $r1=sha1(microtime() . rand(1,99999999));
            $r2=sha1(microtime() . rand(1,99999999));
            $this->db->query($sql, array("0x{$r1}", "0x{$r2}",rand(1,9999),time(),time()+rand(1,3600)));
        }
        die("OK:{$i}");
    }
}