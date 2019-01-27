<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="author" content="John Doe">
    <meta name="description" content="">
    <meta name="keywords" content="HTML,CSS,XML,JavaScript">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>Home</title>
    <!-- Place favicon.ico in the root directory -->
    <link rel="apple-touch-icon" href="<?php echo base_url('assets/') ?>img/Code.png">
    <link rel="shortcut icon" type="image/ico" href="<?php echo base_url('assets_home/') ?>images/favicon.ico" />
    <!-- Plugin-CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>css/magnific-popup.css">
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>css/animate.css">
    <!-- Main-Stylesheets -->
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>css/normalize.css">
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>style.css">
    <link rel="stylesheet" href="<?php echo base_url('assets_home/') ?>css/responsive.css">
    <script src="<?php echo base_url('assets_home/') ?>js/vendor/modernizr-2.8.3.min.js"></script>

    <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body data-spy="scroll" data-target="#primary-menu">

    <div class="preloader">
        <div class="sk-folding-cube">
            <div class="sk-cube1 sk-cube"></div>
            <div class="sk-cube2 sk-cube"></div>
            <div class="sk-cube4 sk-cube"></div>
            <div class="sk-cube3 sk-cube"></div>
        </div>
    </div>
    <!--Mainmenu-area-->
    <div class="mainmenu-area" data-spy="affix" data-offset-top="100">
        <div class="container">
            <!--Logo-->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#primary-menu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand logo" >

                    <h2 align="center"><img src="<?php echo base_url('assets/')?>img/JSM.png" style="max-height: 70px" alt=""></h2>
                </a>
            </div>
            <!--Logo/-->
            <nav class="collapse navbar-collapse" id="primary-menu">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="#home-page">Home</a></li>
                    <!-- <li><a href="#feature-page">About</a></li> -->
                    <li><a href="#feature-page">Feature</a></li>
                    <li><a href="#contact-page">Contact</a></li>
                    <li><a href="<?php echo site_url('Login') ?>">Login</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <!--Mainmenu-area/-->




