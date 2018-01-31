
-- add columns to elements
ALTER TABLE `atsd_configurators_fieldsets_elements_articles`
    ADD `surcharge` INT(11) NOT NULL DEFAULT '0' AFTER `quantityMultiply`;
