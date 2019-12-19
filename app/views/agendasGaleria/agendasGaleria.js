/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| Kevin Ramírez
| - Controllador de la Vista de agendasGaleria
|
*/

var app = angular.module('agendasGaleria', ['angularFileUpload','as.sortable']);

// Controller
app.controller( 'agendasGaleriaController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$uploader', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $uploader, $http ){

    // Scope Variables
    $scope.agenda           = {};
    $scope.agenda_imagenes  = [];
    $scope.temporal         = [];

    $scope.editar = { status: false, id: null, imgShow: false };
    $scope.titulo = { plural: "galería", singular: "galería", icono: "ti-layers-alt" };

    var uploader = $uploader.load({
        url : 'api/agendas/upload',
        autoUpload: true,
        headers: {
            Authorization: localStorage.getItem('gjb.token')
        }
    });
    $scope.uploader = uploader;

    uploader.onCompleteItem = function(item, response, status, headers){
        $scope.temporal.push({
            'vc_imagen' : response.nombre
        });
    };

    $scope.regresar = function() {
        $state.go('agendas');
    };

    $scope.deleteItem = function(item, posicion){
        item.remove();
        $scope.temporal.splice( posicion, 1 );
    };

    $scope.acciones = {
        dragEnd : function(){
        //Actualizamos en orden del slider
        var params = { agenda_imagenes : [] };

        angular.forEach( $scope.agenda_imagenes, function( item, index ){
            params.agenda_imagenes.push({
                id : item.id,
                nu_orden : index + 1
            });
        });

        $loading.show();

        $http.post('api/agendas/imagenes/' + $stateParams.id + '/ordenar', params)
            .success(function (res) {
              $message.success(res.texto);
            })
            .error(function(error){
                $message.warning(error.texto);
                $scope.init();
            })
            .finally(function(){
                $loading.hide();
            })
        }
    }

    $scope.guardar = function(){

        if($scope.temporal.length == 0){
            $message.warning('No se ha cargado ninguna imagen para guardar en los agendas.');
            return;
        }

        if( !$validate.form('form-validate') )
            return;

      $loading.show();

      var params    = {};
      var agenda_imagenes  = angular.copy($scope.agenda_imagenes);

      //Agregamos el nombre de la imagen
      params.agenda_imagenes = [];

      angular.forEach($scope.temporal, function(item){
          params.agenda_imagenes.push(item.vc_imagen);
      });

      $http.post('api/agendas/'+$stateParams.id+'/imagenes/store', params)
        .success(function (res) {
          $message.success(res.texto);

          for(var agendas_imagen in params.agenda_imagenes){
            $scope.temporal.pop(params.agenda_imagenes[agendas_imagen]);
          }

          uploader.queue = [];
          $scope.init();
        })
        .error(function (error) {
          if(error.texto){
              $message.warning(error.texto);
          } else {
              $message.warning('La imagen, no se pudo agregar correctamente. #LOT03');
          }
        })
        .finally(function(){
            $loading.hide();
        });

    };

    $scope.eliminar = function( agenda_imagenes , index) {
        $message.confirm({
            text    : '¿Estás seguro de eliminar la <b>imágen</b> ?',
            callback : function( msg ){
                $loading.show();

                $http.delete('api/agendas/'+agenda_imagenes.id+'/imagenes')
                    .success(function(res){
                        $scope.agenda_imagenes.splice( index, 1 );
                        $message.success(res.texto);
                        msg.close();
                        $scope.init();
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('La imagen, no se pudo eliminar correctamente. #LO02');
                        }
                    })
                    .finally(function(){
                        $loading.hide();
                    });
            }
        });
    };

    $scope.init = function(){
        // Definir Modelo
        ModelService.addModel('agendas');

        // // Verificar Estatus Agregar o Editar
        // $util.stateParams(function(){
        //     $loading.show();
        //     // $scope.subtitleView = "EDITAR";
        //     $scope.editar.status = true;
        //     $scope.editar.imgShow = true;
        //
        //     $http.get('api/agendas/'+$stateParams.id+'/imagenes')
        //         .success(function(res){
        //             $scope.agenda           = res;
        //             $scope.agenda_imagenes  = res.agenda_imagenes;
        //             uploader.queueLimit   = 8 - res.agenda_imagenes.length;
        //         })
        //         .error(function (error) {
        //             if(error.texto){
        //                 $message.warning(error.texto);
        //             } else {
        //                 $message.warning("No se pudo obtener el registro. #LOT01");
        //             }
        //         })
        //         .finally(function(){
        //             $loading.hide();
        //         });
        // });
    };

// Begin Module
    $scope.init();

}]);
