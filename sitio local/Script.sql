CREATE TABLE tiempo_real ("parametro" VARCHAR (20) NOT NULL, "valor" NUMERIC NOT NULL);
INSERT INTO tiempo_real VALUES ('Temperatura', '0');
INSERT INTO tiempo_real VALUES ('Ruido', '0');
INSERT INTO tiempo_real VALUES ('Calidad de aire', '0');

CREATE TABLE contadores_incidencias ("parametro" VARCHAR(20) NOT NULL, "valor" INT NOT NULL);
INSERT INTO contadores_incidencias VALUES ('Temperatura', '0');
INSERT INTO contadores_incidencias VALUES ('Ruido', '0');
INSERT INTO contadores_incidencias VALUES ('Calidad de aire', '0');

CREATE TABLE incidencias_temperatura ("id" INT NOT NULL, "fecha_y_hora" VARCHAR(50) NOT NULL, "valor" NUMERIC NOT NULL);
CREATE TABLE incidencias_ruido ("id" INT NOT NULL, "fecha_y_hora" VARCHAR(50) NOT NULL, "valor" NUMERIC NOT NULL);
CREATE TABLE incidencias_calidad_de_aire ("id" INT NOT NULL, "fecha_y_hora" VARCHAR(50) NOT NULL, "valor" NUMERIC NOT NULL);
