/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de usuariosNuevo
|
*/

var app = angular.module('usuariosNuevo', ['angularFileUpload','as.sortable']);

// Controller
app.controller( 'usuariosNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$uploader', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $uploader, $http ){

// Scope Variables
    $scope.usuario  = { id_genero: "", vc_nombre: "", vc_imagen: "", vc_imagenUrl:"" };
    $scope.generos  = [];
    $scope.roles    = [];
    $scope.flags    = { editar: false };

// Scope Functions
    var uploader    = $uploader.load({
        url : 'api/usuarios/upload',
        autoUpload: true,
        headers: {
            Authorization: localStorage.getItem('gc.token')
        }
    });

    $scope.uploader = uploader;

    uploader.onCompleteItem = function(item, response, status, headers){

        $scope.usuario.vc_imagen    = response.nombre;
        $scope.usuario.vc_imagenUrl = response.url + response.nombre;

        if( uploader.queue.length > 1 ){
          for (var i = 0; i < uploader.queue.length; i++) {
            if ( i == 0 ) {
              uploader.queue[i].remove();
            }
          }
        }
    };

    $scope.deleteItem = function(item){
      ModelService.custom('delete', 'api/usuarios/eliminarImagen/' + $scope.usuario.vc_imagen )
        .success( function(res){
          $scope.usuario.vc_imagen    = '';
          $scope.usuario.vc_imagenUrl = '';
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
        $scope.usuario.vc_imagen    = '';
        $scope.usuario.vc_imagenUrl = '';
    };

    $scope.regresar = function() {
        $state.go('usuarios');
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        if ( $scope.usuario.vc_password != $scope.usuario.vc_password_re ) {
            $message.warning('Las constraseñas proporcionadas no son identicas.');
            return;
        }

        $loading.show();

        if( !$scope.flags.editar ) {
            ModelService.add($scope.usuario)
                .success(function (res) {
                    $message.success(res.texto);
                    $state.go('usuarios');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El usuario '+$scope.usuario.vc_nombre+', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else {
            ModelService.update($scope.usuario)
                .success(function (res) {
                    $message.success(res.texto);
                    $loading.hide();
                    $state.go('usuarios');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El usuario '+$scope.usuario.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){
        $rol = $rootScope.usuario.rol.id;
        if($rol == 4)
            window.location.href = 'admin';

        // Definir Modelo
        ModelService.addModel('usuarios');

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
                            $scope.usuario                = res;
                            $scope.usuario.id_genero      = String($scope.usuario.id_genero);
                            $scope.usuario.id_rol         = String($scope.usuario.id_rol);
                            $scope.usuario.vc_password_re = angular.copy($scope.usuario.vc_password);
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
