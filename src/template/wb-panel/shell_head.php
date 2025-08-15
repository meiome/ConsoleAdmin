<?php $this->startBlock('head'); ?>
	

<title><?php echo $this->getData('PageTitle', 'Page Title'); ?></title>

		<!-- Include viewport Metatag  -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />


	<!-- Include Android Metatag -->
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="icon" sizes="192x192" href="/iphone_webapp_icon.png">


	<!-- Include Safari & Iphone Metatag -->
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-title" content="">

	<!-- <link rel=icon href=favico.png sizes="16x16" type="image/png"> -->


	<!-- Include Font -->
	<link rel="stylesheet" type="text/css" href="/fonts/Roboto/Roboto.css">
	<link rel="stylesheet" type="text/css" href="/fonts/Quicksand/Quicksand.css">
	<link rel="stylesheet" type="text/css" href="/fonts/Material-design-icons/material-icons.css">

	<link rel="stylesheet" type="text/css" href="/css/webbrick/wb_layout.css">
	<link rel="stylesheet" type="text/css" href="/css/webbrick/wb_components.css">
	<link rel="stylesheet" type="text/css" href="/css/webbrick/wb_md_icons.css">
	
	<link rel="stylesheet" type="text/css" href="/css/webbrick/wb_shell_layout.css">

	<link rel="stylesheet" type="text/css" href="/css/wb-panel.css">

	<script type="text/javascript" src="/js/wb_components.js"></script>
    <?php  //$this->printCss(); ?>


	<?php  //$this->printJavascript(); ?>

<?php $this->endBlock(); ?>
