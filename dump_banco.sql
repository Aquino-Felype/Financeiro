-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: financeiro
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bancos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` int NOT NULL,
  `nome` varchar(255) COLLATE utf8mb3_turkish_ci NOT NULL,
  `sigla` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `tipo` varchar(100) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,1,'Banco do Brasil S.A.','BB','Banco Múltiplo','2025-02-26 15:16:47'),(2,104,'Caixa Econômica Federal','CEF','Banco Múltiplo','2025-02-26 15:16:47'),(3,237,'Banco Bradesco S.A.','Bradesco','Banco Múltiplo','2025-02-26 15:16:47'),(4,341,'Itaú Unibanco S.A.','Itaú','Banco Múltiplo','2025-02-26 15:16:47'),(5,33,'Banco Santander (Brasil) S.A.','Santander','Banco Múltiplo','2025-02-26 15:16:47'),(6,745,'Banco Citibank S.A.','Citibank','Banco Múltiplo','2025-02-26 15:16:47'),(7,399,'HSBC Bank Brasil S.A. - Banco Múltiplo','HSBC','Banco Múltiplo','2025-02-26 15:16:47'),(8,422,'Banco Safra S.A.','Safra','Banco Múltiplo','2025-02-26 15:16:47'),(9,70,'Banco de Brasília S.A.','BRB','Banco Múltiplo','2025-02-26 15:16:47'),(10,389,'Banco Mercantil do Brasil S.A.','Mercantil','Banco Múltiplo','2025-02-26 15:16:47');
/*!40000 ALTER TABLE `bancos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracoes`
--

DROP TABLE IF EXISTS `configuracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracoes` (
  `id_configuracoes` int NOT NULL AUTO_INCREMENT,
  `tipo_meta` varchar(255) COLLATE utf8mb3_turkish_ci NOT NULL,
  `id_preferencia` int NOT NULL,
  PRIMARY KEY (`id_configuracoes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracoes`
--

LOCK TABLES `configuracoes` WRITE;
/*!40000 ALTER TABLE `configuracoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `configuracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagamentos`
--

DROP TABLE IF EXISTS `pagamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo_solicitacao` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `tipo_pagamento` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `filial` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `data_solicitacao` datetime DEFAULT NULL,
  `nome_cliente` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `numero_requisicao_cupom` varchar(100) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `comprovante` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `cod_fcerta` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `chave_pix` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `conta` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `banco` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `agencia` varchar(55) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `ultimos_digitos` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `motivo_estorno` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `numero_autorizacao` varchar(100) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `observacoes` varchar(100) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `identificacao_cliente` varchar(100) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `usuario_solicitante` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `usuario_financeiro_responsavel` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `data_aprovacao_rejeicao` datetime DEFAULT NULL,
  `comprovante_financeiro` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `situacao` varchar(100) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentos`
--

LOCK TABLES `pagamentos` WRITE;
/*!40000 ALTER TABLE `pagamentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `percentual`
--

DROP TABLE IF EXISTS `percentual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `percentual` (
  `id_percentual` int NOT NULL AUTO_INCREMENT,
  `valor_percentual` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_percentual`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `percentual`
--

LOCK TABLES `percentual` WRITE;
/*!40000 ALTER TABLE `percentual` DISABLE KEYS */;
/*!40000 ALTER TABLE `percentual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preferencias`
--

DROP TABLE IF EXISTS `preferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preferencias` (
  `id_preferencia` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(55) COLLATE utf8mb3_turkish_ci NOT NULL,
  PRIMARY KEY (`id_preferencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preferencias`
--

LOCK TABLES `preferencias` WRITE;
/*!40000 ALTER TABLE `preferencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `preferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_categorias`
--

DROP TABLE IF EXISTS `tbl_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb3_turkish_ci NOT NULL,
  `tipo` enum('R','D') COLLATE utf8mb3_turkish_ci NOT NULL,
  `operacional` enum('S','N') COLLATE utf8mb3_turkish_ci NOT NULL,
  `patrimonio_empresa` enum('S','N') COLLATE utf8mb3_turkish_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_categorias`
--

LOCK TABLES `tbl_categorias` WRITE;
/*!40000 ALTER TABLE `tbl_categorias` DISABLE KEYS */;
INSERT INTO `tbl_categorias` VALUES (7,'RECEITAS','R','S','N'),(8,'DEDUÇÕES','D','S','N'),(9,'FORNECEDORES','D','S','N'),(10,'VARIAVEIS','R','S','N'),(11,'DESPESAS COM PESSOAL','D','S','N'),(12,'DESPESAS IMOVEL','D','S','N'),(13,'MARKETING','R','S','N'),(14,'VEICULOS','D','S','N'),(15,'SERVIÇOS DE TERCEIROS','D','S','N'),(16,'DESPESAS GERAIS','D','S','N'),(17,'TRIBUTARIAS','D','S','N'),(18,'INVESTIMENTO','R','N','S'),(19,'FINANCEIRO','D','N','N'),(20,'OUTRAS RECEITAS','R','N','N'),(22,'MOBILIARIOS','R','N','S'),(23,'CONTRIBUIÇÃO','R','N','N');
/*!40000 ALTER TABLE `tbl_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_contas`
--

DROP TABLE IF EXISTS `tbl_contas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_contas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_conta` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `agencia` varchar(50) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `limite_inicial` decimal(10,2) DEFAULT NULL,
  `ativo` enum('S','N') COLLATE utf8mb3_turkish_ci DEFAULT 'S',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_contas`
--

LOCK TABLES `tbl_contas` WRITE;
/*!40000 ALTER TABLE `tbl_contas` DISABLE KEYS */;
INSERT INTO `tbl_contas` VALUES (6,'Conta Teste','001',1500.00,'S'),(7,'Conta Teste 1','002',2228.75,'S'),(8,'Conta Teste 2 ','002',1002.59,'S'),(11,'Conta Teste 3','003',10000.19,'S'),(12,'Conta Teste 4 ','004',10000.77,'S');
/*!40000 ALTER TABLE `tbl_contas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_despesa_fixa_mes`
--

DROP TABLE IF EXISTS `tbl_despesa_fixa_mes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_despesa_fixa_mes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_lancamento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `valor` float DEFAULT NULL,
  `pago` enum('S','N') DEFAULT NULL,
  `id_conta_despesa` int DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `mes_ano` varchar(10) DEFAULT NULL,
  `id_forma_pagamento` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_despesa_fixa_mes`
--

LOCK TABLES `tbl_despesa_fixa_mes` WRITE;
/*!40000 ALTER TABLE `tbl_despesa_fixa_mes` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_despesa_fixa_mes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_formas_pagamento`
--

DROP TABLE IF EXISTS `tbl_formas_pagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_formas_pagamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_forma_pagamento` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  `observacao` varchar(255) COLLATE utf8mb3_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_formas_pagamento`
--

LOCK TABLES `tbl_formas_pagamento` WRITE;
/*!40000 ALTER TABLE `tbl_formas_pagamento` DISABLE KEYS */;
INSERT INTO `tbl_formas_pagamento` VALUES (1,'Débito','A vista ou parcelado'),(2,'Crédito','TESTE'),(5,'dinheiro','taxfafga');
/*!40000 ALTER TABLE `tbl_formas_pagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fornecedores`
--

DROP TABLE IF EXISTS `tbl_fornecedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_fornecedores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sexo` varchar(1) DEFAULT NULL,
  `nome` varchar(50) DEFAULT '0',
  `sobrenome` varchar(255) DEFAULT NULL,
  `data_de_nascimento` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cep` varchar(255) DEFAULT NULL,
  `id_cidade` int DEFAULT NULL,
  `id_estado` int DEFAULT NULL,
  `pais` varchar(255) DEFAULT 'Brasil',
  `telefone` varchar(255) DEFAULT NULL,
  `newsletter` varchar(255) DEFAULT 'Sim',
  `senha` varchar(255) DEFAULT NULL,
  `cpf` varchar(100) DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `celular` varchar(15) DEFAULT NULL,
  `ddd_telefone` varchar(4) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `ddd_celular` varchar(255) DEFAULT NULL,
  `ativo` varchar(255) DEFAULT 'S',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `nome_fantasia` varchar(200) DEFAULT NULL,
  `cnpj` varchar(30) DEFAULT NULL,
  `tipo_pessoa` enum('F','J') DEFAULT 'F',
  `id_vinculo` int DEFAULT NULL,
  `foto` varchar(70) DEFAULT NULL,
  `cod_banco` int DEFAULT NULL,
  `agencia` varchar(10) DEFAULT NULL,
  `conta` varchar(20) DEFAULT NULL,
  `tipo_conta` enum('corrente','poupanca') DEFAULT NULL,
  `operacao` int DEFAULT NULL,
  `variacao` int DEFAULT NULL,
  `qtd_erros_senhas` int DEFAULT '0',
  `senha_bloqueada` enum('S','N') DEFAULT 'N',
  `rg_ssp` varchar(20) DEFAULT NULL,
  `instagram` varchar(150) DEFAULT NULL,
  `facebook` varchar(150) DEFAULT NULL,
  `inscricao_estadual` varchar(100) DEFAULT NULL,
  `departamentos` varchar(255) DEFAULT NULL,
  `data_de_pagamento` date DEFAULT NULL,
  `tipo_fornecedor` int DEFAULT NULL,
  `pix_fornecedor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fornecedores`
--

LOCK TABLES `tbl_fornecedores` WRITE;
/*!40000 ALTER TABLE `tbl_fornecedores` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_fornecedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_funcionarios`
--

DROP TABLE IF EXISTS `tbl_funcionarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_funcionarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sexo` varchar(1) DEFAULT NULL,
  `nome` varchar(50) DEFAULT '0',
  `sobrenome` varchar(255) DEFAULT NULL,
  `data_de_nascimento` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cep` varchar(255) DEFAULT NULL,
  `id_cidade` int DEFAULT NULL,
  `id_estado` int DEFAULT NULL,
  `pais` varchar(255) DEFAULT 'Brasil',
  `telefone` varchar(255) DEFAULT NULL,
  `newsletter` varchar(255) DEFAULT 'Sim',
  `senha` varchar(255) DEFAULT NULL,
  `cpf` varchar(100) DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `celular` varchar(15) DEFAULT NULL,
  `ddd_telefone` varchar(4) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `ddd_celular` varchar(255) DEFAULT NULL,
  `ativo` varchar(255) DEFAULT 'S',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `nome_fantasia` varchar(200) DEFAULT NULL,
  `cnpj` varchar(30) DEFAULT NULL,
  `tipo_pessoa` enum('F','J') DEFAULT 'F',
  `id_vinculo` int DEFAULT NULL,
  `foto` varchar(70) DEFAULT NULL,
  `cod_banco` int DEFAULT NULL,
  `agencia` varchar(10) DEFAULT NULL,
  `conta` varchar(20) DEFAULT NULL,
  `tipo_conta` enum('corrente','poupanca') DEFAULT NULL,
  `operacao` int DEFAULT NULL,
  `variacao` int DEFAULT NULL,
  `qtd_erros_senhas` int DEFAULT '0',
  `senha_bloqueada` enum('S','N') DEFAULT 'N',
  `rg_ssp` varchar(20) DEFAULT NULL,
  `instagram` varchar(150) DEFAULT NULL,
  `facebook` varchar(150) DEFAULT NULL,
  `inscricao_estadual` varchar(100) DEFAULT NULL,
  `departamentos` varchar(255) DEFAULT NULL,
  `data_de_pagamento` date DEFAULT NULL,
  `pix` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_funcionarios`
--

LOCK TABLES `tbl_funcionarios` WRITE;
/*!40000 ALTER TABLE `tbl_funcionarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_funcionarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_menus`
--

DROP TABLE IF EXISTS `tbl_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `nome_singular` varchar(50) DEFAULT NULL,
  `temCat` enum('S','N') DEFAULT NULL,
  `temPreco` enum('S','N') DEFAULT NULL,
  `descricao` enum('S','N') DEFAULT NULL,
  `breve` enum('S','N') DEFAULT NULL,
  `temLink` enum('S','N') DEFAULT NULL,
  `temDestaque` enum('S','N') DEFAULT NULL,
  `temOrdem` enum('S','N') DEFAULT NULL,
  `temBreve2` enum('S','N') DEFAULT NULL,
  `vinculos` varchar(100) DEFAULT NULL,
  `ftsAdds` enum('S','N') DEFAULT NULL,
  `classBtVinculo` enum('bt39px','bt44px','bt55px','bt63px','bt71px','bt86px','bt81px','bt77px','bt96px','bt100px','bt105px','bt114px','bt124px','bt134px','bt144px','bt154px') DEFAULT NULL,
  `mostraEmIdMenu` varchar(50) DEFAULT NULL,
  `temPromocao` enum('S','N') DEFAULT NULL,
  `temComentarios` enum('S','N') DEFAULT NULL,
  `temData` enum('S','N') DEFAULT NULL,
  `temDataHora` enum('S','N') DEFAULT NULL,
  `nomeBrevePersonalizado` varchar(50) DEFAULT NULL,
  `exibeBreveNaLista` enum('S','N') DEFAULT 'S',
  `catTemDestaque` enum('S','N') DEFAULT 'N',
  `nomeTituloPersonalizado` varchar(100) DEFAULT NULL,
  `mostrarNaLista` enum('S','N') DEFAULT 'S',
  `somente_idioma_id` tinyint DEFAULT NULL,
  `somente_grupo` enum('S','N') DEFAULT 'N',
  `escolher_idiomas` enum('S','N') DEFAULT 'N',
  `mostrarIdFiltroNaColuna` varchar(15) DEFAULT NULL,
  `temArquivos` enum('S','N') DEFAULT 'N',
  `imagemNosIdiomas` enum('S','N') DEFAULT 'S',
  `temArquivo` enum('S','N') DEFAULT 'N',
  `temImagem` enum('S','N') DEFAULT 'S',
  `nomeBreve2Personalizado` varchar(50) DEFAULT NULL,
  `temEstado` enum('S','N') DEFAULT 'N',
  `temCidade` enum('S','N') DEFAULT 'N',
  `botaoAvulsoNome` varchar(50) DEFAULT NULL,
  `botaoAvulsoTarget` enum('_blank','_parent','_self') DEFAULT NULL,
  `botaoAvulsoLink` varchar(200) DEFAULT NULL,
  `botaoAvulsoClass` varchar(20) DEFAULT NULL,
  `pergQuerEnviarArquivos` enum('S','N') DEFAULT 'S',
  `temSubCat` enum('S','N') DEFAULT NULL,
  `temTitulo` enum('S','N') DEFAULT 'S',
  `catTemImagem` enum('S','N') DEFAULT 'N',
  `exibeBreveIdFiltro` enum('S','N') DEFAULT NULL,
  `exibeBreveDoConteudoNoFiltro` enum('S','N') DEFAULT NULL,
  `tipoCampoBreve` enum('input','textarea','date','cor') DEFAULT 'input',
  `tipoCampoBreve2` enum('input','textarea') DEFAULT 'textarea',
  `tipoCampoTitulo` enum('input','textarea') DEFAULT 'input',
  `opcaoIncluirExcluir` enum('S','N') DEFAULT 'S',
  `mostraLinkDescricao` enum('S','N') DEFAULT NULL,
  `fortawesome_icone` enum('S','N') DEFAULT NULL,
  `exibeBreve2DoConteudoNoFiltro` enum('S','N') DEFAULT NULL,
  `marcaDagua` enum('S','N') DEFAULT NULL,
  `temAtivo` enum('S','N') DEFAULT NULL,
  `pergQuerEnviarImagens` enum('S','N') DEFAULT NULL,
  `linkRedirecionaEditar` varchar(150) DEFAULT NULL,
  `linkRedirecionaIncluir` varchar(200) DEFAULT NULL,
  `msgRedirecionaIncluir` varchar(150) DEFAULT NULL,
  `catTemOrdem` enum('S','N') DEFAULT NULL,
  `trocarCodPorData` enum('S') DEFAULT NULL,
  `temBreve3` enum('S') DEFAULT NULL,
  `nomeBreve3Personalizado` varchar(80) DEFAULT NULL,
  `tipoCampoBreve3` enum('input','textarea') DEFAULT NULL,
  `nome_arquivo` varchar(50) DEFAULT NULL,
  `fortawesome_icone_cat` enum('S') DEFAULT NULL,
  `temCliente` enum('S','N') DEFAULT NULL,
  `select_adicional` enum('S','N') DEFAULT NULL,
  `nome_select_adicional` varchar(30) DEFAULT NULL,
  `catTemBreve` enum('S','N') DEFAULT NULL,
  `nomeCampoData` varchar(50) DEFAULT NULL,
  `temPrecoDe` enum('S') DEFAULT NULL,
  `lista` enum('S') DEFAULT 'S',
  `temVinculoCliente` enum('S','N') DEFAULT NULL,
  `nome_vinculo_cliente` varchar(100) DEFAULT NULL,
  `opcoesAdicionaisCliente` enum('S','N') DEFAULT NULL,
  `incluir_mais` enum('S','N') DEFAULT NULL,
  `enviar_notificacao_email` enum('S') DEFAULT NULL,
  `busca_id_filtro` varchar(50) DEFAULT NULL,
  `mostrar_em_produtos` enum('S') DEFAULT NULL,
  `vinculos_produtos` enum('S') DEFAULT NULL,
  `exibe_data_listagem` enum('S') DEFAULT NULL,
  `mostrar_cat_na_lista` enum('S') DEFAULT NULL,
  `mostra_nome_cliente_lista` enum('S') DEFAULT NULL,
  `volta_pro_id` enum('S') DEFAULT NULL,
  `nome_imagem` varchar(100) DEFAULT NULL,
  `nome_arquivo_2` varchar(50) DEFAULT NULL,
  `filtro_por_data` enum('S','N') DEFAULT 'S',
  `desc_botao_ativo_no_site` varchar(100) DEFAULT NULL,
  `desc_campo_descricao` varchar(100) DEFAULT NULL,
  `mostra_select_adicional_lista` enum('S','N') DEFAULT NULL,
  `ordem` int DEFAULT NULL,
  `order_by` varchar(50) DEFAULT 'tbl_noticias.id desc',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=latin1 COMMENT='Começar menor ID com 10';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_menus`
--

LOCK TABLES `tbl_menus` WRITE;
/*!40000 ALTER TABLE `tbl_menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_movimento_caixa`
--

DROP TABLE IF EXISTS `tbl_movimento_caixa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_movimento_caixa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_conta_pai` int DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `data` date DEFAULT NULL,
  `tipo` enum('R','D') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pago_a` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_forma_pagamento` int DEFAULT NULL,
  `anexo_despesas` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_parcela` int DEFAULT NULL,
  `parcela_atual` int DEFAULT NULL,
  `id_categoria` int DEFAULT NULL,
  `pago` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_subcategoria` int DEFAULT NULL,
  `valor_pago` int DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `desconto` float DEFAULT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `juros` float DEFAULT NULL,
  `quitado` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_conta_banco` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_movimento_caixa`
--

LOCK TABLES `tbl_movimento_caixa` WRITE;
/*!40000 ALTER TABLE `tbl_movimento_caixa` DISABLE KEYS */;
INSERT INTO `tbl_movimento_caixa` VALUES (4,NULL,'Felype T.I (1/3) (1/6)',1388.8333333333333,'2025-02-27','D',NULL,1,NULL,6,1,8,'S',130,8333,'2025-02-27',0,'2025-02-27 15:10:16',0,NULL,6),(5,4,'Felype T.I (1/3) (2/6)',1388.8333333333333,'2025-03-27','D',NULL,1,NULL,6,2,8,'N',130,NULL,NULL,0,'2025-02-27 15:10:16',NULL,NULL,6),(6,4,'Felype T.I (1/3) (3/6)',1388.8333333333333,'2025-04-27','D',NULL,1,NULL,6,3,8,'N',130,NULL,NULL,0,'2025-02-27 15:10:16',NULL,NULL,6),(7,4,'Felype T.I (1/3) (4/6)',1388.8333333333333,'2025-05-27','D',NULL,1,NULL,6,4,8,'N',130,NULL,NULL,0,'2025-03-06 14:12:01',NULL,NULL,6),(8,4,'Felype T.I (1/3) (5/6)',1388.8333333333333,'2025-06-27','D',NULL,1,NULL,6,5,8,'N',130,NULL,NULL,0,'2025-03-06 14:12:01',NULL,NULL,6),(9,4,'Felype T.I (1/3) (6/6)',1388.8333333333333,'2025-07-27','D',NULL,1,NULL,6,6,8,'N',130,NULL,NULL,0,'2025-03-06 14:12:01',NULL,NULL,6);
/*!40000 ALTER TABLE `tbl_movimento_caixa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_noticias`
--

DROP TABLE IF EXISTS `tbl_noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_noticias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) DEFAULT NULL,
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `noticia` longtext,
  `categoria` int DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `destaque` enum('S') DEFAULT NULL,
  `arquivo` varchar(100) DEFAULT NULL,
  `promocao` enum('S','N') DEFAULT NULL,
  `idMenu` int DEFAULT NULL,
  `horario_fim` varchar(100) DEFAULT NULL,
  `tipo` varchar(40) DEFAULT NULL,
  `principal` varchar(1) DEFAULT NULL,
  `principal2` enum('S') DEFAULT NULL,
  `id_cliente` int DEFAULT NULL,
  `de` double DEFAULT NULL,
  `por` double DEFAULT NULL,
  `breve` text,
  `link` varchar(255) DEFAULT NULL,
  `breve2` text,
  `ordem` smallint DEFAULT NULL,
  `id_idioma` int DEFAULT NULL,
  `id_conteudoPrincipal` int DEFAULT NULL,
  `ultimaAtualizacao` datetime DEFAULT NULL,
  `id_estado` smallint DEFAULT NULL,
  `id_cidade` smallint DEFAULT NULL,
  `id_subcat` int DEFAULT NULL,
  `fortawesome_icone` varchar(50) DEFAULT NULL,
  `ativo` enum('S','N') DEFAULT 'S',
  `breve3` text,
  `id_select_adicional` int DEFAULT NULL,
  `visitas` int DEFAULT '0',
  `arquivo_2` varchar(70) DEFAULT NULL,
  `bloquear_excluir` enum('S','N') DEFAULT NULL,
  `id_relaciona_parcelas` int DEFAULT NULL,
  `categorias_financeiro` int DEFAULT NULL,
  `mostra_dre` enum('S') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_noticias`
--

LOCK TABLES `tbl_noticias` WRITE;
/*!40000 ALTER TABLE `tbl_noticias` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_noticias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_status`
--

DROP TABLE IF EXISTS `tbl_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(255) NOT NULL DEFAULT '',
  `cor` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_status`
--

LOCK TABLES `tbl_status` WRITE;
/*!40000 ALTER TABLE `tbl_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_subcategorias`
--

DROP TABLE IF EXISTS `tbl_subcategorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_subcategorias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria` int DEFAULT NULL,
  `nome_subcategoria` varchar(255) DEFAULT NULL,
  `ordem` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=247 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_subcategorias`
--

LOCK TABLES `tbl_subcategorias` WRITE;
/*!40000 ALTER TABLE `tbl_subcategorias` DISABLE KEYS */;
INSERT INTO `tbl_subcategorias` VALUES (11,7,'Vendas Terceirizadas',0),(10,7,'Vendas',0),(16,10,'TAXA DE ROYALTIES',0),(17,10,'COMISSÕES PRODUÇÃO',0),(18,10,'COMISSÕES COMERCIAL',0),(19,10,'COMISSÕES ADMINISTRAÇÃO',0),(20,10,'BONUS CAMPANHA SOBRE VENDAS',0),(21,10,'FRETE SOBRE AS VENDAS',0),(22,10,'TAXAS DE CARTÃO DE CREDITO',0),(23,11,'SALARIOS',0),(24,11,'PRO-LABORE',0),(25,11,'PROVISAO DE FERIAS',0),(27,11,'INSS',0),(28,11,'FGTS',0),(29,11,'ASSISTENCIA MÉDICA',0),(30,11,'VALE TRANSPORTE',0),(31,11,'LANCHES',0),(32,11,'INDENIZACOES E RESCISOES',0),(33,11,'UNIFORMES',0),(34,11,'EPIS',0),(35,11,'TREINAMENTOS/CURSOS',0),(36,11,'CUSTOS ADMISSIONAIS',0),(37,11,'CUSTOS DEMISSIONAIS',0),(39,12,'ENERGIA',0),(40,12,'DESPESAS COM IMOVEL',0),(41,12,'AGUA ',0),(42,12,'TELEFONE MÓVEL',0),(43,12,'TELEFONE FIXO',0),(44,12,'INTERNET',0),(45,12,'ALUGUEL',0),(46,12,'SERVIÇOS DE LIMPEZA',0),(47,12,'CONDOMINIO',0),(48,12,'SEGUROS',0),(49,12,'IPTU (PROVISÃO)',0),(50,12,'CONSERV E MANUT DE INSTALACOES',0),(51,13,'MARKETING DIGITAL',0),(52,13,'MARKETING PDV',0),(53,13,'MARKETING MÉDICO',0),(54,13,'TAXA DE MARKETING',0),(55,13,'EVENTOS E FEIRAS',0),(56,13,'SERVIÇOS DE PANFLETAGEM',0),(57,13,'ENDOMARKETING',0),(58,13,'BRINDES E DOAÇÕES',0),(59,14,'COMBUSTIVEIS',0),(60,14,'MANUTENÇÃO',0),(61,14,'SEGUROS (PROPRIEDADE DA EMPRESA)',0),(62,14,'IPVA (PROPRIEDADE DA EMPRESA)',0),(63,15,'HONORARIOS CONTABEIS',0),(64,15,'SERVICOS DE LIMPEZA E CONSERVACAO',0),(65,15,'CONTRATO MANUTENCAO ALARMES E SEGURANÇA',0),(66,15,'DEDETIZAÇÃO',0),(67,15,'LAVAGEM DE UNIFORMES',0),(68,15,'CALIBRAÇÕES DE EQUIPAMENTOS',0),(69,15,'SERVICOS DE ANALISES TECNICAS',0),(70,15,'CONSULTORIAS/ASSESSORIA',0),(71,15,'HONORARIOS ADVOCATICIOS',0),(72,15,'SERVICOS CONSULTA SPC E SERASA',0),(73,15,'MANUTENÇÃO DE EQUIPAMENTOS TELEFONICOS',0),(74,15,'MANUTENÇÃO DE AR CONDICIONADO',0),(75,15,'MANUTENÇÃO DE INFORMATICA',0),(76,15,'MANUTENÇÃO DOS EQUIPAMENTOS DO LABORÁTORIO',0),(77,15,'SERVIÇOS PRESTADOS - PF',0),(78,15,'MENSALIDADE SISTEMA FORMULA CERTA',0),(79,15,'SERVIÇOS PRESTADOS - PJ',0),(80,16,'MATERIAL DE ESCRITORIO/PAPELARIA',0),(81,16,'MATERIAL DE COSUMO DO LABORATÓRIO',0),(82,16,'MATERIAIS DE USO E CONSUMO GERAL',0),(83,16,'MATERIAIS DE LIMPEZA/ HIGIENE',0),(84,16,'EMBALAGENS - SACOLAS/ETIQUETAS',0),(85,16,'CORREIOS/TRANSPORTES',0),(86,16,'CARTORIO',0),(87,16,'GRAFICA E IMPRESSÕES',0),(88,16,'ASSINATURA JORNAIS,REVISTAS E PUBLICACOES',0),(89,16,'SINDICATO/ASSOCIAÇÕES/ANVISA',0),(90,16,'VIAGENS E ESTADIAS',0),(91,16,'AQUISICAO BENS DE PEQUENO VALOR',0),(92,16,'MATERIAL DE INFORMATICA',0),(93,16,'MATERIAIS E SERVICOS ELETRICOS/HIDRAULICO',0),(94,16,'ALVARÁS/BOMBEIRO/VIGILÂNCIA',0),(95,16,'SEGURO DE VIDA EM GRUPO',0),(96,16,'MENSALIDADE CARTÃO DE CREDITO',0),(97,16,'DIVERSOS - DESPESAS GERAIS',0),(98,16,'COMBUSTIVEIS E LUBRIFICANTES',0),(99,16,'REFEIÇÃO',0),(100,16,'CONFRATERNIZACAO',0),(101,17,'IMPOSTO MUNICIPAL',0),(102,17,'IMPOSTO ESTADUAL',0),(103,17,'IMPOSTO FEDERAL',0),(104,17,'IMPOSTOS DIVERSOS',0),(105,18,'EDIFICAÇÕES/PREDIOS',0),(106,18,'CAPITAL SICOOB',0),(107,18,'COMPUTADORES E PERIFERICOS',0),(246,23,'IRPJ/IRRF',0),(129,7,'VENDAS TERCEIRIZADAS',0),(128,7,'VENDAS',0),(130,8,'DEDUÇÕES - SIMPLES NACIONAL',0),(131,8,'DEVOLUÇÕES',0),(132,9,'FORNECEDORES',0),(133,9,'CMV - TERCEIRIZADAS',0),(134,10,'TAXA DE ROYALTIES',0),(135,10,'COMISSÕES PRODUÇÃO',0),(136,10,'COMISSÕES COMERCIAL',0),(137,10,'COMISSÕES ADMINISTRAÇÃO',0),(138,10,'BONUS CAMPANHA SOBRE VENDAS',0),(139,10,'FRETE SOBRE AS VENDAS',0),(140,10,'TAXAS DE CARTÃO DE CREDITO',0),(141,11,'SALARIOS',0),(142,11,'PRO-LABORE',0),(144,11,'PROVISÃO 13° SALARIO',0),(145,11,'INSS',0),(146,11,'FGTS',0),(147,11,'ASSISTENCIA MÉDICA',0),(148,11,'VALE TRANSPORTE',0),(149,11,'LANCHES',0),(151,11,'UNIFORMES',0),(153,11,'TREINAMENTOS/CURSOS',0),(156,11,'OUTROS BENEFICIOS',0),(157,12,'ENERGIA',0),(158,12,'DESPESAS COM IMOVEL',0),(159,12,'AGUA',0),(160,12,'TELEFONE MÓVEL',0),(161,12,'TELEFONE FIXO',0),(162,12,'INTERNET',0),(163,12,'ALUGUEL',0),(164,12,'SERVIÇOS DE LIMPEZA',0),(165,12,'CONDOMINIO',0),(166,12,'SEGUROS',0),(167,12,'IPTU (PROVISÃO)',0),(168,12,'CONSERV E MANUTENÇÃO DE INSTALAÇÕES',0),(169,13,'MARKETING DIGITAL',0),(170,13,'MARKETING PDV',0),(171,13,'MARKETING MÉDICO',0),(172,13,'TAXA DE MARKETING ',0),(173,13,'EVENTOS E FEIRAS',0),(174,13,'SERVIÇOS DE PANFLETAGEM',0),(175,13,'ENDOMARKETING',0),(176,13,'BRINDES E DOAÇÕES',0),(177,14,'MANUTENÇÃO',0),(178,14,'COMBUSTIVEIS',0),(179,14,'SEGUROS (PROPRIEDADE DA EMPRESA)',0),(180,14,'IPVA (PROPRIEDADE DA EMPRESA)',0),(181,15,'HONORARIOS CONTABEIS',0),(182,15,'SERVICOS DE LIMPEZA E CONSERVACAO',0),(183,15,'CONTRATO MANUTENCAO ALARMES E SEGURANÇA',0),(184,15,'DEDETIZAÇÃO',0),(185,15,'LAVAGEM DE UNIFORMES',0),(186,15,'CALIBRAÇÕES DE EQUIPAMENTOS',0),(187,15,'SERVICOS DE ANALISES TECNICAS',0),(188,15,'CONSULTORIAS/ASSESSORIA',0),(189,15,'HONORARIOS ADVOCATICIOS',0),(190,15,'SERVICOS CONSULTA SPC E SERASA',0),(191,15,'MANUTENÇÃO DE EQUIPAMENTOS TELEFONICOS',0),(192,15,'MANUTENÇÃO DE AR CONDICIONADO',0),(193,15,'MANUTENÇÃO DE INFORMATICA',0),(194,15,'MANUTENÇÃO DOS EQUIPAMENTOS DO LABORÁTORIO',0),(195,15,'SERVIÇOS PRESTADOS - PF',0),(196,15,'MENSALIDADE SISTEMA FORMULA CERTA',0),(197,15,'SERVIÇOS PRESTADOS - PJ',0),(198,16,'MATERIAL DE ESCRITORIO/PAPELARIA',0),(199,16,'MATERIAL DE COSUMO DO LABORATÓRIO',0),(200,16,'MATERIAIS DE USO E CONSUMO GERAL',0),(201,16,'MATERIAIS DE LIMPEZA/ HIGIENE',0),(202,16,'EMBALAGENS - SACOLAS/ETIQUETAS',0),(203,16,'CORREIOS/TRANSPORTES',0),(204,16,'CARTORIO',0),(205,16,'GRAFICA E IMPRESSÕES',0),(206,16,'ASSINATURA JORNAIS,REVISTAS E PUBLICACOES',0),(207,16,'SINDICATO/ASSOCIAÇÕES/ANVISA',0),(208,16,'VIAGENS E ESTADIAS',0),(209,16,'AQUISICAO BENS DE PEQUENO VALOR',0),(210,16,'MATERIAL DE INFORMATICA',0),(211,16,'MATERIAIS E SERVICOS ELETRICOS/HIDRAULICO',0),(212,16,'ALVARÁS/BOMBEIRO/VIGILÂNCIA',0),(213,16,'SEGURO DE VIDA EM GRUPO',0),(214,16,'MENSALIDADE CARTÃO DE CREDITO',0),(215,16,'DIVERSOS - DESPESAS GERAIS',0),(216,16,'COMBUSTIVEIS E LUBRIFICANTES',0),(217,16,'REFEIÇÃO',0),(218,16,'CONFRATERNIZACAO',0),(219,17,'IMPOSTO MUNICIPAL',0),(220,17,'IMPOSTO ESTADUAL',0),(221,17,'IMPOSTO FEDERAL',0),(222,17,'IMPOSTOS DIVERSOS',0),(223,18,'MOVEIS E UTENSILIOS',0),(224,18,'EDIFICAÇÕES/PREDIOS',0),(225,18,'CAPITAL SICOOB',0),(226,18,'COMPUTADORES E PERIFERICOS',0),(227,19,'PAGAMENTO DE EMPRESTIMOS',0),(228,19,'TARIFAS BANCARIAS',0),(229,19,'IOF',0),(230,19,'JUROS',0),(231,19,'RETIRADA DOS SOCIOS (MAXIMO DE 50% DO LUCRO OPERACIONAL)',0),(232,19,'JUROS SOBRE ANTECIPAÇÕES CARTÃO',0),(233,19,'PARCELAMENTO SIMPLES',0),(234,20,'ENTRADA DE APLICAÇÕES FINANCEIRAS',0),(243,20,'JUROS DA APLICAÇÃO',0),(236,20,'JUROS CLIENTES (ATRASO)',0),(237,20,'EMPRESTIMOS',0),(244,23,'CONTRIBUICAO SOBRE LUCRO',0),(240,22,'AQUISIÇÃO IMOB',0),(241,22,'MAQ E EQUIPAMENTOS',0),(242,22,'AQUISIÇÃO IMOB - IMOVEIS',0);
/*!40000 ALTER TABLE `tbl_subcategorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transferencias`
--

DROP TABLE IF EXISTS `tbl_transferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_transferencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_conta_banco_origem` int NOT NULL,
  `id_conta_banco_destino` int NOT NULL,
  `valor` double DEFAULT NULL,
  `saldo_origem_momento` decimal(10,2) DEFAULT NULL,
  `saldo_destino_momento` decimal(10,2) DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transferencias`
--

LOCK TABLES `tbl_transferencias` WRITE;
/*!40000 ALTER TABLE `tbl_transferencias` DISABLE KEYS */;
INSERT INTO `tbl_transferencias` VALUES (1,6,6,25000,1505.25,1505.25,'2025-03-06 00:00:00'),(2,7,8,2,2225.50,1000.59,'2025-03-06 00:00:00'),(3,6,7,5.25,1505.25,2223.50,'2025-03-06 00:00:00');
/*!40000 ALTER TABLE `tbl_transferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sexo` varchar(1) DEFAULT NULL,
  `nome` varchar(50) DEFAULT '0',
  `sobrenome` varchar(255) DEFAULT NULL,
  `data_de_nascimento` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cep` varchar(255) DEFAULT NULL,
  `id_cidade` int DEFAULT NULL,
  `id_estado` int DEFAULT NULL,
  `pais` varchar(255) DEFAULT 'Brasil',
  `telefone` varchar(255) DEFAULT NULL,
  `newsletter` varchar(255) DEFAULT 'Sim',
  `senha` varchar(255) DEFAULT NULL,
  `cpf` varchar(100) DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `celular` varchar(15) DEFAULT NULL,
  `ddd_telefone` varchar(4) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `ddd_celular` varchar(255) DEFAULT NULL,
  `ativo` varchar(255) DEFAULT 'S',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `nome_fantasia` varchar(200) DEFAULT NULL,
  `cnpj` varchar(30) DEFAULT NULL,
  `tipo_pessoa` enum('F','J') DEFAULT 'F',
  `id_vinculo` int DEFAULT NULL,
  `foto` varchar(70) DEFAULT NULL,
  `cod_banco` int DEFAULT NULL,
  `agencia` varchar(10) DEFAULT NULL,
  `conta` varchar(20) DEFAULT NULL,
  `tipo_conta` enum('corrente','poupanca') DEFAULT NULL,
  `operacao` int DEFAULT NULL,
  `variacao` int DEFAULT NULL,
  `qtd_erros_senhas` int DEFAULT '0',
  `senha_bloqueada` enum('S','N') DEFAULT 'N',
  `rg_ssp` varchar(20) DEFAULT NULL,
  `instagram` varchar(150) DEFAULT NULL,
  `facebook` varchar(150) DEFAULT NULL,
  `inscricao_estadual` varchar(100) DEFAULT NULL,
  `pix` varchar(255) NOT NULL,
  `tributacao` int DEFAULT NULL,
  `anexo_cliente` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=504 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-07  6:58:29
