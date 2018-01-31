
--
-- Tabellenstruktur für Tabelle `atsd_configurators_selections_articles`
--

CREATE TABLE `atsd_configurators_selections_articles` (
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `selectionId` int(11) DEFAULT NULL,
  `articleId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `atsd_configurators_selections_articles`
--
ALTER TABLE `atsd_configurators_selections_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_746450DAED72CAA4` (`selectionId`),
  ADD KEY `IDX_746450DAFEA2A0EE` (`articleId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `atsd_configurators_selections_articles`
--
ALTER TABLE `atsd_configurators_selections_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `atsd_configurators_selections_articles`
--
ALTER TABLE `atsd_configurators_selections_articles`
  ADD CONSTRAINT `FK_746450DAED72CAA4` FOREIGN KEY (`selectionId`) REFERENCES `atsd_configurators_selections` (`id`),
  ADD CONSTRAINT `FK_746450DAFEA2A0EE` FOREIGN KEY (`articleId`) REFERENCES `atsd_configurators_fieldsets_elements_articles` (`id`);
