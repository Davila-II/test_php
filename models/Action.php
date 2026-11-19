<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18/01/2018
 * Time: 06:47
 */
class Action extends BaseModel{


    /**
     * Action constructor.
     */
    public function __construct()
    {

    }

    public static function Commenter()
    {
    	global $BD;

    	$insert = $BD->prepare("INSERT INTO `action`(`ID_BLOG`, `NOM`, `EMAIL`, `TYPE_ACTION`, `DETAILS_ACTION`) VALUES (?,?,?,?,?)");
    	$insert->execute(array($_REQUEST["idelt"],utf8_decode($_REQUEST["nom"]),$_REQUEST["email"],'commentaire',utf8_decode($_REQUEST["details"])));
    }
}