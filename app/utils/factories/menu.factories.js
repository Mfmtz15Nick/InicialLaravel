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

                        // AGENDAS
                            { state: 'tiposEventos', url: 'tiposEventos', file: 'tiposEventos', ext: 'html' },
                            { state: 'tiposEventosNuevo', url: 'tiposEventos/nuevo', file: 'tiposEventosNuevo', ext: 'html' },
                            { state: 'tiposEventosEditar', url: 'tiposEventos/:id/editar', file: 'tiposEventosNuevo', ext: 'html' },
                            { state: 'tiposEventosGaleria', url: 'tiposEventos/:id/galeria', file: 'tiposEventosGaleria', ext: 'html' },
                        
                        // USUARIOS
                            { state: 'clientes', url: 'clientes', file: 'clientes', ext: 'html' },
                            { state: 'clientesNuevo', url: 'clientes/nuevo', file: 'clientesNuevo', ext: 'html' },
                            { state: 'clientesEditar', url: 'clientes/:id/editar', file: 'clientesNuevo', ext: 'html' },
                        
                        // Eventos
                            { state: 'eventos', url: 'eventos', file: 'eventos', ext: 'html' },
                            { state: 'eventosNuevo', url: 'eventos/nuevo', file: 'eventosNuevo', ext: 'html' },
                            { state: 'eventosEditar', url: 'eventos/:id/editar', file: 'eventosNuevo', ext: 'html' },

                    ],
                    navigation : {
                        aside: [
                            // MENU
                            { name: 'Menú', url: '', icon: '', title: 1 },
                                { name: 'Usuarios', url: '#/usuarios', icon: 'ti-user', title: 0 },
                                { name: 'Tipos Eventos', url: '#/tiposEventos', icon: 'ti-star', title: 0 },
                                { name: 'Clientes', url: '#/clientes', icon: 'ti-crown', title: 0 },
                                { name: 'Eventos', url: '#/eventos', icon: 'ti-pin', title: 0 },
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
