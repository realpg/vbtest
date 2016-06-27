<?php
class Secp256k1 extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public function verify($msg,$sig,$pubkey) {
        $context = secp256k1_context_create(SECP256K1_CONTEXT_VERIFY);
        $msg=hash('sha256',$msg,true);
        $pubkeyRaw = pack("H*",$pubkey);
        secp256k1_ec_pubkey_parse($context, $pubkey, $pubkeyRaw);
        $sigRaw=pack("H*",$sig);
        secp256k1_ecdsa_signature_parse_der($context,$sig, $sigRaw);
        $result = secp256k1_ecdsa_verify($context, $sig, $msg, $pubkey);
        if ($result==1) {
            return true;
        }
        return false;
    }
}