<?php
namespace Civi\Api4;

/**
 * TrainingRecord entity.
 *
 * Provided by the FIXME extension.
 *
 * @package Civi\Api4
 */
class TrainingRecord extends Generic\DAOEntity {

	public static function permissions() {
		$permissions = parent::permissions();
		$permissions['get'] = ['edit my contact'];

		return $permissions;
	}

}
