<?php
  header('Access-Control-Allow-Origin: http://192.168.178.27');
  header('Access-Control-Allow-Methods: GET, POST, PUT');
  header('Access-Control-Allow-Headers: Content-Type');
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  header('Content-Type: application/javascript');
?>

<?php
    echo "//Ci sono ".count($this->getData('respeso',null))." barre peso\r\n";
    echo "var bckpeso = [";
    //echo "{ type: 5, id: 3129, bar:'2100095000000', description:'Reparto Latticini', department: 1,},";//aggiunto barre bilancia vecchia
    //echo "{ type: 5, id: 6713, bar:'2100085000000', description:'Reparto Salumi', department: 2,},";//aggiunto barre bilancia vecchia
    foreach ($this->getData('respeso',null) as &$row) {
      //rimuovo single quote , double quote, caratteri new line
        echo "{ type: 2, id: ".$row['id'].", bar:'".$row['bar']."', Pricekg:".$row['pricekg'].", description:'".str_replace("\r","",str_replace("\n","",str_replace('"','',str_replace("'","",$row['description']))))."', department: ".$row['department'].",},\r\n";
    }
    echo "];\r\n";
    unset($row); // break the reference with the last element
    ?>


<?php
    echo "//Ci sono ".count($this->getData('resbilancia',null))." barre bilancia\r\n";
    echo "var bckbilancia = [";
    //echo "{ type: 5, id: 3129, bar:'2100095000000', description:'Reparto Latticini', department: 1,},";//aggiunto barre bilancia vecchia
    //echo "{ type: 5, id: 6713, bar:'2100085000000', description:'Reparto Salumi', department: 2,},";//aggiunto barre bilancia vecchia
    foreach ($this->getData('resbilancia',null) as &$row) {
      //rimuovo single quote , double quote, caratteri new line
        echo "{ type: 5, id: ".$row['id'].", bar:'".$row['barcodebilancia']."', description:'".str_replace("\r","",str_replace("\n","",str_replace('"','',str_replace("'","",$row['description']))))."', department: ".$row['department'].",},\r\n";
    }
    echo "];\r\n";
    unset($row); // break the reference with the last element
    ?>

<?php
    echo "//Ci sono ".count($this->getData('ressimple',null))." barre\r\n";
    echo "var bck = [";
    foreach ($this->getData('ressimple',null) as &$row) {
      //rimuovo single quote , double quote, caratteri new line
        echo "{ type: 0, id: ".$row['id'].", bar:'".$row['bar']."', unitPrice:".$row['unitprice'].", description:'".str_replace("\r","",str_replace("\n","",str_replace('"','',str_replace("'","",$row['description']))))."', department: ".$row['department'].",},\r\n";
    }
    echo "];\r\n";
    unset($row); // break the reference with the last element
    ?>
