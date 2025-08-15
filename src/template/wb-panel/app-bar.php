<?php $this->startBlock('AppBar'); ?>
<div id="WBAppBar" class="wb-app-bar">
	<div class="body">
		
		<ul class="wb-toolbar left">
			<li id="WBAppBarNav" onclick="wb_toogle_sidenav('wbAppSideNav', 'left');">
				<!-- <img src="/imgs/icons/google-md/menu-black-48dp.svg"> -->
				<div class="wb-icon-64 wb-md-icon-black-menu"></div>
			</li>
			<li>
				<?php //echo $this->getData('WBAppBarTitle'); ?>
			</li>
			<!-- <li>
				<select>
					<option>Selezione 1</option>
					<option>Selezione 2</option>
					<option>Selezione 3</option>
				</select>
			</li> -->
		</ul>
	</div>
</div>
<div class="wb-app-bar-spacer"></div>

<?php $this->endBlock();?>

<?php
/* =========================================================
 * Sidenav Left
*/
$this->startBlock('AppSideNavLeft'); 
?>

	<div id="wbAppSideNav" class="wb-app-sidenav left ">
		<div class="header">
			<ul class="wb-toolbar right">
				<li id="WBAppBarNav" onclick="wb_AppBarNavClose('wbAppSideNav');">
					<img src="/img/icons/scalable/ic_clear_black_48px.svg">
				</li>
			</ul>
		</div>
		<div class="body">
			<section>
				<ul class="wb-sidenav-list" style="">
					
					<li style="">
						<a href="/wb-panel/forms/GenerateModal">
						<!-- <img src="/imgs/icons/outline-add-black-24px.svg"> -->
							<span class="wb-icon-64 wb-md-icon-black-speakerNotes"></span>
							
							<span>Form builder</span>
						</a>
					</li>
					<li style="">
						<img src="/imgs/icons/print.svg">
						<span>Stampa</span>
					</li>
					<li><img src="/imgs/icons/outline-delete_outline-24px.svg">
						<span>Cestino</span>
					</li>
					<li>
						<img src="/imgs/icons/outline-create_new_folder-24px.svg" onclick="modalAnagraficaNuovoGruppo();">
						<span>Nuovo gruppo</span>
					</li>
					<li>
						<img src="/imgs/icons/outline-folder-24px.svg" onclick="modalAnagraficaNuovoGruppoJoin();">
						<span>Fornitori</span>
					</li>
					<li>
						<img src="/imgs/icons/outline-folder-24px.svg">
						<span>Clienti</span>
					</li>
					<li>
						<img src="/imgs/icons/outline-settings-24px.svg">
						<span>Impostazioni</span>
					</li>
				</ul>
			</section>
		</div>
	</div>



<?php $this->endBlock(); ?>