/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de tiposEventos
|
*/

var app = angular.module('tiposEventos', []);

// Controller
app.controller( 'tiposEventosController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $http  ){

// Scope Variables
    $scope.tiposEventos               = [];
    $scope.tiposEventosPaginados      = [];
    $scope.cargando               = false;
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.tiposEventosPaginados      = [];
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
                $scope.tiposEventos     = res.data;
                $scope.anteriorUrl  = res.prev_page_url;
                $scope.siguienteUrl = res.next_page_url;
                $scope.paginaActual = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los tiposEventos.");
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

        urlConsulta = 'api/tiposEventos?page='+numero;

        if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/tiposEventos/'+$scope.buscar+'/buscar?page='+numero;
        }

          ModelService.custom('get', urlConsulta)
            .success(function(res){
                $scope.tiposEventos               = res.data;
                $scope.tiposEventosPaginados      = res.data;
                $scope.anteriorUrl            = res.prev_page_url;
                $scope.siguienteUrl           = res.next_page_url;
                $scope.paginaActual           = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los tiposEventos.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
      }
    };

    $scope.buscarTiposEventos = function(){
      if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda  ){

        urlConsulta = 'api/tiposEventos/'+$scope.buscar+'/buscar';

        ModelService.custom('get', urlConsulta)
          .success(function(res){
              $scope.tiposEventos            = res.data;
              $scope.tiposEventosPaginados   = res.data;
              $scope.total              = res.last_page;
              $scope.anteriorUrl        = res.prev_page_url;
              $scope.siguienteUrl       = res.next_page_url;
              $scope.paginaActual       = res.current_page;
          })
          .error(function (error) {
              if(error.texto){
                  $message.warning(error.texto);
              } else {
                  $message.warning("No se pudieron obtener los tiposEventos.");
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
        $state.go('tiposEventosNuevo');
    };

    $scope.editar = function( tiposEventos ) {
        $state.go('tiposEventosEditar', { id : tiposEventos.id });
    };

    $scope.galeria = function( tiposEventos ) {
        $state.go('tiposEventosGaleria', { id : tiposEventos.id });
    };

    $scope.horarios = function( tiposEventos ) {
        $state.go('tiposEventosHorarios', { id : tiposEventos.id });
    };

    $scope.eliminar = function( tiposEventos ) {
        $message.confirm({
            text    : '¿Estás seguro de eliminar el tiposEventos '+tiposEventos.vc_nombre+'?',
            callback : function( msg ){
                $loading.show();
                ModelService.delete( tiposEventos.id )
                    .success(function(res){
                        msg.close();
                        var posicion = $util.getPosition($scope.tiposEventos, 'id', tiposEventos.id);
                        $scope.tiposEventos.splice( posicion, 1 );
                        $message.success(res.texto);
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('El tiposEventos '+tiposEventos.vc_nombre+', no pudo eliminar correctamente.');
                        }
                    })
                    .finally(function(){
                        $loading.hide();
                    });
            }
        });
    };

    $scope.init = function() {

        ModelService.addModel('tiposEventos');

        $scope.cargando   = true;

        ModelService.list()
            .success(function( res ){
                $scope.tiposEventos            = res;
                $scope.tiposEventosPaginados   = res.data;
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
