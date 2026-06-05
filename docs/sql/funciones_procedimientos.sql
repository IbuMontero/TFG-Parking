-- FUNCIONES Y PROCEDIMIENTOS – SISTEMA DE PARKING

USE parking;

DELIMITER //

DROP FUNCTION IF EXISTS contar_reservas_usuario//
CREATE FUNCTION contar_reservas_usuario(p_id_usuario INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE total INT;

    SELECT COUNT(*)
    INTO total
    FROM reservas
    WHERE id_usuario = p_id_usuario;

    RETURN total;
END//

DROP PROCEDURE IF EXISTS insertar_plaza_validada//
CREATE PROCEDURE insertar_plaza_validada(
    IN p_tipo VARCHAR(20),
    IN p_reservado_para VARCHAR(30)
)
BEGIN
    IF p_tipo NOT IN ('coche', 'moto', 'bici') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de plaza no válido';
    END IF;

    IF p_reservado_para NOT IN ('ninguno', 'alumnado', 'profesorado', 'movilidad_reducida') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Valor de reservado_para no válido';
    END IF;

    INSERT INTO plazas (tipo, reservado_para, estado)
    VALUES (p_tipo, p_reservado_para, 'libre');
END//

DROP PROCEDURE IF EXISTS crear_reserva_validada//
CREATE PROCEDURE crear_reserva_validada(
    IN p_id_usuario INT,
    IN p_id_plaza INT
)
BEGIN
    DECLARE v_estado VARCHAR(20);
    DECLARE v_usuario_existe INT;
    DECLARE v_plaza_existe INT;

    SELECT COUNT(*) INTO v_usuario_existe
    FROM usuarios
    WHERE id = p_id_usuario;

    IF v_usuario_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El usuario no existe';
    END IF;

    SELECT COUNT(*) INTO v_plaza_existe
    FROM plazas
    WHERE id = p_id_plaza;

    IF v_plaza_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La plaza no existe';
    END IF;

    SELECT estado INTO v_estado
    FROM plazas
    WHERE id = p_id_plaza;

    IF v_estado <> 'libre' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La plaza no está libre';
    END IF;

    INSERT INTO reservas (id_usuario, id_plaza, fecha_reserva)
    VALUES (p_id_usuario, p_id_plaza, CURDATE());
END//

DELIMITER ;
