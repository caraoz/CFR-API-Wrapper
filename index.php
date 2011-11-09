<?php
error_reporting( E_ALL );
include('class.cfr.api.php');
$cfr = new CFR_API();

var_dump( $cfr->get_vol( 41, 1 ) );