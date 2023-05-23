CREATE
DATABASE IF NOT EXISTS apilaravel;
USE
apilaravel;

CREATE TABLE users
(
    id             int auto_increment not null primary key,
    role           varchar(20),
    name           varchar(255),
    surname        varchar(255),
    password       varchar(255),
    created_at     datetime DEFAULT NULL,
    updated_at     datetime DEFAULT NULL,
    remember_token varchar(255)
)ENGINE=InnoDb;

CREATE TABLE cars
(
    id          int auto_increment not null primary key,
    user_id     int not null,
    title       varchar(255),
    description text,
    status      varchar(30),
    price       varchar(30),
    created_at  datetime DEFAULT NULL,
    updated_at  datetime DEFAULT NULL,

    CONSTRAINT fk_cars_users FOREIGN KEY (user_id) REFERENCES users (id)
)ENGINE=InnoDb;
