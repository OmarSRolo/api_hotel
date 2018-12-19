<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$config['useragent'] = "CodeIgniter";
$config['mailpath'] = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
$config['protocol'] = "smtp";
$config['smtp_host'] = "localhost";
$config['smtp_port'] = "25";
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['wordwrap'] = true;
$config['send_multipart'] = false;