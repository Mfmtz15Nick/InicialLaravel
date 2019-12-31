/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
| - Controllador de la Vista de eventosGaleria
|
*/

var app = angular.module('eventosGaleria', ['angularFileUpload','as.sortable']);

// Controller
app.controller( 'eventosGaleriaController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$uploader', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $uploader, $http ){

    // Scope Variables
    $scope.evento = {};
    $scope.evento_imagenes = [];
    $scope.temporal = [];

    $scope.editar = { status: false, id: null, imgShow: false };
    $scope.titulo = { plural: "galería", singular: "galería", icono: "ti-layers-alt" };

    var uploader = $uploader.load({
        url : 'api/eventos/upload',
        autoUpload: true,
        headers: {
            Authorization: localStorage.getItem('gc.token')
        }
    });
    $scope.uploader = uploader;

    uploader.onCompleteItem = function(item, response, status, headers){
        $scope.temporal.push({
            'vc_imagen' : response.nombre
        });
    };

    $scope.regresar = function() {
        $state.go('eventos');
    };

    $scope.deleteItem = function(item, posicion){
        item.remove();
        $scope.temporal.splice( posicion, 1 );
    };

    $scope.acciones = {
        dragEnd : function(){
        //Actualizamos en orden del slider
        var params = { evento_imagenes : [] };

        angular.forEach( $scope.evento_imagenes, function( item, index ){
            params.evento_imagenes.push({
                id : item.id,
                nu_orden : index + 1
            });
        });

        $loading.show();

        $http.post('api/eventos/imagenes/' + $stateParams.id + '/ordenar', params)
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
            $message.warning('No se ha cargado ninguna imagen para guardar en los eventos.');
            return;
        }

        if( !$validate.form('form-validate') )
            return;

      $loading.show();

      var params    = {};
      var evento_imagenes  = angular.copy($scope.evento_imagenes);

      //Agregamos el nombre de la imagen
      params.evento_imagenes = [];

      angular.forEach($scope.temporal, function(item){
          params.evento_imagenes.push(item.vc_imagen);
      });

      $http.post('api/eventos/'+$stateParams.id+'/imagenes/store', params)
        .success(function (res) {
          $message.success(res.texto);

          for(var eventos_imagen in params.evento_imagenes){
            $scope.temporal.pop(params.evento_imagenes[eventos_imagen]);
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

    $scope.eliminar = function( evento_imagenes , index) {
        $message.confirm({
            text    : '¿Estás seguro de eliminar la <b>imágen</b> ?',
            callback : function( msg ){
                $loading.show();

                $http.delete('api/eventos/'+evento_imagenes.id+'/imagenes')
                    .success(function(res){
                        $scope.evento_imagenes.splice( index, 1 );
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
        ModelService.addModel('eventos');

        // Verificar Estatus Agregar o Editar
        $util.stateParams(function(){
            $loading.show();
            // $scope.subtitleView = "EDITAR";
            $scope.editar.status = true;
            $scope.editar.imgShow = true;

            $http.get('api/eventos/'+$stateParams.id+'/imagenes')
                .success(function(res){
                    $scope.evento           = res;
                    $scope.evento_imagenes  = res.imagenes;
                    uploader.queueLimit   = 8 - res.imagenes.length;
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning("No se pudo obtener el registro. #LOT01");
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        });
    };

// Begin Module
    $scope.init();

}]);
