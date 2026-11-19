<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18/01/2018
 * Time: 06:47
 */
class Blog extends BaseModel{


    /**
     * Action constructor.
     */
    public function __construct()
    {
    }

    public function NombreCommentaire(){

    	global $BD;

    	$getcommentaire = $BD->query("SELECT count(*) as nb from action WHERE action.ID_BLOG = ".$this->ID_BLOG." AND action.TYPE_ACTION='commentaire'");
    	$sqld = $getcommentaire->fetch();

    	return $sqld["nb"];
    }
}