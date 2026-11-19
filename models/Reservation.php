<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18/01/2018
 * Time: 06:47
 */
class Reservation extends BaseModel{


    /**
     * Action constructor.
     */
    public function __construct()
    {
    }

    public static function addReservation(){
    	global $BD;
    	$insert = $BD->prepare("INSERT INTO `reservation`(`NOM`, `EMAIL`, `ADRESSE`, `TEL`, `COMMENTAIRE`, `DATE_RESERVATION`, `HEURE_RESERVATION`) VALUES(?,?,?,?,?,?,?)");
			$insert->execute(array(utf8_encode($_REQUEST["nom"]),utf8_encode($_REQUEST["email"]),utf8_encode($_REQUEST["adresse"]),$_REQUEST["tel"],$_REQUEST["message"],date('Y-m-d',strtotime($_REQUEST["datereservation"])),date('h:i',strtotime($_REQUEST["heurereservation"]))));


			$stmt = $BD->query("SELECT LAST_INSERT_ID()");
			$lastId = $stmt->fetch(PDO::FETCH_NUM);
			$lastId = $lastId[0];


			/*for ($i=0; $i <count($_REQUEST['service']) ; $i++) { 
				$insert = $BD->prepare('INSERT INTO `reservation_service`(`ID_RESERVATION`, `ID_SERVICE`) VALUES (?,?)');
				$insert->execute(array($lastId, $_REQUEST['service'][$i]));
			}*/
			$insert = $BD->prepare('INSERT INTO `reservation_service`(`ID_RESERVATION`, `ID_SERVICE`) VALUES (?,?)');
			$insert->execute(array($lastId, $_REQUEST['service']));


			$result = array();/**/
			$to = 'bcombel@ymail.com';
			$subject = 'Reservation';

			$message = 'Le client <b>'.$_REQUEST['nom'].'</b> a pris une rendez-vous pour le(s) service(s) '.$lists.'. il a laissé le message suivant :<blockquote>'.$_REQUEST['message'].'</blockquote><br>Informations sur le client : '; 
			$message .= "<table>
							<tr>
								<td>Date et heure réservation : </td>
								<td><b>".$_REQUEST["datereservation"]." à ".$_REQUEST["heurereservation"]."</b></td>
							</tr>
							<tr>
								<td>Email : </td>
								<td><b>".$_REQUEST['email']."</b></td>
							</tr>
							<tr>
								<td>Tel : </td>
								<td><b>".$_REQUEST['tel']."</b></td>
							</tr>
							<tr>
								<td>Adresse : </td>
								<td><b>".$_REQUEST['adresse']."</b></td>
							</tr>
						</table>
						<br>";

				
			 $headers = 'From: Bcombel <contact@bcombel.com>'."\r\n";
				
			$headers .= 'Mime-Version: 1.0'."\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$headers .= "\r\n";

			// Sending email
			if(mail($to, $subject, $message, $headers)){
			    $result['status'] = 'success';
			} else{
			    $result['text'] = "Impossible d'envoyer le mail pour l'instant réessayer plus tard.";
			    $result['status'] = 'error';
			}

			//echo json_encode($result);
			//header('Location: accueil');
    }
}