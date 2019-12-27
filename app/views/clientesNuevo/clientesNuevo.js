/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de clientesNuevo
|
*/

var app = angular.module('clientesNuevo', ['angularFileUpload','as.sortable']);

// Controller
app.controller( 'clientesNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$uploader', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $uploader, $http ){

// Scope Variables
    $scope.cliente  = { vc_nombre: "", vc_apellido: "", nu_telefono:"" };
    $scope.generos  = [];
    $scope.roles    = [];
    $scope.flags    = { editar: false };

// Scope Functions
    var uploader    = $uploader.load({
        url : 'api/clientes/upload',
        autoUpload: true,
        headers: {
            Authorization: localStorage.getItem('gc.token')
        }
    });

  

    $scope.deleteItem = function(item){
      ModelService.custom('delete', 'api/clientes/eliminarImagen/' + $scope.cliente.vc_imagen )
        .success( function(res){
          $scope.cliente.vc_imagen    = '';
          $scope.cliente.vc_imagenUrl = '';
          item.remove();
          $message.success(res.texto);
        })
        .error(function (error) {
            if(error.texto){
                $message.warning(error.texto);
            } else {
                $message.warning('La imagen no se pudo eliminar correctamente.');
            }
        })
        .finally(function(){
            $loading.hide();
        });
    };

    $scope.eliminar = function() {
        $scope.cliente.vc_imagen    = '';
        $scope.cliente.vc_imagenUrl = '';
    };

    $scope.regresar = function() {
        $state.go('clientes');
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;
        

        $loading.show();

        if( !$scope.flags.editar ) {
            ModelService.add($scope.cliente.detalle)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('clientes');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El cliente '+$scope.cliente.vc_nombre+', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else {
            ModelService.update($scope.cliente.detalle)
                .success(function (res) {
                    $message.success(res.texto);
                    $loading.hide();
                    $state.go('clientes');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El cliente '+$scope.cliente.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){

        // Definir Modelo
        ModelService.addModel('clientes');

        $loading.show();

        ModelService.create()
            .success(function(res){

                $scope.generos  = angular.copy(res.generos);
                $scope.roles    = angular.copy(res.roles);

                // Verificar proceso Agregar o Editar
                $util.stateParams(function(){

                    $scope.flags.editar = true;


                    ModelService.edit( $stateParams.id )
                        .success(function(res){
                            $scope.cliente                = res;
                            $scope.cliente.id_genero      = String($scope.cliente.id_genero);
                            $scope.cliente.id_rol         = String($scope.cliente.id_rol);
                            $scope.cliente.vc_password_re = angular.copy($scope.cliente.vc_password);
                        })
                        .error(function (error) {
                            if(error.texto){
                                $message.warning(error.texto);
                            } else {
                                $message.warning("No se pudo obtener el registro.");
                            }
                        })
                        .finally(function(){
                            $loading.hide();
                        });
                });
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudo obtener el registro.");
                }
            })
            .finally(function(){
                $loading.hide();
            });
    };

// Begin Module
    $scope.init();

}]);
