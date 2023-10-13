-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the existing tables - this section generated from drop.tpl
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `training_record`;
DROP TABLE IF EXISTS `training_type`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * training_type
-- *
-- * FIXME
-- *
-- *******************************************************/
CREATE TABLE `training_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Type ID',
  `name` varchar(128) NOT NULL,
  `description` varchar(255),
  `is_active` tinyint COMMENT 'Is this training type enabled?',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index_training_name`(name)
)
ENGINE=InnoDB;

-- /*******************************************************
-- *
-- * training_record
-- *
-- * FIXME
-- *
-- *******************************************************/
CREATE TABLE `training_record` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Record ID',
  `type_id` int unsigned NOT NULL COMMENT 'FK to TrainingType',
  `contact_id` int unsigned NOT NULL COMMENT 'FK to Contact',
  `label` varchar(255) COMMENT 'Identifier for the training item',
  `description` text COMMENT 'Full description of record. Text and html allowed.',
  `credits_awarded` tinyint NOT NULL DEFAULT 0 COMMENT 'Whether or not credits were awarded for this record. If so, you can specify the number of credits below.',
  `credits` int unsigned DEFAULT NULL COMMENT 'The number of credits awarded for the record.',
  `entry_date` date DEFAULT NULL COMMENT 'Date the record was made.',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_training_record_type_id FOREIGN KEY (`type_id`) REFERENCES `training_type`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_training_record_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE
)
ENGINE=InnoDB;
