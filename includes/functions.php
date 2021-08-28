<?php

function unlock_video(){}

function check_coupon($coupon){}

function coupon_used($code, $is_expired = false ){}

function log_coupon($status, $coupon, $vid_id){}


function generate_code(){
    $bytes = random_bytes(2);
    return bin2hex($bytes);
}