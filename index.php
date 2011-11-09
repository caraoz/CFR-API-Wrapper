<?php

//example usage, retrives 1 CFR 1
echo "<PRE>";
include('class.cfr.api.php');

$cfr = new CFR_API();
$data = $cfr->get_vol( 1, 1 );

print_r( $data );