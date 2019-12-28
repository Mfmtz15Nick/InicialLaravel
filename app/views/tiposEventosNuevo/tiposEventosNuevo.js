/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de El Vista de tiposEventosNuevo
|
*/

var app = angular.module('tiposEventosNuevo', ['angularFileUpload','as.sortable']);

// Controller
app.controller( 'tiposEventosNuevoController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$uploader', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $uploader, $http ){

// Scope Variables
    $scope.tipoEvento = { vc_imagenUrl : '' };
    $scope.generos = [];
    $scope.flags  = {
        editar: false
    };

// Scope Functions
    var uploader = $uploader.load({
        url : 'api/tiposEventos/upload',
        autoUpload: true,
        headers: {
            Authorization: localStorage.getItem('gc.token')
        }
    });

    $scope.uploader = uploader;
    
    uploader.onCompleteItem = function(item, response, status, headers){
        $scope.tipoEvento.vc_imagenUrl = response.nombre;
        
        if( uploader.queue.length > 1 ){
            for (var i = 0; i < uploader.queue.length; i++) {
                if ( i == 0 ) {
                    uploader.queue[i].remove();
                }
            }
        }
    };
    $scope.deleteItem = function(item){
        $scope.tipoEvento.vc_imagenUrl = '';
        item.remove();
    };

    $scope.eliminar = function() {
        $scope.tipoEvento.vc_imagenUrl = '';
    };


    $scope.regresar = function() {
        $state.go('tiposEventos');
    };

    $scope.submit = function() {
        $scope.guardar();
    };

    $scope.guardar = function(){

        if( !$validate.form('form-validate') )
            return;

        if( $scope.tipoEvento.vc_imagenUrl == '' ){
            $message.warning('No se ha cargado ninguna imagen.');
            return;
        }

        $loading.show();

        if( !$scope.flags.editar ) {

            ModelService.add($scope.tipoEvento)
                .success(function () {
                    $message.success('El tipoEvento '+$scope.tipoEvento.vc_nombre+', fue guardado correctamente.');
                    $state.go('tiposEventos');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El tipoEvento '+$scope.tipoEvento.vc_nombre+', no se pudo agregar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        } else {

            ModelService.update($scope.tipoEvento)
                .success(function () {
                    $message.success('El tipoEvento '+$scope.tipoEvento.vc_nombre+', fue editado correctamente.');
                    $loading.hide();
                    $state.go('tiposEventos');
                })
                .error(function (error) {
                    if(error.texto){
                        $message.warning(error.texto);
                    } else {
                        $message.warning('El tipoEvento '+$scope.tipoEvento.vc_nombre+', no se pudo editar correctamente.');
                    }
                })
                .finally(function(){
                    $loading.hide();
                });
        }
    };

    $scope.init = function(){

        $rol = $rootScope.usuario.rol.id;
        console.log($rol)
        if($rol == 4)
            window.location.href = 'admin';


        // Definir Modelo
        ModelService.addModel('tiposEventos');

        $loading.show();

        ModelService.create()
            .success(function(res){
                // Verificar proceso Agregar o Editar
                $util.stateParams(function(){

                    $scope.flags.editar = true;

                    ModelService.edit( $stateParams.id )
                        .success(function(res){
                            $scope.tipoEvento                = res;
                            $scope.tipoEvento.vc_nombre      = res.detalle.vc_nombre;
                            $scope.tipoEvento.vc_imagenUrl   = res.detalle.vc_imagenUrl;
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
