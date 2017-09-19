<?php
	namespace Admin;
	
	use Core\Controller;
	use Core\PDO\Request;

	use \Exception;	

	use Admin\Entity\Etude;
	use Admin\Entity\Com;

	class AjaxController extends Controller {

/*

	Fonctions Etude
	Toutes les fonctions de sauvegarde liées à une étude

*/
		public function LastEtudes() {
			$page = (int) $this->httpRequest->post("page");
			$page_size = (int) $this->httpRequest->post("page_size");

			$offset = $page*$page_size; // pas de +1 car commence à zero

			$res = $this->pdo->get("Admin\Entity\Etude", array(
				"#s.child IS NULL ORDER BY #s.numero DESC LIMIT :0 OFFSET :1",
				array($page_size, $offset)
			), false);

			$r = new Request("SELECT COUNT(*) AS n FROM #^ WHERE #child IS NULL", Etude::getEntitySQL());
			$n = $r->fetch()["n"];

			return $this->success(array("etudes" => $res, "n" => $n));
		}


		public function SaveCom() {
			$content = $this->httpRequest->post("content");
			$etude_id = (int) $this->httpRequest->post("etude_id");


			$e = $this->pdo->get("Admin\Entity\Etude", $etude_id);
			if ($e == null) {return $this->error("L'étude que vous souhaitez commenter a été supprimée !");}

			$com = new Com(array("content" => $content, "etude" => $e));
			$res = $this->pdo->save($com);
			if (!$res) {$this->error("Une erreur s'est produite lors de la sauvegarde votre commentaire.");}

			return $this->success(array("com" => $com));
		}

	}
?>