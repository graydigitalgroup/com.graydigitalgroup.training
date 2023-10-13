<?php

use CRM_Training_ExtensionUtil as E;
/**
 * Class for CiviRule Condition Training
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Training_CivirulesActions_Create extends CRM_CivirulesActions_Generic_Api {


	/**
	 * Method to get the api entity to process in this CiviRule action
	 *
	 * @access protected
	 * @abstract
	 */
	protected function getApiEntity() {
		return 'TrainingRecord';
	}

	/**
	 * Method to get the api action to process in this CiviRule action
	 *
	 * @access protected
	 * @abstract
	 */
	protected function getApiAction() {
		return 'create';
	}

	/**
	 * Returns an array with parameters used for processing an action
	 *
	 * @param array $parameters
	 * @param CRM_Civirules_TriggerData_TriggerData $rtiggerData
	 * @return array
	 * @access protected
	 */
	protected function alterApiParameters($parameters, CRM_Civirules_TriggerData_TriggerData $triggerData) {
		//this method could be overridden in subclasses to alter parameters to meet certain criteria
		$contactId = $triggerData->getContactId();
		$parameters['contact_id'] = $contactId;
		$actionParameters = $this->getActionParameters();

		if ( !empty( $actionParameters['type_id'] ) ) {
			$parameters['type_id'] = $actionParameters['type_id'];
		}

		if ( !empty( $actionParameters['use_event'] ) && "1" == $actionParameters['use_event'] ) {
			$event = $triggerData->getEntityData('Event');
			$parameters['label'] = $event['title'];
		} else {
			$parameters['label'] = $actionParameters['label'];
		}

		if ( !empty( $actionParameters['label'] ) ) {
			$parameters['label'] = $actionParameters['label'];
		}

		if ( !empty( $actionParameters['description'] ) ) {
			$parameters['description'] = $actionParameters['description'];
		}

		if ( !empty( $actionParameters['credits_awarded'] ) ) {
			$parameters['credits_awarded'] = $actionParameters['credits_awarded'];
		}

		if ( !empty( $actionParameters['credits'] ) ) {
			$parameters['credits'] = $actionParameters['credits'];
		}

		if ( !empty( $actionParameters['entry_date'] ) ) {
			$parameters['entry_date'] = $actionParameters['entry_date'];
		} else {
			$parameters['entry_date'] = date("Y-m-d");
		}

		return $parameters;
	}

	/**
	 * Returns a redirect url to extra data input from the user after adding a action
	 *
	 * Return false if you do not need extra data input
	 *
	 * @param int $ruleActionId
	 * @return bool|string
	 * $access public
	 */
	public function getExtraDataInputUrl($ruleActionId) {
		return CRM_Utils_System::url('civicrm/civirules/actions/training', 'rule_action_id='.$ruleActionId);
	}

	/**
	 * alterApiParameters is a protected method, defined by the Civirules
	 * extension and as such we cannot make it public. The public method below
	 * exposes that function enabling us to have phpunit tests for it.
	 */
	public function alterApiParametersForTesting($parameters, CRM_Civirules_TriggerData_TriggerData $triggerData) {
		return $this->alterApiParameters($parameters, $triggerData);
	}

	/**
	 * Returns a user friendly text explaining the condition params
	 * e.g. 'Older than 65'
	 *
	 * @return string
	 * @access public
	 */
	public function userFriendlyConditionParams() {
		$training_type = '--Not Selected--';
		$record_name = '';
		$record_description = '';
		$credits_awarded = '';
		$params = $this->getActionParameters();

		if ( !empty( $params['type_id'] ) ) {
			$trainingType = $this->getTrainingType( intval( $params['type_id'] ) );
			if ( isset( $trainingType ) ) {
				$training_type = $trainingType['name'];
			}
		}

		if ( !empty( $params['use_event'] ) && "1" == $params['use_event'] ) {
			$record_name = ' the event from the trigger ';
		} else {
			$record_name = ' \'' .$params['label'] .'\'';
		}

		if ( !empty( $params['description'] )) {
			$record_name = ' with description of: ' .$params['description'];
		}

		if ( !empty( $params['credits_awarded'] && !empty( $params['credits'] ) ) ) {
			$credits_awarded = ' and ' . $params['credits'] . ' credits awarded';
		}

		return E::ts("Record a Training Record with a Trainig Type of '%1' with the name of %2%3%4, using date of trigger for awarded date.", [
			1 => $training_type,
			2 => $record_name,
			3 => $record_description,
			4 => $credits_awarded
		]);
	}

	/**
	 * Tries to locate a Training Type by ID
	 *
	 * @param int $type_id The ID of the training type to locate.
	 *
	 * @return null|object
	 */
	protected function getTrainingType( $type_id ) {

		$trainingTypes = \Civi\Api4\TrainingType::get(TRUE)
			->addWhere('id', '=', $type_id)
			->setLimit(1)
			->execute();
		return $trainingTypes->first();
	}
}
