<?php
include_once('class_img.php');
$cat = new img_cat;

//here get info about catalog before any manipulations
$cat->get_info_before();

//here resave images and load info about them
$cat->save_images();

//result
$cat->get_info_after();

$cat->show_result();