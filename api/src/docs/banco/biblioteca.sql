DROP SCHEMA IF EXISTs `biblioteca`;
CREATE SCHEMA IF NOT EXISTS `biblioteca` DEFAULT CHARACTER SET utf8 ;
USE `biblioteca` ;
CREATE TABLE IF NOT EXISTS `biblioteca`.`Autor` (
	idAutor int auto_increment primary key,
    nomeAutor varchar(255),
    nacionalidade varchar(255),
    anoNascimento int)
ENGINE=InnoDB;

INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('1', 'HP LOVECRAFT', 'Estadunidense', '1890');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('2', 'J R R TOLKIEN', 'Sul africano', '1892');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('3', 'LEWIS CARROLL', 'Britânico', '1832');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('4', 'MACHADO DE ASSIS', 'Brasileiro', '1839');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('5', 'JOSÉ DE ALENCAR', 'Brasileiro', '1829');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('6', 'MAURICE LEBLANC', 'Frances', '1864');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('7', 'MIGUEL DE CERVANTES', 'Espanhol', '1547');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('8', 'SHAKESPEARE', 'Britânico', '1564');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('9', 'STEVE DITKO', 'Estadunidense', '1927');
INSERT INTO `Biblioteca`.`Autor` (`idAutor`, `nomeAutor`, `nacionalidade`, `anoNascimento`) VALUES ('10', 'EMILY BRONTE', 'Britânica', '1818');

CREATE TABLE IF NOT EXISTS `biblioteca`.`Categoria` (
	idCategoria int auto_increment primary key,
    nomeCategoria varchar(255))
ENGINE=InnoDB;

INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('1', 'Lírico');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('2', 'Épico');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('3', 'Drama');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('4', 'Romance');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('5', 'Ficção');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('6', 'Conto');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('7', 'Crônica');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('8', 'Poema');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('9', 'Suspense');
INSERT INTO `Biblioteca`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES ('10', 'Fantasia');

CREATE TABLE IF NOT EXISTS `biblioteca`.`Livro` (
	idLivro int auto_increment primary key,
    nomeLivro varchar(255),
    dataLancamento date,
    idAutor int,
    idCategoria int,
    foreign key (idAutor) references Autor(idAutor),
    foreign key (idCategoria) references Categoria(idCategoria)
) ENGINE=InnoDB;

INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('1', 'Alice no País das Maravilhas', '1864-11-26', '3', '10');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('2', 'O Chamado de Cthulu', '1928-02-01', '1', '9');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('3', 'O Senhor dos Anéis', '1954-07-29', '2', '10');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('4', 'Dom Casmurro', '1899-01-01', '4', '4');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('5', 'Iracema', '1865-01-01', '5', '4');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('6', 'Arsène Lupin contra Herlock Sholmès', '1910-01-01', '6', '5');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('7', 'Dom Quixote de la Mancha', '1605-01-01', '7', '4');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('8', 'Hamlet', '1623-01-01', '8', '3');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('9', 'O Fantástico Homem-Aranha', '2012-07-06', '9', '5');
INSERT INTO `Biblioteca`.`Livro` (`idLivro`, `nomeLivro`, `dataLancamento`, `idAutor`, `idCategoria`) VALUES ('10', 'Morro dos Ventos Uivantes', '1847-11-24', '10', '4');