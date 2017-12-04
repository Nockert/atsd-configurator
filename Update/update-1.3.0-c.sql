
-- copy selection articles to new table
INSERT INTO atsd_configurators_selections_articles
    SELECT
        NULL,
        atsd_configurators_fieldsets_elements_articles.quantity,
        atsd_configurators_selections_to_articles.selectionId,
        atsd_configurators_selections_to_articles.articleId
    FROM atsd_configurators_selections_to_articles
        LEFT JOIN atsd_configurators_fieldsets_elements_articles
            ON atsd_configurators_selections_to_articles.articleId = atsd_configurators_fieldsets_elements_articles.id;

-- remove old table
DROP TABLE atsd_configurators_selections_to_articles;
