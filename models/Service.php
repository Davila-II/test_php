<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18/01/2018
 * Time: 06:47
 */
class Service extends BaseModel{

    public function __construct(){
    }

    public static function allServices(){
    	global $BD;
    	$sql = $BD->query("SELECT * FROM service, categorie WHERE service.ID_CATEGORIE = IF(categorie.ID_CATEGORIE=5, '6,7,8', categorie.ID_CATEGORIE) AND categorie.ID_SCATEGORIE = 0  AND categorie.NOM_CATEGORIE != 'C.E.M'");
    	$result = array();
    	while ($res = $sql->fetch()) {
    		$output[]=array_map("utf8_encode", $res);
    	}
    	$result['status'] = 'success';
    	$result['services'] = $output;
    	return $result;
    }
}