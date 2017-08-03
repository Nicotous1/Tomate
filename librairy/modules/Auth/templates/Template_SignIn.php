<?php include("librairy/templates/Template_Header.php"); ?>



    <div  class="md-whiteframe-z2" style="padding: 0;" ng-controller="EditController">
    <md-toolbar>
      <div class="md-toolbar-tools">
        <span>Portail de connexion</span>
      </div>
    </md-toolbar>

      <md-content>

        <md-tabs md-dynamic-height md-border-bottom >


	        <md-tab label="Se connecter" ng-disabled="sending">

				<form method="POST" ng-submit="signIn()">
					<md-content class="md-padding">
						<md-input-container class="md-block flex">
						  <label>Mail de l'Ensae</label>
						  <input type="text" ng-model="etudiant.mail"  class="mail_inputs" required md-maxlength="130" ng-change="updateMail()">
						</md-input-container>

						<md-input-container class="md-block flex">
						  <label>Mot de passe</label>
						  <input type="password" ng-model="etudiant.password" required md-maxlength="72">
						</md-input-container>

						<div>
							<md-button type="submit" ng-disabled="sending">{{sending ? 'Connexion en cours...' : 'Se connecter'}}</md-button>	
						</div>
					</md-content>
				</form>

	        </md-tab>


	        <md-tab label="S'inscrire" ng-disabled="sending">

				<form ng-submit="register()">
					<md-content class="md-padding">

						<md-subheader>Identifiants de connexion :</md-subheader>
						<div layout="row">
							<md-input-container class="md-block flex">
							  <label>Mail de l'Ensae</label>
							  <input type="text" ng-model="etudiant.mail" md-maxlength="130" required ng-change="updateMail()">
							</md-input-container>

							<md-input-container class="md-block flex">
							  <label>Mot de passe</label>
							  <input type="password" ng-model="etudiant.password" md-maxlength="72" required>
							</md-input-container>  
						</div>
						<md-subheader>Quelques informations</md-subheader>
						<div layout="row"  class="md-block">
			                <md-input-container>
			                  <md-select ng-model="etudiant.titre" placeholder="Titre" required>  
			                    <md-option ng-value="t.id" ng-repeat="t in formEtudiant.titres track by t.id">{{t.titre}}</md-option>
			                  </md-select>
			                </md-input-container>
							<md-input-container class="md-block flex">
							  <label>Nom</label>
							  <input type="text" ng-model="etudiant.nom" md-maxlength="40"  required>
							</md-input-container>
							<md-input-container class="md-block flex">
							  <label>Prénom</label>
							  <input type="text" ng-model="etudiant.prenom" md-maxlength="40"  required>
							</md-input-container>

			                <md-input-container>
				                <md-select ng-model="etudiant.annee" placeholder="Classe" required>  
				                  <md-option ng-value="annee.id" ng-repeat="annee in formEtudiant.annees track by annee.id">{{annee.name}}</md-option>
				                </md-select>
			                </md-input-container>	
						</div>
						<div layout-align="center center">
							<md-button type="submit" ng-disabled="sending">{{sending ? 'Inscription en cours...' : 'S\'inscrire'}}</md-button>		
						</div>
					</md-content>
				</form>

	        </md-tab>



        </md-tabs>    
        <md-divider></md-divider>  
      </md-content>
      </div>
    </div>

<?php include("librairy/templates/Template_Footer_1.php"); ?>

<script type="text/javascript">
	    app.controller('EditController', function($scope, $http, $mdToast) {

	    	$scope.etudiant = {};
	    	$scope.sending = false;

	    	$scope.formEtudiant = {
     			annees : <?php echo json_encode(User::$anneeArray); ?>,
     			titres : <?php echo json_encode(User::$titreArray); ?>,
	    	};

	    	function send(url) {
	    		if ($scope.etudiant.mail.indexOf("@") < 0)  {alert("Veuillez entrer une adresse mail valide !"); return;}

		        $scope.sending = true;
		        var resHandler = handle_response({
		          success : function(data, msg) {
								window.location = data.url;
		                    },
		           failed : function(data, msg) {
		           		alert(msg);
		           		$scope.sending = false;
		           },
		        });
		        $http.post(url, $scope.etudiant).then(resHandler, resHandler);
	    	}

	    	$scope.register = function() {
	    		send("<?php echo $routeur->getUrlFor("AuthRegister") ?>");
	    	};

	    	$scope.signIn = function() {
	    		send("<?php echo $routeur->getUrlFor("AuthSignIn") ?>");
	    	};

			$scope.updateMail = function() {
				var end = "@ensae.fr";
				var mail = $scope.etudiant.mail;
				var i = mail.indexOf("@");
				if (i < 0) {return;}
				if (i == 0) {$scope.etudiant.mail = ""; return;}
				var m = mail.substr(0,i); //Identifiants du mail
				var l = (mail.length - i) 
				if (l == 1 || l >= end.length) { //Juste un @
					m += end;
				}
				$scope.etudiant.mail = m;
			};

	    });
</script>

<?php include("librairy/templates/Template_Footer_2.php"); ?>