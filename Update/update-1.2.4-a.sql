
-- add column to configuration
ALTER TABLE `atsd_configurators` ADD `chargeArticle` TINYINT(1) NOT NULL DEFAULT '1' AFTER `rebate`;
