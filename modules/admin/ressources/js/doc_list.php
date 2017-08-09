<?php
  use Admin\Entity\DocTemplate; 
  use Admin\Entity\DocType; 
  use Admin\Entity\VarQuali; 
?>

<script type="text/javascript">
  app.controller('DocListController', function($scope, $http, $mdDialog, FileUploader) {
    $scope.doctypes = <?php echo json_encode($doc_types); ?>;
    $scope.templates = <?php echo json_encode($templates); ?>;
    $scope.vars_quali = <?php echo json_encode($vars_quali); ?>;
  });



  app.controller('TemplatesController', function($scope, $http, $mdDialog, FileUploader) {

    function add(e) {
      if (e == null || !(e.id > 0)) {return;}
      var done = false;
      angular.forEach($scope.templates, function(x,i) {
        if (!done && x.id == e.id) {done = true; $scope.templates[i] = e;}
      });
      if (!done) {$scope.templates.push(e);}
    }

    $scope.edit = function(template = null, $event) {
      if (template === null) {template = <?php echo json_encode(new DocTemplate()); ?>;}
      var backup = jQuery.extend({}, template);


       var parentEl = angular.element(document.body);
       $mdDialog.show({
         parent: parentEl,
         targetEvent: $event,
         templateUrl: "<?php echo $routeur->getRessource("ressources/templates/Template_Template_New.html"); ?>",
         controller: TemplateDialogController,
         locals: {
           doctypes: $scope.doctypes,
           template: template,
         },
      }).then(function(e = null) {
        if (e === null) { e = backup; }
        add(e);
      });

      function TemplateDialogController(template, doctypes, $scope, $mdDialog, $http) {
        $scope.doctypes = doctypes;
        $scope.template = template;
        $scope.sending = false;

        var url = "<?php echo $routeur->getUrlFor("AjaxSaveTemplate"); ?>";
        $scope.uploader = new FileUploader({
          url : url,
          alias : "template",
          formData : [$scope.template],
          
          onAfterAddingFile : function(item) {
            this.queue = [item];
          },

          filters: [{
              name: 'word',
              // A user-defined filter
              fn: function(item) {
                  var name = item.name;
                  var ext = name.substr(name.lastIndexOf('.') + 1);
                  if (ext != "docx") {
                    alert("'" + name + "' n'est pas un fichier Word ! (.docx)"); return false;
                  }
                  return true;
              }
          }],

          onCompleteItem : function(item, response, status, headers) {
            console.log(response);
            if (response.res === true) {
              $mdDialog.hide(response.template);
            } else {
              alert("Une erreur s'est produite contactez votre DSI !");
            }
          },
        });          

        $scope.close = function() {
          $mdDialog.hide();
        }

        $scope.fileName = function() {
          if ($scope.uploader.queue.length > 0) {
            return $scope.uploader.queue[0].file.name;
          } else {
            if (template.doc === null) {return null} else {return template.doc.nom;}
          }
        }

        $scope.add = function() {
          $scope.sending = true;
          console.log("posting template....");
          console.log($scope.template);
          if ($scope.uploader.getNotUploadedItems().length > 0) {
            console.log("upload");
            $scope.uploader.uploadAll(); 
          } else {
            if (!($scope.template.id > 0)) {return;}
            console.log("post");
            $scope.sending = true;
            $http.post(url, $scope.template).success(function(data){
              console.log(data);
              $scope.sending = false;  
              if (data.res === true) {
                $mdDialog.hide(data.template);
              } else {
                alert("Une erreur s'est produite contactez votre DSI !");
              }
            });            

          }
        }
      }
    };
    
  });



  app.controller('DocTypesController', function($scope, $http, $mdDialog) {

    function add(e) {
      if (e == null || !(e.id > 0)) {return;}
      var done = false;
      angular.forEach($scope.doctypes, function(x,i) {
        if (!done && x.id == e.id) {done = true; $scope.doctypes[i] = e;}
      });
      if (!done) {$scope.doctypes.push(e);}
    }

    $scope.edit = function(doctype = null, $event) {
      if (doctype === null) {doctype = <?php echo json_encode(new DocType()); ?>;}
      var backup = jQuery.extend({}, doctype);


       var parentEl = angular.element(document.body);
       $mdDialog.show({
         parent: parentEl,
         targetEvent: $event,
         templateUrl: "<?php echo $routeur->getRessource("ressources/templates/Template_DocType_New.html"); ?>",
         controller: DocTypeDialogController,
         locals: {
           doctype: $scope.doctype
         },
      }).then(function(e = null) {
        if (e === null) { e = backup; }
        add(e);
      });

      function DocTypeDialogController($scope, $mdDialog, $http) {

        $scope.doctype = doctype;
        $scope.sending = false;

        $scope.close = function() {
          $mdDialog.hide();
        }

        $scope.add = function() {
          $scope.sending = true;
          console.log("posting doctype....");
          console.log($scope.doctype);
          var url = "<?php echo $routeur->getUrlFor("AjaxSaveDocType") ?>";

          $http.post(url, $scope.doctype).success(function(data){
            $scope.sending = false;
            if (data.res !== true) {alert("Une erreur s'est produite contactez votre DSI !"); return;}
            var e = data.doctype;
            $mdDialog.hide(e);
          });     
        }
      }
    };
  });



  app.controller('VarController', function($scope, $http, $mdDialog) {

    $scope.edit = function(var_quali = null, $event) {
      console.log(var_quali);
      if (var_quali === null) {var_quali = <?php echo json_encode(new VarQuali()); ?>;}
      var backup = jQuery.extend({}, var_quali);


       var parentEl = angular.element(document.body);
       $mdDialog.show({
         parent: parentEl,
         targetEvent: $event,
         templateUrl: "<?php echo $routeur->getRessource("ressources/templates/Template_VarQuali_New.html"); ?>",
         controller: DialogController,
         locals: {
           var_quali: var_quali,
         },
      }).then(function(e = null) {
        if (e === null) { e = backup; }
        add_entity(e, $scope.vars_quali);
      });

      function DialogController(var_quali, $scope) {

        $scope.var = var_quali;
        $scope.sending = false;

        $scope.close = function() {
          $mdDialog.hide();
        }

        $scope.save = function() {
          $scope.sending = true;
          console.log("posting var quali....");
          console.log($scope.var);
          var url = "<?php echo $routeur->getUrlFor("AjaxSaveVar") ?>";

          $http.post(url, $scope.var).success(function(data){
            $scope.sending = false;
            if (data.res !== true) {alert("Une erreur s'est produite contactez votre DSI !"); return;}
            var e = data.var;
            $mdDialog.hide(e);
          });
        }
      }
    };
    
  });
</script>