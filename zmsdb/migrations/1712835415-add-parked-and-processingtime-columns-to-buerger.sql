ALTER TABLE `buerger` ADD COLUMN `parked` int(5) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `buerger` ADD COLUMN `processingTime` TIME DEFAULT null;