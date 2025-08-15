<html>
	
	<head>
	<?php $this->printBlock( 'head' ); ?>
	<?php //$this->printBlock( '' );//$this->printCssLink(); ?>
	<?php //echo 'mondo'; ?>
	</head>

<body>
	<script>
		function wb_toogle_sidenav(navID, direction){
		var nav = document.getElementById(navID);
		if(nav){
			if( nav.classList.contains('show') ){
				nav.classList.remove('show');
			}else{
				nav.classList.add('show');
			}
		}
	}
	</script>
	<?php $this->printBlock('AppBar'); ?>
	<?php $this->printBlock('AppToolBar'); ?>
	
	<?php $this->printBlock('AppSideNavLeft'); ?>



	<div id="" class="wb-app-sidenav right pinned" style="display:none;">
		<div class="header">
			<ul class="wb-toolbar left">
				<li id="WBAppBarNav" onclick="wb_AppBarNavOpen('wbAppSideNav');"><img src="/img/icons/scalable/ic_menu_white_48px.svg"></li>
			</ul>
		</div>
		<div class="body">
			<ul class="wb-sidenav-list" style="">
				<li style="" onclick="wb_AppBarNavClose( 'wbAppSideNav' );"><span>CLOSE</span></li>
				<li style=""><span>DASHBOARD</span></li>

				<li style=""><span>UTENTI</span></li>
				<li><span>CLIENTI</span></li>
				<li class="selected"><a href="/admin/prodotti.html">PRODOTTI</a></li>
				<li><a href="/admin/categorie.html">CATEGORIE</a></li>
			</ul>
		</div>
	</div>
	
	<div id="wbAppBody">
		
		
		
		<?php $this->printBlock( 'body' ); ?>
	</div>
		<?php $this->printBlock('wb-xray'); ?>
	<!-- <div id="wb-modal-container" class="wb-modal-container" style="display:none;"></div> -->
</body>
	
</html>
