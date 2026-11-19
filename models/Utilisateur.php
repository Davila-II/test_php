<?php 

	class Utilisateur extends BaseModel{
		
		function __construct(){
			# code...
		}
		public static function getUserByToken($token){
			$access_token = Access_token::q()->filter(array("TOKEN"=>$token))->execute()[0];
			if(count($access_token)==0)
				return null;
			else{
				return Utilisateur::q()->filter(array("ID_UTILISATEUR"=>$access_token->ID_UTILISATEUR))->execute()[0];
			}
		}
		public static function login(){
			global $BD;	

 			$result = array();

			$sql = $BD->prepare("SELECT * FROM utilisateur WHERE (EMAIL = ? OR NUMERO = ?) AND PASSWORD =  ?");
			$sql->execute(array($_REQUEST['email'],$_REQUEST['email'], $_REQUEST['password']));
			if($sql->rowCount()>0){
				$result['status'] = "success";
				$result['login'] = "success";
				$res = array();
				$res = $sql->fetch();

				$check = $BD->prepare("SELECT * FROM access_token WHERE ID_UTILISATEUR = ?");
				$check->execute(array($res['ID_UTILISATEUR']));
				if($check->rowCount()==0){
					$sql2 = $BD->prepare("INSERT INTO `access_token`(`ID_UTILISATEUR`, `TOKEN`) VALUES (?,?)");
					$token = getToken(100);
					$sql2->execute(array($res['ID_UTILISATEUR'], $token));
					$result['token'] = $token;
				}
				else {
					$token = $check->fetch();
					$result['token'] = $token['TOKEN'];
				}
				
	    	  	$output[]=array_map("utf8_encode", $res);
				//echo json_encode($output);
				$result['utilisateur'] = $output;

			}
			else {
				$result['status'] = 'success';
				$result['login'] = "error";	
			}

			return $result;
		}

		public static function register(){
			global $BD;	
			$result = array();
			if(isset($_REQUEST['email']) && isset($_REQUEST['password'])){

				$sql = $BD->prepare("SELECT * FROM utilisateur WHERE EMAIL=? OR NUMERO = ?");
				$sql->execute(array($_REQUEST['email'], $_REQUEST['telephone']));
				if($sql->rowCount()>0){
					$result['status'] = 'success';
					$result['register'] = "error";	

				}
				else {
					$result['status'] = "success";
					$result['register'] = "success";

					$req = $BD->query("SELECT MAX(ID_UTILISATEUR) as m FROM utilisateur");
			        $m = 0;
			        while ($row=$req->fetch()) {
			            if($row['m']==null)
			                $m = 0;
			            else
			                $m = $row['m'];
			        }
			        $m = $m + 1;

			        if(isset($_REQUEST['image']) && strlen($_REQUEST['image'])>0){
			            $image = "utilisateur".$m.".jpg";
			        }
			        else
			            $image = "";

			        $ins = $BD->prepare("INSERT INTO `utilisateur`(`ID_UTILISATEUR`, `NOM`, `PRENOM`, `EMAIL`, `NUMERO`, `PHOTO_PROFILE`, `ADRESSE`, `PASSWORD`) VALUES (?,?,?,?,?,?,?,?)");
			        $ins->execute(array($m, utf8_decode($_REQUEST['nom']), utf8_decode($_REQUEST['prenom']), utf8_decode($_REQUEST['email']), utf8_decode($_REQUEST['telephone']),$image, utf8_decode($_REQUEST['adresse']), utf8_decode($_REQUEST['password'])));


			         if(isset($_REQUEST['image']) && strlen($_REQUEST['image'])>0){
			            // Get image string posted from Android App
			            $base=$_REQUEST['image'];
			        	// Get file name posted from Android App
			        	$filename = $image;
			        	// Decode Image
			            $binary=base64_decode($base);
			            header('Content-Type: bitmap; charset=utf-8');
			        	// Images will be saved under 'www/imgupload/uplodedimages' folder
			            $file = fopen('images/utilisateur/'.$filename, 'wb');
			        	// Create File
			            fwrite($file, $binary);
			            fclose($file);
			        }

			        $get = $BD->query("SELECT * FROM utilisateur WHERE ID_UTILISATEUR = ".$m);
					$res = array();
					$res = $get->fetch();

					$sql2 = $BD->prepare("INSERT INTO `access_token`(`ID_UTILISATEUR`, `TOKEN`) VALUES (?,?)");
					$token = getToken(100);
					$sql2->execute(array($res['ID_UTILISATEUR'], $token));
					$result['token'] = $token;
		    	  	$output[]=array_map("utf8_encode", $res);
					//echo json_encode($output);
					$result['utilisateur'] = $output;

				}

		 	}
		 	else {
				$result['status'] = 'error';
				$result['reason'] = "Paramètres incorrects";
		 	} 	

		 	return $result;
		}
	}
?>