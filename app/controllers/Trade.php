<?php
class Trade extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
    public function go() {
        $pubkey=$this->input->post('pubkey');
        $msg=$this->input->post('msg');
        $sig=$this->input->post('sig');
        
        if (!preg_match("/^[a-zA-Z0-9]{66}$/", $pubkey)) {
            die(json_encode(array('status'=>-1,'msg'=>'公钥不正确')));
        }
        $this->load->model('Secp256k1');
        if (!$this->Secp256k1->verify($msg,$sig,$pubkey)) {
            die(json_encode(array('status'=>-1,'msg'=>'签名不正确')));
        }
        @ $data=explode(',', $msg);
        if (count($data)!=3) {
            die(json_encode(array('status'=>-1,'msg'=>'转账指令不正确')));
        }
        $t160=$data[0];
        if (!preg_match("/^[a-zA-Z0-9]{40}$/", $t160)) {
            die(json_encode(array('status'=>-1,'msg'=>'转出目的地址不正确')));
        }
        $amount = intval($data[1]);
        $token = intval($data[2]);
        if ($amount<1) {
            die(json_encode(array('status'=>-1,'msg'=>'转出金额不正确')));
        }
        if ($token<1) {
            die(json_encode(array('status'=>-1,'msg'=>'转账网络凭据失效，请重试')));
        }
        $this->load->model('Tradeutil');
        $f160 = $this->Tradeutil->getR160FromPubkeyHexString($pubkey);
        if (!$f160) {
            die(json_encode(array('status'=>-1,'msg'=>'公钥封装不正确')));
        }
        $this->load->model('Blockchain');
        try {
            $this->Blockchain->transfer($f160,$t160,$amount,$token);            
        } catch (Exception $e1) {
            die(json_encode(array('status'=>-1,'msg'=>$e1->getMessage())));
        }
        die(json_encode(array('status'=>1,'msg'=>'OK')));
    }
    public function token() {
        $f160=$this->input->post('f160');
        $t160=$this->input->post('t160');
        $v=intval($this->input->post('v'));
        if ($v<1) {
            die(json_encode(array('status'=>-1,'token'=>'','msg'=>'转账金额不正确')));
        }
        if (strlen($f160)!=40) {
            die(json_encode(array('status'=>-1,'token'=>'','msg'=>'转出地址不正确')));
        }
        if (strlen($t160)!=40) {
            die(json_encode(array('status'=>-1,'token'=>'','msg'=>'转入地址不正确')));
        }
        $this->load->model('Tradeutil');
        if (!$this->Tradeutil->chkLastTrans($f160,$t160,$v)) {
            die(json_encode(array('status'=>-1,'token'=>'','msg'=>'您曾在30分钟内向相同的地址转账过一笔相同金额的交易。\n由于网络的非实时性，为了避免您这次操作是重复操作，网络自动阻止了您的这次交易。\n如果您确认想再次给相同的人转账相同金额，请等30分钟后，或者将一笔交易拆成两笔总和相同的交易')));
        }
        $token=$this->Tradeutil->genToken($f160);
        die(json_encode(array('status'=>1,'token'=>"{$token}",'msg'=>"OK")));
    }
}