/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador de la Vista de Agendas
|
*/

var app = angular.module('agendas', []);

// Controller
app.controller( 'agendasController', ['$scope', '$rootScope', '$state', '$stateParams', '$location', '$util', '$message', '$loading', '$validate', 'ModelService', '$http',
    function( $scope, $rootScope, $state, $stateParams, $location, $util, $message, $loading, $validate, ModelService, $http  ){

// Scope Variables
    $scope.agendas               = [];
    $scope.agendasPaginados      = [];
    $scope.cargando               = false;
    $scope.anteriorUrl            = null;
    $scope.siguienteUrl           = null;
    $scope.paginaActual           = 0;
    $scope.buscar                 = "";
    $scope.minimoBusqueda         = 3;
    $scope.total                  = 0;
    $scope.totalMostrarPaginado   = 2;
    $scope.agendasPaginados      = [];
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
                $scope.agendas     = res.data;
                $scope.anteriorUrl  = res.prev_page_url;
                $scope.siguienteUrl = res.next_page_url;
                $scope.paginaActual = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los agendas.");
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

        urlConsulta = 'api/agendas?page='+numero;

        if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda ){
          urlConsulta = 'api/agendas/'+$scope.buscar+'/buscar?page='+numero;
        }

          ModelService.custom('get', urlConsulta)
            .success(function(res){
                $scope.agendas               = res.data;
                $scope.agendasPaginados      = res.data;
                $scope.anteriorUrl            = res.prev_page_url;
                $scope.siguienteUrl           = res.next_page_url;
                $scope.paginaActual           = res.current_page;
            })
            .error(function (error) {
                if(error.texto){
                    $message.warning(error.texto);
                } else {
                    $message.warning("No se pudieron obtener los agendas.");
                }
            })
            .finally(function(){
                $loading.hide();
                $scope.cargando = false;
            });
      }
    };

    $scope.buscarAgendas = function(){
      if( $scope.buscar != "" && $scope.buscar.length >= $scope.minimoBusqueda  ){

        urlConsulta = 'api/agendas/'+$scope.buscar+'/buscar';

        ModelService.custom('get', urlConsulta)
          .success(function(res){
              $scope.agendas            = res.data;
              $scope.agendasPaginados   = res.data;
              $scope.total              = res.last_page;
              $scope.anteriorUrl        = res.prev_page_url;
              $scope.siguienteUrl       = res.next_page_url;
              $scope.paginaActual       = res.current_page;
          })
          .error(function (error) {
              if(error.texto){
                  $message.warning(error.texto);
              } else {
                  $message.warning("No se pudieron obtener los agendas.");
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
        $state.go('agendasNuevo');
    };

    $scope.editar = function( agenda ) {
        $state.go('agendasEditar', { id : agenda.id });
    };

    $scope.horarios = function( agenda ) {
        $state.go('agendasHorarios', { id : agenda.id });
    };

    $scope.eliminar = function( agenda ) {
        $message.confirm({
            text    : '¿Estás seguro de eliminar el agendas '+agenda.vc_nombre+'?',
            callback : function( msg ){
                $loading.show();
                ModelService.delete( agenda.id )
                    .success(function(res){
                        msg.close();
                        var posicion = $util.getPosition($scope.agendas, 'id', agenda.id);
                        $scope.agendas.splice( posicion, 1 );
                        $message.success(res.texto);
                    })
                    .error(function (error) {
                        if(error.texto){
                            $message.warning(error.texto);
                        } else {
                            $message.warning('El agendas '+agenda.vc_nombre+', no pudo eliminar correctamente.');
                        }
                    })
                    .finally(function(){
                        $loading.hide();
                    });
            }
        });
    };

    $scope.init = function() {

        ModelService.addModel('agendas');

        $scope.cargando   = true;

        ModelService.list()
            .success(function( res ){
                $scope.agendas            = res.data;
                $scope.agendasPaginados   = res.data;
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
