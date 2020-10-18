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
    	
    	$banks=$mapper->query("select distinct cuenta from getTweetData")->toArray();
        
        foreach ($banks as $key => $banco) {
            $auxbanco=[];
            $data=$mapper->query("select * from getTweetData where cuenta='".$banco["cuenta"]."'")->toArray();    
            //$auxbanco[$banco["cuenta"]]=[];
            foreach ($data as $key => $value) {
                $aux=[];
                $aux["promedioRT"]=$value["avg_rt"];
                $aux["promedioFV"]=$value["avg_fv"];
                $aux["maxRT"]=$value["max_rt"];
                $aux["maxFV"]=$value["max_fv"];
                $aux["fecha"]=$value["fecha"];
                array_push($auxbanco,$aux);
            }
            $array=array_merge($array, [$banco["cuenta"]=>$auxbanco]);
        }

        
        header('Content-Type: application/json');
    	echo json_encode($array);
    }



    public function getBancks($req,$res){
        $mapper=$this->spot->mapper("Entity\Tweet");
        $banks=$mapper->query("select distinct cuenta from getTweetData")->toArray();
        $auxbanco=[];
        foreach ($banks as $key => $banco) {
            array_push($auxbanco, $banco["cuenta"]);
        }
        header('Content-Type: application/json');
        echo json_encode($auxbanco);

    }
    /**
    * Get WorstCOmments
    */
    public function getWorstComments($req,$res) {
        $mapper=$this->spot->mapper("Entity\Tweet");
        $auxbanco=[];
        $data=$mapper->query("select * from getWorstComments where vectorSentimiento<0 and cuenta='".$req->data["banco"]."' limit 10")->toArray();
        foreach ($data as $key => $value) {
            $aux=[];
            $aux["query"]=str_replace("@","",$value["cuenta"]);
            $aux["vectorSentimiento"]=$value["vectorSentimiento"];
            $aux["fecha"]=$value["fecha"];
            $aux["texto"]=utf8_decode($value["texto"]);
            $aux["rt"]=$value["rt"];
            $aux["fav"]=$value["fav"];
            array_push($auxbanco,$aux);
        }
        $res->m = $res->mustache->loadTemplate("utils/tabla.mustache");
        echo $this->renderView($res,["worstComments"=>$auxbanco,"banco"=>$req->data["banco"]]);
    }
    /**
    * Get BestComments
    */
    public function getBestComments($req,$res) {
        $array=[];
        $mapper=$this->spot->mapper("Entity\Tweet");
        $banks=$mapper->query("select distinct cuenta from getTweetData")->toArray();
        foreach ($banks as $key => $banco) {
            $auxbanco=[];
            $data=$mapper->query("select * from getWorstComments where vectorSentimiento>0 and cuenta='".$banco["cuenta"]."' order by vectorSentimiento desc,rt desc,fav desc limit 10")->toArray();
            foreach ($data as $key => $value) {
                $aux=[];
                $aux["query"]=str_replace("@","",$value["cuenta"]);
                $aux["vectorSentimiento"]=$value["vectorSentimiento"];
                $aux["fecha"]=$value["fecha"];
                $aux["texto"]=$value["texto"];
                $aux["rt"]=$value["rt"];
                $aux["fav"]=$value["fav"];
                array_push($auxbanco,$aux);
            }
            $array=array_merge($array, [$banco["cuenta"]=>$auxbanco]);

        }
        
        
        header('Content-Type: application/json');
        echo json_encode($array);
    }

    public function getSentimentOverview($req,$res){
        $array=[];
        $mapper=$this->spot->mapper("Entity\Tweet");
        $overView=$mapper->query("select * from getSentimentOverview")->toArray();
        foreach ($overView as $key => $value) {
            $aux=[];
            $aux["cuenta"]=$value["cuenta"];
            $aux["porcentage_neutral"]=($value["neutral"]*100)/$value["total"];
            $aux["porcentage_positivo"]=($value["positivo"]*100)/$value["total"];
            $aux["porcentage_negativo"]=($value["negativo"]*100)/$value["total"];
            array_push($array, $aux);
        }
        header('Content-Type: application/json');
        echo json_encode($array);
        
    }
}