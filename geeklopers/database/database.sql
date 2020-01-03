
/* BASE DE DATOS EVENTOS GEEKLOPES - LARAVEL */

SET FOREIGN_KEY_CHECKS=0;


/*=========================

      Tablas de Inicio de Sesión

===========================*/

  /* Tabla de roles */
  DROP TABLE IF EXISTS roles;
  CREATE TABLE roles(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    vc_nombre VARCHAR(50) NOT NULL,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

  
  /* Tabla de generos */
  DROP TABLE IF EXISTS generos;
  CREATE TABLE generos(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    vc_nombre VARCHAR(50) NOT NULL,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

  
  /* Tabla de usuarios */
  DROP TABLE IF EXISTS usuarios;
  CREATE TABLE usuarios(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

  /* Tabla de usuariosRoles */
  DROP TABLE IF EXISTS usuariosRoles;
  CREATE TABLE usuariosRoles(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_rol INT UNSIGNED NOT NULL,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id ),
    FOREIGN KEY( id_usuario ) REFERENCES usuarios ( id ),
    FOREIGN KEY( id_rol ) REFERENCES roles ( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

  /* Tabla de usuariosDetalles */
  DROP TABLE IF EXISTS usuariosDetalles;
  CREATE TABLE usuariosDetalles(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_genero INT UNSIGNED NOT NULL,

    vc_nombre VARCHAR(50) NOT NULL,
    vc_apellido VARCHAR(50) NOT NULL,
    vc_email VARCHAR(50) NOT NULL,
    vc_password VARCHAR(50) NOT NULL,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id ),
    CONSTRAINT FK_UsuariosDetalles_Usuarios FOREIGN KEY( id_usuario )
      REFERENCES usuarios ( id ),
    CONSTRAINT FK_UsuariosDetalles_Generos FOREIGN KEY( id_genero )
      REFERENCES generos ( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

  /* Tabla de usuariosTokens */
  DROP TABLE IF EXISTS usuariosTokens;
  CREATE TABLE usuariosTokens(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_rol INT UNSIGNED NOT NULL,
    id_token TEXT NOT NULL,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id ),
    CONSTRAINT FK_Usuarios FOREIGN KEY( id_usuario ) REFERENCES usuarios ( id ),
    CONSTRAINT FK_Roles FOREIGN KEY( id_rol ) REFERENCES roles ( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/* --------------------- CATALOGOS --------------------- */

/* Tabla de tiposEventos */
  DROP TABLE IF EXISTS tiposEventos;
  CREATE TABLE tiposEventos(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id )

  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/* Tabla de tiposEventosDetalles */
  DROP TABLE IF EXISTS tiposEventosDetalles;
  CREATE TABLE tiposEventosDetalles(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_tiposEventos INT UNSIGNED NOT NULL,
   

    vc_nombre VARCHAR(50) NOT NULL,
    vc_imagen VARCHAR(50) NOT NULL,
    vc_imagenUrl VARCHAR(50) NOT NULL,
   
    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id ),
    CONSTRAINT FK_TiposEventosDetalles_TiposEventos FOREIGN KEY( id_tiposEventos )
      REFERENCES tiposEventos ( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;



/* Tabla de clientes */
  DROP TABLE IF EXISTS clientes;
  CREATE TABLE clientes(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/* Tabla de clientesDetalles */
  DROP TABLE IF EXISTS clientesDetalles;
  CREATE TABLE clientesDetalles(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_cliente INT UNSIGNED NOT NULL,
   

    vc_nombre VARCHAR(50) NOT NULL,
    vc_apellido VARCHAR(50) NOT NULL,
    nu_celular VARCHAR(50) NOT NULL,
   
    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id ),
    CONSTRAINT FK_ClientesDetalles_Clientess FOREIGN KEY( id_cliente )
      REFERENCES clientes ( id )
  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

  /* Tabla de eventos */
  DROP TABLE IF EXISTS eventos;
  CREATE TABLE eventos(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id )

  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/* Tabla de eventosDetalles */
  DROP TABLE IF EXISTS eventosDetalles;
  CREATE TABLE eventosDetalles(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    vc_nombre VARCHAR(50) NOT NULL,
    id_evento INT UNSIGNED NOT NULL,
    id_tiposEventos INT UNSIGNED NOT NULL,
    

    sn_activo TINYINT NOT NULL DEFAULT 1,
    sn_eliminado TINYINT NOT NULL DEFAULT 0,
    dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dt_eliminado TIMESTAMP NULL,
    id_creador INT UNSIGNED NOT NULL,

    PRIMARY KEY( id ),

    CONSTRAINT FK_eventosDetalles_eventos FOREIGN KEY( id_evento )
    REFERENCES eventos ( id ),

    CONSTRAINT FK_eventosDetalles_TiposEventos FOREIGN KEY( id_tiposEventos )
    REFERENCES tiposEventos ( id )

  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


  /* Tabla de eventosImagenes */
  DROP TABLE IF EXISTS eventosImagenes;
  CREATE TABLE eventosImagenes(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_evento INT UNSIGNED NOT NULL,
  nu_posicion SMALLINT(6) NOT NULL,

  vc_imagen VARCHAR(100) NOT NULL,
  vc_imagenUrl TEXT NOT NULL,

  sn_activo TINYINT NOT NULL DEFAULT 1,
  sn_eliminado TINYINT NOT NULL DEFAULT 0,
  dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  dt_eliminado TIMESTAMP NULL,
  id_creador INT UNSIGNED NOT NULL,

  PRIMARY KEY( id ),
  CONSTRAINT FK_eventosImagenes_eventos FOREIGN KEY( id_evento ) REFERENCES eventos ( id )
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/* Tabla de clientesEventos */
DROP TABLE IF EXISTS clientesEventos;
CREATE TABLE clientesEventos(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cliente INT UNSIGNED NOT NULL,
  id_evento INT UNSIGNED NOT NULL,

  nu_dia SMALLINT(7) NOT NULL,
  nu_mes SMALLINT(10) NOT NULL,


  tm_entrada TIME NULL,
  tm_salida TIME NULL,

  sn_activo TINYINT NOT NULL DEFAULT 1,
  sn_eliminado TINYINT NOT NULL DEFAULT 0,
  dt_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dt_editado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  dt_eliminado TIMESTAMP NULL,
  id_creador INT UNSIGNED NOT NULL,

  PRIMARY KEY( id ),
  FOREIGN KEY( id_creador ) REFERENCES usuarios ( id ),
  FOREIGN KEY( id_cliente ) REFERENCES clientes ( id ),
  FOREIGN KEY( id_evento ) REFERENCES eventos ( id )
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;




-- INSERT
 /* Insertar roles */
  INSERT INTO roles(vc_nombre, id_creador) VALUES('Sistema', 1);
  INSERT INTO roles(vc_nombre, id_creador) VALUES('Administrador', 1);
  INSERT INTO roles(vc_nombre, id_creador) VALUES('Auxiliar', 1);
  INSERT INTO roles(vc_nombre, id_creador) VALUES('Consultor', 1);
/* Insertar generos */
  INSERT INTO generos(vc_nombre, id_creador) VALUES('Masculino', 1);
  INSERT INTO generos(vc_nombre, id_creador) VALUES('Femenino', 1);
  /* Insertar a usuario */
  INSERT INTO usuarios(id, id_creador) VALUES(2, 1);
  INSERT INTO usuariosRoles(id_usuario, id_rol, id_creador) VALUES(2,2,1);
   INSERT INTO usuariosDetalles(id_usuario, id_genero, vc_nombre, vc_apellido, vc_email, vc_password, id_creador)
      VALUES(2, 1, 'Admin', 'Bladmir', 'admin@bladmir.com', 'Admin123.', 1);




