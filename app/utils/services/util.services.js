/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Servicios para Consultas
|
*/

+function(){

    angular.module('gl.util.services',[])

    .service('$uploader', ['FileUploader', function( FileUploader ){
        this.load = function(obj){
            var options = angular.extend({
                url : 'api/upload'
            }, obj);

            return new FileUploader( options );
        }
    }])

    .service('ModelService', ['$http', function( $http ){
        var url = null;

        this.addModel = function( model ){
            url = 'api/' + model;
        };

        this.controller = function(_url)
        {
            return $http.get(_url);
        };

        this.show = function( id ) {
            return $http.get( url +'/'+ id);
        };

        this.list = function(){
            return $http.get( url );
        };

        this.create = function(){
            return $http.get( url + '/create');
        };

        this.add = function( params ){
            return $http.post( url, params);
        };

        this.edit = function( id ){
            return $http.get( url + '/' + id + '/edit');
        };

        this.delete = function( id ){
            return $http.delete( url + '/' + id);
        };

        this.update = function( params ){
            return $http.put( url + '/' + params.id , params);
        };

        this.custom = function(method, urlCustom, params = null) {
            switch(method) {
                case 'get':
                    return $http.get( urlCustom );
                    break;
                case 'post':
                    return $http.post( urlCustom, params);
                    break;
                case 'put':
                    return $http.put( urlCustom, params);
                    break;
                case 'delete':
                    return $http.delete( urlCustom );
                    break;
                default:
                    return null;
            }
        }
    }])

    .service('LoginService', ['$http', function( $http ){
        url = '';

        this.login = function( user ) {
            return $http.post( url + 'api/login', user );
        };

        this.logout = function() {
            return $http.get( url + 'api/login/logout');
        };

        this.check = function() {
            return $http.get( url + 'api/login/check');
        };
    }]);

}();
