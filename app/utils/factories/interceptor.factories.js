/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Fabricas de Utilidades
|
*/

+function(){

    angular.module('gl.interceptor.factories',[])

	.factory('$httpInterceptor', ['$q', '$message', '$timeout', function($q, $message, $timeout) {

		return {
			request: function(config) {
				config.headers.Authorization = localStorage.getItem('gjb.token');
				return config;
			},
			requestError: function(rejection) {
				return $q.reject(rejection);
			},
			response: function(response) {
				if (response.headers().authorization) {
                    localStorage.setItem('gjb.token', response.headers().authorization);
				}
				return response;
			},
			responseError: function(rejection) {
				if (rejection.status == 401 || rejection.status == 404) {
					$message.warning('Tu sesión ha expirado, te redireccionaremos al inicio de sesión.');
					$timeout(function(){
						window.location = 'login';
					}, 5000);
				}
				return $q.reject(rejection);
			}
		};

	}]);

}();
