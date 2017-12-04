
-- add columns to elements-to-articles
ALTER TABLE `atsd_configurators_fieldsets_elements_articles`
    ADD `quantitySelect` TINYINT(1) NOT NULL DEFAULT '0' AFTER `quantity`,
    ADD `quantityMultiply` TINYINT(1) NOT NULL DEFAULT '0' AFTER `quantitySelect`;
