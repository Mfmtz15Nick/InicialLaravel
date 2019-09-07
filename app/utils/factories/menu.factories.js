/*
|==========================================================================
| Geeklopers - Document JS
|==========================================================================
|
| - Fabricas de Menu
|
*/

+function(){
    angular.module('gl.menu.factories',[])
        .factory('$menu', [ function(){
            var factory = {
                general : [
                    // INICIO
                        { state: 'inicio', url: 'inicio', file: 'inicio', ext: 'html' },
                    // PERFIL
                        { state: 'perfil', url: 'perfil', file: 'perfil', ext: 'html' },
                    // CONFIGURACION
                        { state: 'configuracion', url: 'configuracion', file: 'configuracion', ext: 'html' },
                ],
                admin : {
                    states: [
                        // USUARIOS
                            { state: 'usuarios', url: 'usuarios', file: 'usuarios', ext: 'html' },
                            { state: 'usuariosNuevo', url: 'usuarios/nuevo', file: 'usuariosNuevo', ext: 'html' },
                            { state: 'usuariosEditar', url: 'usuarios/:id/editar', file: 'usuariosNuevo', ext: 'html' },
                    ],
                    navigation : {
                        aside: [
                            // MENU
                            { name: 'Menú', url: '', icon: '', title: 1 },
                                { name: 'Usuarios', url: '#/usuarios', icon: 'ti-user', title: 0 },
                        ],
                        header: [
                            { name: 'Mi perfil', url: '#/perfil', icon: 'ti-user', title: 1 },
                        ],
                    },
                }
            };

            return factory;
        }])
}();
