/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de Citas
|
*/

var app = angular.module('citas', []);

// Controller
app.controller( 'citasController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $http  ){

// Scope Variables
    $scope.citas               = [];
    $scope.citasPaginados      = [];
    $scope.cargando               = false;
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.citasPaginados      = [];
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.cargando               = false;
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.SISTEMA                = 1;

    var urlConsulta;

// Scope Funciones

    $scope.paginador = function(numero) {
        return new Array(numero);
    }

    $scope.cambiarPagina = function(url){
        $scope.cargando = true;
        $loading.show();
        ModelService.custom('get', url )
            .success(function(res){
                $scope.citas     = res.data;
                $scope.anteriorUrl  = res.prev_page_url;
                $scope.siguienteUrl = res.next_page_url;
                $scope.paginaActual = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los citas.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
    }

    $scope.consultar = function(numero){
    // Consultar si es diferente la pagina a la actual
    if ( numero[0][0] != $scope.paginaActual ) {
        $scope.cargando = true;
        $loading.show();

        urlConsulta = 'api/citas?page='+numero;

        if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/citas/'+$scope.buscar+'/buscar?page='+numero;
        }

          ModelService.custom('get', urlConsulta)
            .success(function(res){
                $scope.citas               = res.data;
                $scope.citasPaginados      = res.data;
                $scope.anteriorUrl            = res.prev_page_url;
                $scope.siguienteUrl           = res.next_page_url;
                $scope.paginaActual           = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los citas.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
      }
    };

    $scope.buscarCitas = function(){
      if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda  ){

        urlConsulta = 'api/citas/'+$scope.buscar+'/buscar';

        ModelService.custom('get', urlConsulta)
          .success(function(res){
              $scope.citas              = res.data;
              $scope.citasPaginados     = res.data;
              $scope.total              = res.last_page;
              $scope.anteriorUrl        = res.prev_page_url;
              $scope.siguienteUrl       = res.next_page_url;
              $scope.paginaActual       = res.current_page;
          })
          .error(function (error) {
              if(error.texto){
                  $message.warning(error.texto);
              } else {
                  $message.warning("No se pudieron obtener los citas.");
              }
          })
          .finally(function(){
            $loading.hide();
          });
      }
      else {
          $scope.actualizar();
      }
    }

    $scope.actualizar = function() {
        $scope.init();
    };

    $scope.agregar = function() {
        $state.go('citasNuevo');
    };

    $scope.editar = function( cita ) {
        $state.go('citasEditar', { id : cita.id });
    };

    $scope.eliminar = function( cita ) {
        $message.confirm({
            text    : '¿Estás seguro de eliminar la cita '+cita.vc_nombre+'?',
            callback : function( msg ){
                $loading.show();
                ModelService.delete( cita.id )
                    .success(function(res){
                        msg.close();
                        var posicion = $util.getPosition($scope.citas, 'id', cita.id);
                        $scope.citas.splice( posicion, 1 );
                        $message.success(res.texto);
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('La cita '+cita.vc_nombre+', no se pudo eliminar correctamente.');
                        }
                    })
                    .finally(function(){
                        $loading.hide();
                    });
            }
        });
    };

    $scope.init = function() {

        ModelService.addModel('citas');

        $scope.cargando   = true;

        ModelService.list()
            .success(function( res ){
                $scope.citas              = res.data;
                $scope.citasPaginados     = res.data;
                $scope.total              = res.last_page;
                $scope.anteriorUrl        = res.prev_page_url;
                $scope.siguienteUrl       = res.next_page_url;
                $scope.paginaActual       = res.current_page;
            })
            .error(function () {
                $message.warning("No se obtener los registros.");
            })
            .finally(function(){
                $scope.cargando = false;
            });
    };

// Iniciar Controller
    $scope.init();
}]);
