<?php $this->startBlock('head'); ?>

<title><?php echo $this->getData('PageTitle', 'Page Title'); ?></title>


<style>
body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 12pt "Tahoma";
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }
    .page {
        width: 210mm;/*width: 246mm;*/
        min-height: 297mm;/*min-height: 326mm;*/
        /* adding: 13mm;*//*padding: 13mm;*/
        padding: 0 13mm 13mm 13mm;
        margin: 10mm auto;
        /*border: 1px #D3D3D3 solid;*/
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .testata {
      height: 13mm;
      background-color: red;
    }
    .subpage {
        /*border: 5px red solid;*/
        height: 271mm;/*height: 300mm;*/
        /*outline: 13mm #FFEAEA solid;*/
    }

    @page {
        size: A4;
        margin: 0;
    }
    @media print {
        html, body {
            width: 210mm;/*width: 246mm;*/
            height: 297mm;/*height: 326mm;*/
        }
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }

    .lateralpage {
      display: inline-block;
      width: 5%;
      height: 100%;
      text-align: center;
      /*border: 0.5px solid blue;*/
    }

    .categorypage {
      writing-mode: vertical-lr;
      height : 20mm;
      margin-top: 20mm;
    }

    .reverse {
      transform: rotate(-180deg);
    }

    .numberpage {
      background-color: #ddd;
      border-radius: 25px;
      height : 10mm;
      margin-top: 180mm;
      width: 100%;
      overflow: hidden;
      text-align: center;
      padding: 2mm 0;
      border: 1px solid black;
    }

    .internalpage {
      display: inline-block;
      width: 95%;
      height: 100%;
      /*border-bottom: 0.5px solid blue;*/
    }

    .RigaFoto {
      text-align: center;
      width: 100%;
      height: 40mm;
      float: left;
      margin-bottom: 3mm;
    }

    .RigaFoto img {
      width: 25%;
      height: 40mm;
      object-fit: contain;
    }

    .RigaFotoIntera {
      width: 100%;
      height: 40mm;
      float: left;
      margin-bottom: 0mm;
    }

    .RigaFotoIntera img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .RigaGriglia {
      border-collapse: collapse;
      width: 100%;
      float: left;
      font-size: 16px;
    }

    .RigaGriglia td, .RigaGriglia th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    .RigaGriglia tr:nth-child(even){background-color: #f2f2f2;}

    .RigaGriglia tr:hover {background-color: #ddd;}

    .RigaGriglia th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #87CEFA;
      color: white;
    }

    .Spazio {
      width: 100%;
      height: 10mm;
      float: left;
    }

    .Titolo {
      text-align: left;
      margin-top: 5mm;
      width: 100%;
      height: 10mm;
      float: left;
      overflow: hidden;
      /*border-bottom: 1px solid black;*/
      font-size: 26px;
      font-weight: 600;
    }

    .SottoTitolo {
      text-align: left;
      margin-bottom: 5mm;
      width: 100%;
      height: 10mm;
      float: left;
      overflow: hidden;
      border-bottom: 1px solid black;
      font-size: 16px;
      font-weight: 100;
    }

    .Titolo2 {
      text-align: left;
      margin-top: 5mm;
      margin-bottom: 5mm;
      width: 100%;
      height: 10mm;
      float: left;
      overflow: hidden;
      /*border-bottom: 1px solid black;*/
      font-size: 16px;
      font-weight: 700;
    }

    .Testo {
      text-align: left;
      margin-top: 5mm;
      margin-bottom: 5mm;
      width: 100%;
      height: 10mm;
      float: left;
      overflow: hidden;
      /*border-bottom: 1px solid black;*/
      font-size: 16px;
      font-weight: 100;
    }

</style>

<?php $this->endBlock(); ?>
