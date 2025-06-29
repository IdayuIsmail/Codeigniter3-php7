<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['admin'] = 'login';
$route['oauth/login'] = 'oauth/login';
$route['callback'] = 'oauth/callback';
