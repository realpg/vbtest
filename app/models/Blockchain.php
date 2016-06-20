<?php
class Blockchain extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public function fetch100($lastid) {
        $this->db->where('id >',$lastid);
        $this->db->order_by('id',asc);
        $this->db->limit(100);
        $query=$this->db->get('qpc_trans');
        if ($query->num_rows()>0) {
            return $query->result();
        }
        return false;
    }
    
}