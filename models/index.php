<?php
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
    header("Cache-Control: post-check=0, pre-check=0", false); 
    header("Cache-Control: private");
    header("Pragma: no-cache");
    header('Content-Type:text/html; Charset=UTF-8');
	
	ob_start();

	session_start();

	define("BASEURL", "/");

	require("db.php");
	$query = array();
	$parts = parse_url($_SERVER["REQUEST_URI"]);
	if(isset($parts['query'])){
		parse_str($parts['query'], $query);
		if(isset($query) && count($query)>0){
			foreach ($query as $key => $value) {
				$_REQUEST[$key] = $value;
			}
		}
	}
	require "models/classes.php";

    $ctrl = "accueil"; // Page d'acceuil par défaut
	if(isset($_REQUEST['page']) ){
		$ctrl = $_REQUEST['page'];
	}

	if(!isset($_SESSION['lang'])){
		$_SESSION['lang'] = 'fr';
	}

	//require("langs/".$_SESSION['lang'].".php"); //Chargement du fichier langue

	require_once("fonctions.php");    // Chargement des fonction de base


    switch($ctrl){
        case 'accueil':{
            $page_title = "Accueil";
            include("views/header.php");
            include("views/menu.php");
            include("views/accueil.php");
            include("views/footer.php");
            break;
        }
        case 'cem':{
            $page_title = "C.E.M";
            include("views/header.php");
            include("views/menu.php");
            include("views/cem.php");
            include("views/footer.php");
            break;
        }
        case 'blog':{
            $action = isset($_GET['cible'])? trim($_GET['cible']) : "load";
            
            switch($action){
                case 'load':{
                    $page_title = "Blog";
                    include("views/header.php");
                    include("views/menu.php");
                    include("views/blog.php");
                    include("views/footer.php");
                    break;
                }

                case 'edit':{
                    $page_title = "Blog";
                    Action::Commenter();
                    header("Location: ../blog/".$_REQUEST["idelt"]);
                    break;
                }

                default : {
                    if(is_numeric($action)){
                        $_REQUEST['id'] = $action;
                        $page_title = "Détails du blog";
                        include("views/header.php");
                        include("views/menu.php");
                        include("views/blog_details.php");
                        include("views/footer.php");
                    }
                    break;
                }
   
                        
            }
            break;
            
        }

        case 'dlab':{
            $page_title = "D-LAB";
            include("views/header.php");
            include("views/menu.php");
            include("views/dlab.php");
            include("views/footer.php");
            break;
        }
        case 'contact':{
            $page_title = "D-LAB";
            include("views/header.php");
            include("views/menu.php");
            include("views/contact.php");
            include("views/footer.php");
            break;
        }
        case 'newsletter':{
            $page_title = "News letter";
            include("models/newsletter.php");
            break;
        }
        case 'reservation':{
            $page_title = "News letter";
            Reservation::addReservation();
            break;
        }
        case 'rendezvous':{
            $page_title = "Rendez vous";
            Rdv::addRdv();
            break;
        }
        case 'mail':{
            $page_title = "News letter";
            include("models/contact.php");
            break;
        }
        case 'Apk':{
            include("controller/ApplicationController.php");
            break;
        }
        default:{
            header("Location: ".getBaseUrl()."accueil");
            /*header('location:../views/error.php');*/
        }
    }
	ob_end_flush();
?>