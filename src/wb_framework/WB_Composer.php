<?php

namespace App\php\wb_framework;

use \Exception as Exception;
/**
*
* Component contengono un solo blocco
*
*
* $composer = new Composer( template/test/skel.php);
*
* $composer->extend(root/header);
* $composer->extend(root/appBody);
* $composer->extend(root/appBody/header);
* $composer->extend(root/appBody/body);
*
*
* $composer->loadComponent();
*
* Special blocks:
* global_css
* global_javascript
*
* $this->startBlock( 'body' );
* $this->endBlock();
*/

class WB_Composer{

		/*
		* $structure['name']['file']
		*                   ['childs']
		*/
		private $views;
		private $blocks;

		private $head_file;


		private $runtime_block_name;
		private $runtime_block_type;
		private $cssBlock;
		private $jsBlock;
		private $data;
		private $logger = null;
		private $WB_ROOT_PATH = null;


	public function __construct( $skel_file, $head=null ) {
		// -> la riga sotto è stata sostituita con ***
    
		//*** questa riga qui (presa e modificata da global.php)
		$this->WB_ROOT_PATH = $_SERVER['DOCUMENT_ROOT'];
		//*** a qui
		$this->logger = $GLOBALS["WB_LOGGER"];

		$this->cssBlock = new WB_Block();
		$this->jsBlock = new WB_Block();
		$this->blocks = array();
		$this->views = array();
/*
		if( $head != null ){
			//echo 'doppio';
			$this->views['body']['file'] = $skel_file;
			$this->views['head']['file'] = $skel_file;
		}else{
			//echo 'singolo';
			$this->views['root']['file'] = $skel_file;
		}*/
		$this->views['root']['file'] = $skel_file;
		//$this->head = '';
		$this->runtime_block_name = null;
		$this->data = array();
		//$this->structure['_body'][]
	}


	public function extend( $full_name, $file ): void{
		//echo 'FULLNAME:'.$full_name;
		$full_name_tok = explode("/", $full_name);
		$depth = count($full_name_tok);
		if( $depth > 1){
			$name = $full_name_tok[$depth-1];
			//echo 'NAME:'.$name;
			$parent = substr($full_name, 0, -strlen($name)-1);
			//throw new Exception("FULLNAME:".$full_name."DEPTH:".$depth." NAME:".$name." PARENT:".$parent);
			//echo 'PARENT:'.$parent;
			//print_r( $this->structure);
			$this->addChild( $name, $file, $parent);
		}else{
			$this->addChild( $full_name, $file);
		}
		//print_r( $this->structure);
	}


	/*
	* Function: addChild
    * @param $name: Child name
    * @param $file: File path
    * @param $parent: parent to extend
    * Default:body
	*/
	public function addChild( $name, $file, $parent='root' ): void{
		$this->logger->log('wb_composer', 'Add child template '.$name.' to:'.$parent, 3);

		//echo 'Add Child template:'.$name.' to:'.$parent.'<br>';
		$full_name = $parent.'/'.$name;
		if( !array_key_exists($parent, $this->views) ){
			throw new Exception("Unable to add child ".$name." parent ".$parent." doesn't exists", 1);
		}
		if( !file_exists($file) ){
			throw new Exception("Unable to add child file $file doesn't exists", 1);
		}

		if( array_key_exists('childs', $this->views[ $parent ] ) ){
			$childs = &$this->views[ $parent ]['childs'];//=& assegnazione tipo puntatore

			if (in_array($full_name, $childs)){//se presente
				$key = array_search($full_name, $childs);//search tramite value
				unset($childs[$key]);//se già presente TOLGO la stringa vecchia in modo che sotto al riaggingo con $this->views[ $parent ]['childs'][]= $full_name;
			}

		}
		$this->views[ $parent ]['childs'][]= $full_name;//add element to array
		$this->views[ $full_name ] = array( 'file'=>$file,  'childs'=>array() );//sovrascrivo eventuali child con lo stesso nome

	}


	/*
	* Genera la lista dei file da renderizzare nel corretto ordine
	* viene richiamata prima di iniziare il rendering
	*/
	private function generate_render_list( $parent, &$render_list): void{//& passing a pointer to the variable
		/*if($render_list == null){
			$render_list = array();
		}*/
		$render_list[] = $parent;
		if( array_key_exists( $parent, $this->views) ){
			$childs = $this->views[ $parent ][ 'childs' ];
            if( ($childs != null) && (count($childs) > 0) ){
								//$this->logger->log('CONTA DEI CHILD', 'parent:'.$parent, 3);
                foreach($childs as $child){
                    $this->generate_render_list( $child, $render_list );
                    //$render_list = array_merge($render_list, $tmp);
                }
            }
		}


		//return $render_list;
	}

	public function render(){

		$render_list = array();
		$output = '';

		$this->generate_render_list('root/head', $render_list);

		$childs = $this->views['root'][ 'childs' ];
		$key = array_search('root/head', $childs);
		if (false !== $key) {
		    unset($childs[$key]);
		}

		foreach ($childs as $child_key=>$child_val) {
			$tmp = array();
			$this->generate_render_list($child_val, $render_list);
		}

		array_unshift( $render_list, 'root');//appende davanti a tutti gli elementi dell'array l'elemento 'root'

		//render list diventa qui la lista di tutti i nomi dei child preceduta da root esempio: array(root,root/head,root/app-bar,root/body);
		//$this->logger->log('RENDER_LIST', 'array: '.implode(',',$render_list), 3);

		$count = count($render_list)-1;
		while( $count > -1){

			$file = $this->views[ $render_list[$count] ]['file'];
			if( !file_exists($file) ){
				throw new Exception("Rendering composer: file".$file." not exists", 1);
			}
			$this->logger->log('wb_composer', 'Rendering '.$file, 3);

			//FACCIO l'include di tutti i flile dall'ULTIMO elemento di $render_list

			if($count == 0){
				ob_start();//Questa funzione attiverà il buffering dell'output. Mentre il buffering dell'output è attivo, nessun output viene inviato dallo script (diverso dalle intestazioni), invece l'output viene memorizzato in un buffer interno.

				$this->logger->log('wb_composer', 'INCLUDE with ob_get_contents() '.$file, 3);
				include $file;
				$output = ob_get_contents();//copio il contenuto del buffer
				ob_end_clean();
			}else{
				include $file;
				$this->logger->log('wb_composer', 'INCLUDE '.$file, 3);
			}
			$count--;
		}

		$output_head = '';
		if( $this->head_file != null){
			ob_start();

			include $this->head_file;
			$output_head = ob_get_contents();
			ob_end_clean();
		}


		if( $this->head_file != null){


		}

		//$this->cssBlock->printContent();
		return $output;



	}

	public function startBlock( $blockName, $type='html', $mergeMethod='append' ): void{
		$this->runtime_block_name = $blockName;
		$this->runtime_block_type = $type;

		if( $this->runtime_block_name == null ){
			throw new Exception("composer, start, Block layer not started", 1);
		}

		if(ob_get_level() > 1){
			throw new Exception( 'Bufferig already started');
			//echo 'Buffering already started';
		}

		if( !array_key_exists($blockName, $this->blocks) ){
			$this->blocks[$blockName] = new WB_Block();
		}
		$this->logger->log('wb_composer', 'start block:'.$blockName.' type:'.$type, 3);

		ob_start();
	}


	public function printBlock( $blockName ): void{
		if( array_key_exists($blockName, $this->blocks) ){
			$this->blocks[$blockName]->printContent();

			//$this->logger->log('wb_composer', 'PRINT block:'.$blockName.' type:'.$type, 3);

			if( strlen($this->blocks[$blockName]->getContent('css')) > 0 ){

				$this->logger->log('wb_composer', 'copy css', 3);

			}
			$this->cssBlock->append($this->blocks[$blockName]->getContent('css'));
			//$this->cssBlock->append('ma la vacca');

		}
	}


	public function printCssBlock(): void{
		$this->cssBlock->printContent();
		//echo 'azzo se stampo q:';
		$d = $this->cssBlock->getContent();
		//echo strlen ( $d );
		$this->logger->log('wb_composer', 'printing css block', 3);

	}

  public function printFile($file): void{
      if(file_exists($this->WB_ROOT_PATH.$file) ){
          echo file_get_contents($this->WB_ROOT_PATH.$file);
      }
  }

	public function endBlock( ): void{
		if( $this->runtime_block_name == null ){
            throw new Exception( 'Errore block never started');
		}

		$buffer = ob_get_contents();
		ob_end_clean();
		$this->blocks[$this->runtime_block_name]->append($buffer, $this->runtime_block_type);
		$this->logger->log('wb_composer', 'end block: (APPEND CONTENT)'.$this->runtime_block_name.' type:'.$this->runtime_block_type, 3);

		$this->runtime_block_name = null;
		$this->runtime_block_type = null;
	}


	public function getData( $name, $alt=null ){
		if( array_key_exists($name, $this->data) ){
			return $this->data[$name];
		}else{
			return $alt;
		}
	}

	public function setData( $name, $data ): void{
		$this->data[$name] = $data;
	}


}



class WB_Block{


	private $data;


	public function __construct(){
		$this->data = array();
		$this->data['html'] = '';
	}


	public function append( $data, $type='html'): void{
		if(array_key_exists($type, $this->data) ){
			$this->data[$type] .= $data;
		}else{
			$this->data[$type] = $data;
		}


	}


	public function getContent( $type='html'){
		if(array_key_exists($type, $this->data) ){
			return $this->data[$type];
		}else{
			return '';
		}
	}


	public function printContent( $type='html'): void{
		if(array_key_exists($type, $this->data) ){
			echo $this->data[$type];
		}else{
			echo 'stampo un bel niente';
		}
		//echo $this->getContent($type);
		//echo $this->data[$type];
	}


}

?>
