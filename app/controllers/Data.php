<?php
class Data extends CI_Controller {
    public function ins() {
        for ($i=1;$i<1000;$i++) {
            $r1=sha1(microtime() . rand(1,99999999));
            $r2=sha1(microtime() . rand(1,99999999));
            $a=rand(1,999);
            $t1=time();
            $t2=time()+rand(1,3600);
            $sql = "INSERT INTO qpc_trans (f160,t160,amount,req_time,acc_time) VALUES (0x{$r1}, 0x{$r2}, {$a}, {$t1}, {$t2})";
            $this->db->query($sql);
        }
        die("OK:{$i}");
    }
    public function show() {
        $this->db->limit(100);
        $this->db->order_by('id','asc');
        $query=$this->db->get('qpc_trans');
        foreach ($query->result() as $row) {
            $farr=str_split($row->f160);
            $tarr=str_split($row->t160);
            $f="";$t="";
            for ($i=0;$i<20;$i++) {
                $f .= sprintf("%02X",ord($farr[$i]));
                $t .= sprintf("%02X",ord($tarr[$i]));
            }
            echo "{$row->id}: [{$f}] -> [{$t}] {$row->amount}<br>";
        }
    }
    public function sigtest() {
        $pubkey=$this->input->post('publickey');
        $msg=$this->input->post('msg');
        $sig=$this->input->post('sig');
        if (empty($pubkey)) {
            $this->load->view('test/sigtest');
            return;
        }
        if (strlen($pubkey)!=66) {
            die('WRONG PUBLIC KEY!');
        }
        if (strlen($msg)!=64) {
            die('WRONG MSG');
        }
        $this->load->model('Tradeutil');
        $r160 = $this->Tradeutil->getR160FromPubkeyHexString($pubkey);
        echo "{$pubkey}:\n\t{$r160}\r\n";
        
        $context = secp256k1_context_create(SECP256K1_CONTEXT_VERIFY);
        
        $msgRaw = pack("H*", $msg);
        $sigRaw = pack("H*", $sig);
        $pubRaw = pack("H*", $pubkey);
        
        
        $publicKey = '';;
        secp256k1_ec_pubkey_parse($context, $publicKey, $pubRaw);
        $signature = '';
        secp256k1_ecdsa_signature_parse_der($context,$signature, $sigRaw);
        // Verify:
        $result = secp256k1_ecdsa_verify($context, $signature, $msgRaw, $publicKey);
        var_dump($result);
    }
}