<?php $this->startBlock('body') ?>
<div id="dettaglioarticolo">
  <div class="bannerrettangolo"><div class="testo" style="font-family: serif;"><?php echo $this->getData('exceptionname', 'ECCEZZIONE');	?></div></div>
</div>

<div id="prodotti" style="text-align: center;">
	<?php echo $this->getData('exceptionmessage', '0');	?>
</div>
<div onclick="window.history.back();" style="border: 2px solid #676767; border-radius: 32px;padding: 16px 32px 16px 32px;text-align: center;color: #676767;font-weight: 800;cursor:pointer;margin-left: auto;margin-right: auto;max-width: 200px;margin-bottom: 10px;margin-top: 10px;">INDIETRO</div>
<?php $this->endBlock() ?>
