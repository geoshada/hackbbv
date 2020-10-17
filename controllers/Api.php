<?php
/**
 * Controlador para paginas estaticas, por ejemplo el login
 * La seleccion de vista se hace de la siguiente forma  
 * $res->m = $res->mustache->loadTemplate("Carpeta/archivo.mustache");
 *
 * La obtencion de parametros de la siguiente manera:
 * $req->data["campo"] (Dessde el formulario)
 * $req->params["seccion"] (Desde la url)
 *    
 * Para pasar datos, poner un arreglo, de tal manera que:
 * echo $this->renderWiew(array_merge(["datos"=>datos]),$res);
 *
 * @author geoskull
 */
class Api extends Tornado\Controller{
	/**
	* Get MaxDataByDate
	*/
    public function getMaxDataByDay($req,$res) {
    	$array=[];
    	$mapper=$this->spot->mapper("Entity\Tweet");
    	$data=$mapper->query("select * from getTweetData")->toArray();
    	foreach ($data as $key => $value) {
    		$aux=[];
    		$aux["query"]=str_replace("@","",$value["cuenta"]);
    		$aux["promedioRT"]=$value["avg_rt"];
    		$aux["promedioFV"]=$value["avg_fv"];
    		$aux["maxRT"]=$value["max_rt"];
    		$aux["maxFV"]=$value["max_fv"];
    		$aux["fecha"]=$value["fecha"];
    		array_push($array,$aux);
    	}
    	echo json_encode($array);
    }
}