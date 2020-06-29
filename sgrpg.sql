CREATE DATABASE sgrpg;
USE sgrpg;


CREATE TABLE User(
	id integer AUTO_INCREMENT,
	lv integer,
	exp integer,
	money integer,
	
	PRIMARY KEY(id)
);

CREATE TABLE Chara(
	id integer AUTO_INCREMENT,
	name varchar(64),
	
	PRIMARY KEY(id)
);

CREATE TABLE UserChara(
	id integer AUTO_INCREMENT,
	user_id integer,
	chara_id integer,
	
	PRIMARY KEY(id)
);
