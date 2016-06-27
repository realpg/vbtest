<?php
class Blockchain extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch100($lastid) {
        $this->db->where('id >',$lastid);
        $this->db->order_by('id','asc');
        $this->db->limit(100);
        $query=$this->db->get('qpc_trans');
        if ($query->num_rows()>0) {
            return $query->result();
        }
        return false;
    }
    public function getMax() {
        $sql="select max(id) as maxid from qpc_trans";
        $query=$this->db->query($sql);
        if ($query->num_rows()==0) return 0;
        $row=$query->row();
        return $row->maxid;
    }
    public function getPending($idArray) {
        $this->db->select('id,acc_time');
        $this->db->where_in('id',$idArray);
        $this->db->where('acc_time >',0);
        $query=$this->db->get('qpc_trans');
        if ($query->num_rows()>0) {
            return $query->result();
        }
        return false;
    }
    public function transfer($f160,$t160,$v,$token) {
        if ($f160=='0000000000000000000000000000000000000000') {
            throw new Exception("创世数据禁止手动交易",1001);
            return;
        }
        $this->db->trans_begin();
        $query=$this->db->query("SELECT * FROM qpc_token WHERE token={$token} AND f160=0x{$f160} AND v=0 FOR UPDATE");
        if ($query->num_rows()==0) {
            $this->db->trans_rollback();
            throw new Exception("Token[{$token}]失效，请重新尝试转账[{$f160}]",1021);
            return;
        }
        $balance=$this->chkBalance($f160);
        if ($balance<$v) {
            $this->db->trans_rollback();
            throw new Exception("网络不接受您的交易:余额不足，\r\n请等待网络同步完毕确认您的真实可用余额",1031);
            return;
        }
        $tm=time()+1;
        $sql="INSERT INTO qpc_trans (f160,t160,amount,req_time) VALUES (0x{$f160},0x{$t160},{$v},{$tm})";
        $this->db->query($sql);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            throw new Exception("交易失败。原因：网络不稳定。",1041);
            return;
        }
        $balance=$this->chkBalance($f160);
        if ($balance<0) {
            $this->db->trans_rollback();
            throw new Exception("网络不接受您的交易:二次确认余额不足，\r\n请等待网络同步完毕确认您的真实可用余额",1031);
            return;
        }
        $sql="UPDATE qpc_token SET v={$v}, tm={$tm} WHERE token={$token}";
        $this->db->query($sql);
        if ($this->db->affected_rows()==0 || $this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            throw new Exception("撤销授权失败",1041);
            return;
        }
        $this->db->trans_commit();
    }
    public function chkBalance($r160) {
        $balance=0;
        $sql="SELECT SUM(amount) AS b1 ,0 AS b2 FROM qpc_trans WHERE f160=0x{$r160} UNION ALL SELECT 0,SUM(amount) FROM qpc_trans WHERE t160=0x{$r160}";
        $query=$this->db->query($sql);
        foreach ($query->result() as $row) {
            $balance += $row->b2;
            $balance -= $row->b1;
        }
        return $balance;
    }
}