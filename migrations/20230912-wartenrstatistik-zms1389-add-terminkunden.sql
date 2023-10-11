-- Rename the columns with the "_spontan" suffix
ALTER TABLE `wartenrstatistik`
    CHANGE `zeit_ab_00` `zeit_ab_00_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_01` `zeit_ab_01_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_02` `zeit_ab_02_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_03` `zeit_ab_03_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_04` `zeit_ab_04_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_05` `zeit_ab_05_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_06` `zeit_ab_06_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_07` `zeit_ab_07_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_08` `zeit_ab_08_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_09` `zeit_ab_09_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_10` `zeit_ab_10_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_11` `zeit_ab_11_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_12` `zeit_ab_12_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_13` `zeit_ab_13_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_14` `zeit_ab_14_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_15` `zeit_ab_15_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_16` `zeit_ab_16_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_17` `zeit_ab_17_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_18` `zeit_ab_18_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_19` `zeit_ab_19_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_20` `zeit_ab_20_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_21` `zeit_ab_21_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_22` `zeit_ab_22_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `zeit_ab_23` `zeit_ab_23_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,

    CHANGE `wartende_ab_00` `wartende_ab_00_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_01` `wartende_ab_01_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_02` `wartende_ab_02_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_03` `wartende_ab_03_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_04` `wartende_ab_04_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_05` `wartende_ab_05_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_06` `wartende_ab_06_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_07` `wartende_ab_07_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_08` `wartende_ab_08_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_09` `wartende_ab_09_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_10` `wartende_ab_10_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_11` `wartende_ab_11_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_12` `wartende_ab_12_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_13` `wartende_ab_13_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_14` `wartende_ab_14_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_15` `wartende_ab_15_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_16` `wartende_ab_16_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_17` `wartende_ab_17_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_18` `wartende_ab_18_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_19` `wartende_ab_19_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_20` `wartende_ab_20_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_21` `wartende_ab_21_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_22` `wartende_ab_22_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `wartende_ab_23` `wartende_ab_23_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,

    CHANGE `echte_zeit_ab_00` `echte_zeit_ab_00_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_01` `echte_zeit_ab_01_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_02` `echte_zeit_ab_02_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_03` `echte_zeit_ab_03_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_04` `echte_zeit_ab_04_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_05` `echte_zeit_ab_05_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_06` `echte_zeit_ab_06_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_07` `echte_zeit_ab_07_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_08` `echte_zeit_ab_08_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_09` `echte_zeit_ab_09_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_10` `echte_zeit_ab_10_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_11` `echte_zeit_ab_11_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_12` `echte_zeit_ab_12_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_13` `echte_zeit_ab_13_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_14` `echte_zeit_ab_14_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_15` `echte_zeit_ab_15_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_16` `echte_zeit_ab_16_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_17` `echte_zeit_ab_17_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_18` `echte_zeit_ab_18_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_19` `echte_zeit_ab_19_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_20` `echte_zeit_ab_20_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_21` `echte_zeit_ab_21_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_22` `echte_zeit_ab_22_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE `echte_zeit_ab_23` `echte_zeit_ab_23_spontan` int(5) UNSIGNED NOT NULL DEFAULT 0;

-- Add the new columns with the "_termin" suffix
ALTER TABLE `wartenrstatistik`
    ADD `zeit_ab_00_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_01_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_02_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_03_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_04_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_05_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_06_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_07_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_08_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_09_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_10_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_11_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_12_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_13_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_14_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_15_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_16_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_17_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_18_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_19_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_20_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_21_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_22_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `zeit_ab_23_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    
    ADD `wartende_ab_00_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_01_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_02_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_03_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_04_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_05_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_06_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_07_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_08_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_09_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_10_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_11_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_12_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_13_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_14_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_15_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_16_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_17_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_18_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_19_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_20_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_21_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_22_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `wartende_ab_23_termin` int(5) UNSIGNED NOT NULL DEFAULT 0,

    ADD `echte_zeit_ab_00_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_01_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_02_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_03_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_04_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_05_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_06_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_07_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_08_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_09_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_10_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_11_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_12_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_13_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_14_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_15_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_16_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_17_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_18_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_19_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_20_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_21_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_22_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0,
    ADD `echte_zeit_ab_23_termin` INT(5) UNSIGNED NOT NULL DEFAULT 0;
