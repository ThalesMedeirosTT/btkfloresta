-- SQL inicial para criar o banco e tabelas
CREATE DATABASE IF NOT EXISTS floresta_muaythai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE floresta_muaythai;

CREATE TABLE IF NOT EXISTS alunos (
  id CHAR(36) PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  data_nascimento DATE NOT NULL,
  celular VARCHAR(15) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS templates_aulas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dia_semana TINYINT UNSIGNED NOT NULL CHECK (dia_semana BETWEEN 0 AND 7),
  hora TIME NOT NULL,
  professor VARCHAR(100) NOT NULL,
  capacidade INT UNSIGNED DEFAULT 20,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS aulas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  data DATE NOT NULL,
  hora TIME NOT NULL,
  professor VARCHAR(100),
  capacidade INT NOT NULL,
  template_id INT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (template_id) REFERENCES templates_aulas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS agendamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aluno_id CHAR(36) NOT NULL,
  aula_id INT NOT NULL,
  status ENUM('confirmado','espera') NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
  FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
);
