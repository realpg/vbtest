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
}