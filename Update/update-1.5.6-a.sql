
-- disable foreign key checks
SET foreign_key_checks = 0;

-- get the current configuration id
SET @id = (
    SELECT element.`id`
    FROM s_core_config_elements AS element
        LEFT JOIN s_core_config_forms AS form
            ON element.form_id = form.id
    WHERE form.name = 'AtsdConfigurator'
        AND element.`name` = 'allowArticlesWithoutCategory'
);

-- remove translations
DELETE FROM s_core_config_element_translations
WHERE element_id = @id;

-- remove values
DELETE FROM s_core_config_values
WHERE element_id = @id;

-- remove element
DELETE FROM s_core_config_elements
WHERE id = @id;

-- enable foreign key checks
SET foreign_key_checks = 1;
