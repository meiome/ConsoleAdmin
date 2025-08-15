<?php $this->startBlock('head'); ?>

<title><?php echo $this->getData('PageTitle', 'Page Title'); ?></title>

<!-- Include viewport Metatag  -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<!-- Include Android Metatag -->
<meta name="mobile-web-app-capable" content="yes">
<link rel="icon" sizes="192x192" href="/imgs/icons/favicon.png">


<!-- Include Safari & Iphone Metatag -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="">

<meta name="mobile-web-app-capable" content="yes">
<!-- <link rel=icon href=favico.png sizes="16x16" type="image/png"> -->


<!-- Include Font/css -->

<link rel="stylesheet" id="baseweb"  href="/css/vendite/vendite.css" type="text/css" media="all" />
<style>
<?php $this->printCssBlock(); ?>
</style>


<?php $this->endBlock(); ?>
