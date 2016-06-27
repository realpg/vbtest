<?php
class Tradeutil extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function genToken($r160) {
        $sql="REPLACE INTO qpc_token (r160,tm,v) VALUES (0x" . $r160 . "," . time() . ",0)";
        $query=$this->db->query($sql);
        return $this->db->insert_id();
    }
    public function chkLastTrans($f160,$t160,$v) {
        $tm=time()-1800;
        $sql = "DELETE FROM qpc_token WHERE tm<{$tm}";
        $this->db->query($sql);
        
        $sql="SELECT token FROM qpc_token WHERE f160=0x{$f160} AND t160=0x{$t160} AND v={$v}";
        $query=$this->db->query($sql);
        if ($query->num_rows()==0) {
            return true;
        }
        return false;
    }
    public function getR160FromPubkeyHexString($hex) {
        if (strlen($hex)!=66) {
            return false;
        }
        $pubkeyRaw=pack("H*",$hex);
        $sha256Raw = hash('sha256',$pubkeyRaw,true);
        $array['string'] = hash('ripemd160',$sha256Raw);
        $array['raw'] = hash('ripemd160',$sha256Raw,true);
        return $array;
    }
}