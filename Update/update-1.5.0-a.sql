
-- add columns to elements
ALTER TABLE `atsd_configurators_fieldsets_elements`
    ADD `dependency` TINYINT(1) NOT NULL DEFAULT '0' AFTER `multiple`,
    ADD `surcharge` TINYINT(1) NOT NULL DEFAULT '0' AFTER `dependency`;
