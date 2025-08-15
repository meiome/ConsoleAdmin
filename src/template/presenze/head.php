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

<link rel="stylesheet" href="/css/presenze/presenze.css" type="text/css" media="all" />
<link rel="stylesheet" id="baseweb" href="/css/terminal/terminal.css" type="text/css" media="all" />
<link rel="stylesheet" href="/js/chosen_v1.8.7/chosen.min.css">
<script src="/js/jquery-3.3.1.min.js" type="text/javascript"></script>
<script src="/js/chosen_v1.8.7/chosen.jquery.min.js" type="text/javascript"></script>

<style>
<?php $this->printCssBlock(); ?>
</style>

<script>
  function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

  function deleteCookie(cname) {
    document.cookie = cname + "=;" + "expires=Thu, 01 Jan 1970 00:00:00 UTC" + ";path=/";
  }

  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
</script>

<?php $this->endBlock(); ?>
