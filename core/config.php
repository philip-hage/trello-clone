<?php 

$system = [];
$system['address'] = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . '/';

$system['scope'] = isset($_GET['scope']) ? $_GET['scope'] : NULL;
$system['action'] = isset($_GET['action']) ? $_GET['action'] : NULL;
$system['id'] = isset($_GET['id']) ? $_GET['id'] : NULL;
$system['data'] = isset($_GET['data']) ? $_GET['data'] : NULL;

$jsScripts = [];

$cssStyles = [];

date_default_timezone_set('Europe/Amsterdam');
