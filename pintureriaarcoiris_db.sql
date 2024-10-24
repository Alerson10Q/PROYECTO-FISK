CREATE DATABASE PintureriaArcoiris_db;
USE PintureriaArcoiris_db;
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(100) NOT NULL,
    clasificacion ENUM('Cliente', 'Administrador') NOT NULL,
    fecha_ingreso TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE Clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    direccion VARCHAR(255),
    datos_contacto VARCHAR(255),
    fecha_nacimiento DATE,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);
CREATE TABLE Proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(255),
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE Productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    precio DECIMAL(10, 2) NOT NULL,
    stock_cantidad INT,
    nombre VARCHAR(100),
    marca VARCHAR(100),
    imagen VARCHAR(255),
    descripcion TEXT,
    tipo_producto ENUM('Pinturas', 'Accesorios', 'Mini-ferretería') NOT NULL,
    fecha_ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_proveedor INT,
    FOREIGN KEY (id_proveedor) REFERENCES Proveedores(id_proveedor)
);
CREATE TABLE Accesorios (
    id_producto INT PRIMARY KEY,
    medidas VARCHAR(50),
    tipo VARCHAR(50),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);
CREATE TABLE Mini_ferreteria (
    id_producto INT PRIMARY KEY,
    garantia VARCHAR(50),
    tipo VARCHAR(50),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);
CREATE TABLE Pinturas (
    id_producto INT PRIMARY KEY,
    litros DECIMAL(5, 2),
    funcion_aplicacion ENUM('exterior', 'interior', 'metal', 'madera', 'sintética', 'membrana') NOT NULL,
    codigo_de_color VARCHAR(50),
    fecha_vencimiento DATE,
    terminacion ENUM('mate', 'brillante', 'semimate', 'satinada') NOT NULL,
    fecha_creacion DATE,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) );
CREATE TABLE Paleta_de_color (
    id_paleta INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100),
    codigo_de_color VARCHAR(50),
    nombre_color VARCHAR(100),
    tintes_utilizados TEXT );
CREATE TABLE Ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    forma_de_pago ENUM('efectivo', 'tarjeta') NOT NULL,
    fecha_de_venta DATE,
    productos_vendidos TEXT,
    valor_de_venta DECIMAL(10, 2),
    estado ENUM('en proceso', 'completado') NOT NULL,
    direccion_de_envio VARCHAR(255),
    datos_extra_notas TEXT,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) );
CREATE TABLE Venta_de_productos (
    id_venta INT, id_producto INT,
    precio_del_momento DECIMAL(10, 2),
    cantidad INT,
    PRIMARY KEY (id_venta, id_producto),
    FOREIGN KEY (id_venta) REFERENCES Ventas(id_venta),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) );
CREATE TABLE Carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT, id_producto INT, cantidad INT,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) );
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'admin';
GRANT ALL PRIVILEGES ON PintureriaArcoiris_db.* TO 'admin'@'localhost';
