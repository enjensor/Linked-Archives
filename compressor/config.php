<?php
#########################################
## Compressor option file ###############
#########################################
## Access control
$compress_options['username'] = "";
$compress_options['password'] = "";
## Path info
$compress_options['document_root'] = "./";
$compress_options['javascript_cachedir'] = "./compressor/";
$compress_options['css_cachedir'] = "./compressor/";
## Minify options
$compress_options['minify']['javascript'] = "1";
$compress_options['minify']['page'] = "1";
$compress_options['minify']['css'] = "1";
## Gzip options
$compress_options['gzip']['javascript'] = "1";
$compress_options['gzip']['page'] = "1";
$compress_options['gzip']['css'] = "1";
## Versioning
$compress_options['far_future_expires']['javascript'] = "1";
$compress_options['far_future_expires']['css'] = "1";
#########################################
?>