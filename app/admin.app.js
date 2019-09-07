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

		// Request User
        $authentication.check( function( res ){

	    	if( !res || $authentication.usuario.rol.id != 2 ){
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
                case 2 : // Adminstrador
                        states = [].concat.call( $menu.general, $menu.admin.states );
                        $rootScope.usuario.menu = [].concat.call( $menu.admin.navigation );
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
							localStorage.removeItem('gjb.token');
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
}]);

}();