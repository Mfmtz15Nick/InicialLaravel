/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Controllador Admin de la Aplicación
|
*/

+function(){

var stateProvider = null,
	urlRouterProvider = null;

var admin = angular.module(
	'admin',
	[
		'ui.router',
		'oc.lazyLoad',
		'gl.util.factories',
		'gl.util.services',
    	'gl.authentication.services',
		'gl.menu.factories',
		'gl.validate.service',
		'gl.interceptor.factories',
		'mwl.calendar',
		'ui.tinymce',
		'bw.paging'
	]
);

admin.config(['$stateProvider', '$urlRouterProvider', '$httpProvider', '$interpolateProvider',
	function($stateProvider, $urlRouterProvider, $httpProvider, $interpolateProvider){

		$interpolateProvider.startSymbol('[[').endSymbol(']]');

		stateProvider = $stateProvider;
		urlRouterProvider = $urlRouterProvider;

		$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

		$httpProvider.interceptors.push('$httpInterceptor');
}]);

admin.run( [ '$rootScope', '$state', '$location', '$util', '$menu', '$authentication',
	function( $rootScope, $state, $location, $util, $menu, $authentication ){

		let ADMINISTRADOR 	= 2;
		let AUXILIAR 		= 3;
		let CONSULTOR	 	= 4;
		// Request User
        $authentication.check( function( res ){
			
			$rol = $authentication.usuario.rol.id;
	    	if( !res || ( $rol != ADMINISTRADOR ) && ( $rol != AUXILIAR ) && ( $rol != CONSULTOR )  ){
				console.log('directo a login');
	    		// window.location.href = 'login';
	    	} else {
				$rootScope.usuario = $authentication.usuario;
				
	    		init();
	    	}
	    });

        var states = [];

		// INIT
	    var init = function(){

            switch( $rootScope.usuario.rol.id ){
                case ADMINISTRADOR : // Adminstrador
                    states = [].concat.call( $menu.general, $menu.admin.states );
                    $rootScope.usuario.menu = [].concat.call( $menu.admin.navigation );
				break;
				case AUXILIAR : // AUXILIAR
					console.log('Entre');
                    states = [].concat.call( $menu.general, $menu.auxiliar.states );
                    $rootScope.usuario.menu = [].concat.call( $menu.auxiliar.navigation );
				break;
				case CONSULTOR : // AUXILIAR
					console.log('Entre');
                    states = [].concat.call( $menu.general, $menu.consultor.states );
                    $rootScope.usuario.menu = [].concat.call( $menu.consultor.navigation );
                break;
            }
            var url = $location.path().replace('/', ''),
                position = $util.getPosition( states, 'url', url ),
                state = position ? states[position].state : 'inicio' ;

	    	initState(function(){
	    		$state.go( state );
	    	});
	    };

	    var initState = function( callback ){
			// Create State Database
	    	states.forEach(function( state, index, array ){

				var rutaLocal = state.file;
				if (state.ruta) {
					rutaLocal = state.ruta + '/' +state.file;
				}

				stateProvider.state( state.state, {
		            url: '/'+state.url,
		            templateUrl: 'app/views/'+rutaLocal+'/'+state.file+'.'+state.ext,
		            resolve: {
		                include: function( $ocLazyLoad ){
		                    return $ocLazyLoad.load({
		                        name: state.state,
		                        files: [
		                        	'app/views/'+rutaLocal+'/'+state.file+'.js',
		                        	'app/views/'+rutaLocal+'/'+state.file+'.css'
		                        ]
		                    })
		                }
		            }
	        	});
			});

	        urlRouterProvider.otherwise('/inicio');
	        callback();
	    };
}]);

/* CONTROLLER */
admin.controller( 'adminController', ['$scope', '$rootScope', '$state', '$location', '$loading', '$message', '$authentication',
	function( $scope, $rootScope, $state, $location, $loading, $message, $authentication ){

        $scope.lock 			= $loading.status;
        $scope.bodyClass 	= '';
        $scope.login 			= {};
        $scope.menu 			= {};
				$scope.intervalo;

	    // RootScope
		$rootScope.$on('$stateChangeStart', function( event, next, current ){
		    $scope.bodyClass 	= 'normal';
		    $scope.login 			= $rootScope.usuario;
		    $scope.menu 			= $rootScope.usuario.menu[0];
        });

		$scope.inicio = function(){
			$state.go('inicio');
		};

    $scope.logout = function(){
    	$authentication.logout( function( res ){
    		if( res ){
					localStorage.removeItem('gc.token');
    			window.location.href = 'login';
    		} else {
    			$message.warning('No se pudo realizar el cierre de sesión.');
    		}
    	});
    };

    $scope.perfil = function(){

    };

    $scope.goto = function( url ){
    	window.location.href = url;
    };

		/** Función para cambiar la visualización del sistema (claro-nocturno)
		*/
		$scope.nightMode = function() {
			if ($('body').hasClass('night')) {
				$('body').removeClass('night');
			} else {
				$('body').addClass('night');
			}
		};
		/** Función para mostrar el menu lateral en responsivo
		*/
		$scope.showMenu = function() {
			if ($('body').hasClass('mobile')) {
				if ($('body').hasClass('aside-show')) {
					$('body').removeClass('aside-show');
				} else {
					$('body').addClass('aside-show');
				}
			}
		};
		/** Función para mostrar la vista responsiva del sistema
		*/
		$scope.vistaResponsiva = function() {
			var width = $(window).width();
			if (width < 992) {
				$('body').addClass('mobile');
			} else {
				$('body').removeClass('mobile');
			}
		};
		$(window).resize(function(){
			$scope.vistaResponsiva();
		});

		$scope.vistaResponsiva();

}]);

}();
