SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `ar_metadata` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `ar_metadata`;

DROP TABLE IF EXISTS `annotations`;
CREATE TABLE `annotations` (
  `ID` int(10) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `items_iana_UUID` varchar(250) NOT NULL,
  `items_dc_identifier` varchar(50) NOT NULL DEFAULT '',
  `dc_references` varchar(250) NOT NULL,
  `reg_uri` varchar(250) NOT NULL,
  `rdfs_label` varchar(250) NOT NULL,
  `value_string` varchar(250) NOT NULL DEFAULT '',
  `value_uri` varchar(250) NOT NULL,
  `resource_uri` varchar(250) NOT NULL,
  `dct_contributor` varchar(255) NOT NULL,
  `dc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 13312 kB';

DROP TABLE IF EXISTS `audit`;
CREATE TABLE `audit` (
  `ID` int(10) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `items_iana_UUID` varchar(250) NOT NULL,
  `items_dc_identifier` varchar(50) NOT NULL DEFAULT '',
  `dc_references` varchar(250) NOT NULL,
  `reg_uri` varchar(250) NOT NULL,
  `rdfs_label` varchar(250) NOT NULL,
  `value_string` varchar(250) NOT NULL DEFAULT '',
  `value_uri` varchar(250) NOT NULL,
  `resource_uri` varchar(250) NOT NULL,
  `dct_contributor` varchar(250) NOT NULL,
  `dc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 13312 kB';

DROP TABLE IF EXISTS `audit_viewed`;
CREATE TABLE `audit_viewed` (
  `ID` int(10) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `dc_identifier` varchar(50) NOT NULL DEFAULT '',
  `collections_iana_UUID` varchar(50) NOT NULL DEFAULT '',
  `collections_dc_identifier` varchar(50) NOT NULL DEFAULT '',
  `dc_references` varchar(250) NOT NULL,
  `dc_title` varchar(250) NOT NULL,
  `dct_contributor` varchar(250) NOT NULL,
  `dc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `collections`;
CREATE TABLE `collections` (
  `ID` int(11) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `dc_identifier` varchar(20) NOT NULL DEFAULT '0',
  `bf_heldBy` varchar(750) NOT NULL DEFAULT '',
  `bf_subLocation` varchar(750) NOT NULL,
  `bf_physicalLocation` varchar(750) NOT NULL,
  `skos_collection` varchar(750) NOT NULL DEFAULT '',
  `skos_orderedCollection` varchar(250) NOT NULL DEFAULT '',
  `bibo_volume` int(11) DEFAULT NULL,
  `disco_startDate` varchar(11) DEFAULT NULL,
  `disco_endDate` varchar(11) DEFAULT NULL,
  `dc_relation` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 13312 kB';

DROP TABLE IF EXISTS `contributors`;
CREATE TABLE `contributors` (
  `ID` int(5) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `dc_identifier` varchar(255) DEFAULT NULL,
  `dc_dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dc_description` text,
  `dc_type` varchar(255) DEFAULT NULL,
  `vcard_email` varchar(255) DEFAULT NULL,
  `foaf_familyName` varchar(250) NOT NULL,
  `foaf_firstName` varchar(250) NOT NULL,
  `credential_loginName` varchar(255) DEFAULT NULL,
  `credential_passPhrase` varchar(255) DEFAULT NULL,
  `credential_expDate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `datasource_cities`;
CREATE TABLE `datasource_cities` (
  `ID` int(15) NOT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `region` varchar(256) DEFAULT NULL,
  `population` int(10) UNSIGNED DEFAULT NULL,
  `latitude` varchar(256) DEFAULT NULL,
  `longitude` varchar(256) DEFAULT NULL,
  `combined` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `datasource_continent`;
CREATE TABLE `datasource_continent` (
  `ID` int(11) NOT NULL,
  `continent` text NOT NULL,
  `country` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `datasource_continent_name`;
CREATE TABLE `datasource_continent_name` (
  `ID` int(5) NOT NULL,
  `code` text NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `datasource_country`;
CREATE TABLE `datasource_country` (
  `ID` int(25) NOT NULL,
  `Country` varchar(44) DEFAULT NULL,
  `Alpha2_Code` varchar(2) DEFAULT NULL,
  `Alpha3_Code` varchar(3) DEFAULT NULL,
  `Numeric_Code` int(3) DEFAULT NULL,
  `Latitude` decimal(7,4) DEFAULT NULL,
  `Longitude` decimal(8,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `datasource_googlebooks`;
CREATE TABLE `datasource_googlebooks` (
  `ID` int(15) NOT NULL,
  `google_kind` varchar(255) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `google_etag` varchar(255) NOT NULL,
  `google_selfLink` varchar(255) NOT NULL,
  `volumeInfo_title` varchar(255) NOT NULL,
  `volumeInfo_authors` varchar(255) NOT NULL,
  `volumeInfo_description` text NOT NULL,
  `volumeInfo_smallThumbnail` varchar(255) NOT NULL,
  `volumeInfo_thumbnail` varchar(255) NOT NULL,
  `value_string` varchar(255) NOT NULL,
  `bf_note` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `datasource_sites`;
CREATE TABLE `datasource_sites` (
  `ID` int(15) NOT NULL,
  `gn_name` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `dc_type` varchar(255) NOT NULL,
  `bf_note` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `descriptions`;
CREATE TABLE `descriptions` (
  `ID` int(10) NOT NULL,
  `iana_UUID` varchar(255) NOT NULL,
  `items_iana_UUID` varchar(255) NOT NULL,
  `items_dc_identifier` varchar(255) NOT NULL,
  `dc_description` longtext NOT NULL,
  `json_ld` longtext NOT NULL,
  `dc_created` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `flags`;
CREATE TABLE `flags` (
  `ID` int(11) NOT NULL,
  `dc_references` varchar(250) NOT NULL,
  `dc_created` varchar(250) NOT NULL,
  `bf_note` varchar(250) NOT NULL,
  `credential_loginName` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `ID` int(11) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `dc_identifier` varchar(250) NOT NULL DEFAULT '',
  `collections_iana_UUID` varchar(250) NOT NULL,
  `collections_dc_identifier` varchar(250) NOT NULL,
  `dc_references` varchar(250) NOT NULL,
  `dc_title` varchar(250) NOT NULL DEFAULT '',
  `bibo_pages` int(20) NOT NULL,
  `dc_type` varchar(20) NOT NULL,
  `dc_format` varchar(250) NOT NULL,
  `prism_byteCount` varchar(250) NOT NULL,
  `rdf_resource` varchar(250) NOT NULL DEFAULT '',
  `rights_dc_identifer` varchar(250) NOT NULL,
  `dc_creator` varchar(250) NOT NULL,
  `org_FormalOrganisation` varchar(250) NOT NULL,
  `gn_name` varchar(250) NOT NULL,
  `dc_created` varchar(20) NOT NULL,
  `dc_description` mediumtext NOT NULL,
  `dct_accessRights` varchar(250) NOT NULL,
  `marc_addressee` varchar(250) NOT NULL,
  `rdaa_groupMemberOf` varchar(250) NOT NULL,
  `mads_associatedLocale` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 13312 kB';

DROP TABLE IF EXISTS `labels`;
CREATE TABLE `labels` (
  `ID` int(11) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `reg_uri` varchar(250) NOT NULL,
  `rdfs_label` varchar(250) NOT NULL,
  `skos_definition` varchar(250) NOT NULL,
  `reg_lexicalAlias` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `namespaces`;
CREATE TABLE `namespaces` (
  `ID` int(11) NOT NULL,
  `reg_uri` varchar(250) NOT NULL,
  `rdf_type` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rda`;
CREATE TABLE `rda` (
  `ID` int(10) NOT NULL,
  `reg_uri` varchar(11) DEFAULT NULL,
  `reg_name` varchar(46) DEFAULT NULL,
  `rdfs_label` varchar(77) DEFAULT NULL,
  `reg_lexicalAlias` varchar(51) DEFAULT NULL,
  `skos_description` varchar(290) DEFAULT NULL,
  `skos_scopeNote` varchar(297) DEFAULT NULL,
  `rdfs_domain` varchar(11) DEFAULT NULL,
  `rdfs_range` varchar(11) DEFAULT NULL,
  `reg_hasUnconstrained` varchar(20) DEFAULT NULL,
  `rdfs_subPropertyOf` varchar(18) DEFAULT NULL,
  `owl_inverseOf` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `relatedconcepts`;
CREATE TABLE `relatedconcepts` (
  `ID` int(10) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `annotations_reg_uri` varchar(250) NOT NULL,
  `annotations_rdfs_label` varchar(250) NOT NULL,
  `annotations_value_string` varchar(250) NOT NULL,
  `reg_uri` varchar(250) NOT NULL,
  `rdfs_label` varchar(250) NOT NULL,
  `value_string` varchar(250) NOT NULL,
  `dct_contributor` varchar(250) NOT NULL,
  `dc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dc_source` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rights`;
CREATE TABLE `rights` (
  `ID` int(10) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `dc_identifier` varchar(250) NOT NULL,
  `items_iana_UUID` varchar(250) NOT NULL,
  `items_dc_identifier` varchar(250) NOT NULL,
  `dc_references` varchar(250) NOT NULL,
  `contributors_iana_UUID` varchar(250) NOT NULL,
  `contributors_dc_identifier` varchar(250) NOT NULL,
  `dct_contributor` varchar(250) NOT NULL,
  `dct_accessRights` varchar(250) NOT NULL,
  `dct_modified` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `vocabularies`;
CREATE TABLE `vocabularies` (
  `ID` int(5) NOT NULL,
  `iana_UUID` varchar(250) NOT NULL,
  `reg_lexicalAlias` varchar(250) NOT NULL,
  `skos_definition` varchar(250) NOT NULL,
  `bf_note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `annotations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `reg_uri` (`reg_uri`),
  ADD KEY `rdfs_label` (`rdfs_label`),
  ADD KEY `dct_contributor` (`dct_contributor`),
  ADD KEY `dc_references` (`dc_references`);

ALTER TABLE `audit`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `reg_uri` (`reg_uri`),
  ADD KEY `rdfs_label` (`rdfs_label`),
  ADD KEY `dct_contributor` (`dct_contributor`);

ALTER TABLE `audit_viewed`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `dct_contributor` (`dct_contributor`);

ALTER TABLE `collections`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `FileSource` (`dc_identifier`),
  ADD UNIQUE KEY `FileSource_2` (`dc_identifier`),
  ADD KEY `dc_identifier` (`dc_identifier`);

ALTER TABLE `contributors`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `iana_UUID` (`iana_UUID`),
  ADD UNIQUE KEY `dc_identifier` (`dc_identifier`),
  ADD UNIQUE KEY `credential_loginName_2` (`credential_loginName`),
  ADD UNIQUE KEY `credential_passPhrase` (`credential_passPhrase`),
  ADD KEY `credential_loginName` (`credential_loginName`);

ALTER TABLE `datasource_cities`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `datasource_continent`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `datasource_continent_name`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `datasource_country`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `datasource_googlebooks`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `datasource_sites`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `descriptions`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `flags`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `bf_note` (`bf_note`),
  ADD KEY `dc_loginName` (`credential_loginName`),
  ADD KEY `dc_references` (`dc_references`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `FileName` (`dc_title`),
  ADD UNIQUE KEY `FileLocation` (`rdf_resource`),
  ADD UNIQUE KEY `dc_identifier_3` (`dc_identifier`),
  ADD UNIQUE KEY `iana_UUID` (`iana_UUID`),
  ADD KEY `dc_references` (`dc_references`),
  ADD KEY `dc_identifier` (`dc_identifier`),
  ADD KEY `dc_identifier_2` (`dc_identifier`),
  ADD KEY `dct_accessRights` (`dct_accessRights`),
  ADD KEY `rights_dc_identifer` (`rights_dc_identifer`),
  ADD KEY `rights_dc_identifer_2` (`rights_dc_identifer`),
  ADD KEY `rights_dc_identifer_3` (`rights_dc_identifer`);

ALTER TABLE `labels`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `iana_UUID` (`iana_UUID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `rdfs_label_2` (`rdfs_label`),
  ADD UNIQUE KEY `reg_lexicalAlias` (`reg_lexicalAlias`),
  ADD UNIQUE KEY `skos_definition` (`skos_definition`),
  ADD KEY `namespaces_prefix` (`reg_uri`),
  ADD KEY `rdfs_label` (`rdfs_label`),
  ADD KEY `skos_definition_2` (`skos_definition`);

ALTER TABLE `namespaces`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `prefix_2` (`reg_uri`),
  ADD KEY `prefix` (`reg_uri`),
  ADD KEY `prefix_3` (`reg_uri`);

ALTER TABLE `rda`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `relatedconcepts`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

ALTER TABLE `rights`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `iana_UUID` (`iana_UUID`),
  ADD UNIQUE KEY `dc_identifier_2` (`dc_identifier`),
  ADD KEY `dc_references` (`dc_references`),
  ADD KEY `dc_identifier` (`dc_identifier`),
  ADD KEY `dc_identifier_3` (`dc_identifier`),
  ADD KEY `dct_contributor` (`dct_contributor`),
  ADD KEY `items_iana_UUID` (`items_iana_UUID`),
  ADD KEY `dc_identifier_4` (`dc_identifier`),
  ADD KEY `dct_accessRights` (`dct_accessRights`),
  ADD KEY `contributors_dc_identifier` (`contributors_dc_identifier`);

ALTER TABLE `vocabularies`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `reg_lexicalAlias` (`reg_lexicalAlias`),
  ADD KEY `skos_definition` (`skos_definition`);


ALTER TABLE `annotations`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `audit`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `audit_viewed`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `collections`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contributors`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT;

ALTER TABLE `datasource_cities`
  MODIFY `ID` int(15) NOT NULL AUTO_INCREMENT;

ALTER TABLE `datasource_continent`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `datasource_continent_name`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT;

ALTER TABLE `datasource_country`
  MODIFY `ID` int(25) NOT NULL AUTO_INCREMENT;

ALTER TABLE `datasource_googlebooks`
  MODIFY `ID` int(15) NOT NULL AUTO_INCREMENT;

ALTER TABLE `datasource_sites`
  MODIFY `ID` int(15) NOT NULL AUTO_INCREMENT;

ALTER TABLE `descriptions`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `flags`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `items`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `labels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `namespaces`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rda`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `relatedconcepts`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rights`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vocabularies`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT;


ALTER TABLE `annotations`
  ADD CONSTRAINT `annotations_ibfk_1` FOREIGN KEY (`reg_uri`) REFERENCES `namespaces` (`reg_uri`),
  ADD CONSTRAINT `annotations_ibfk_2` FOREIGN KEY (`rdfs_label`) REFERENCES `labels` (`rdfs_label`),
  ADD CONSTRAINT `annotations_ibfk_3` FOREIGN KEY (`dct_contributor`) REFERENCES `contributors` (`dc_identifier`),
  ADD CONSTRAINT `annotations_ibfk_4` FOREIGN KEY (`dc_references`) REFERENCES `items` (`dc_identifier`);

ALTER TABLE `audit`
  ADD CONSTRAINT `audit_ibfk_1` FOREIGN KEY (`dct_contributor`) REFERENCES `contributors` (`dc_identifier`);

ALTER TABLE `flags`
  ADD CONSTRAINT `flags_ibfk_1` FOREIGN KEY (`dc_references`) REFERENCES `items` (`dc_identifier`),
  ADD CONSTRAINT `flags_ibfk_2` FOREIGN KEY (`credential_loginName`) REFERENCES `contributors` (`credential_loginName`);

ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`dc_references`) REFERENCES `collections` (`dc_identifier`);

ALTER TABLE `labels`
  ADD CONSTRAINT `labels_ibfk_1` FOREIGN KEY (`reg_uri`) REFERENCES `namespaces` (`reg_uri`);

ALTER TABLE `rights`
  ADD CONSTRAINT `rights_ibfk_4` FOREIGN KEY (`dc_references`) REFERENCES `items` (`dc_identifier`),
  ADD CONSTRAINT `rights_ibfk_6` FOREIGN KEY (`dct_contributor`) REFERENCES `contributors` (`dc_identifier`);

ALTER TABLE `vocabularies`
  ADD CONSTRAINT `vocabularies_ibfk_1` FOREIGN KEY (`reg_lexicalAlias`) REFERENCES `labels` (`reg_lexicalAlias`),
  ADD CONSTRAINT `vocabularies_ibfk_2` FOREIGN KEY (`skos_definition`) REFERENCES `labels` (`skos_definition`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
