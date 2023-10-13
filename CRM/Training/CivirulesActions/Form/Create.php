<?php

use CRM_Training_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Training_CivirulesActions_Form_Create extends CRM_Core_Form {
	// Maximum length of description field
	const DESCRIPTION_MAX = 255;
	// Years either side of present for start/end date selection
	const YEARS_RANGE = 20;

	protected $ruleActionId = false;

	protected $ruleAction;

	protected $rule;

	protected $action;

	protected $triggerClass;

	protected $_creditsAwarded = false;

	protected $_hasEvent = false;

	/**
	 * Overridden parent method to do pre-form building processing
	 *
	 * @throws Exception when action or rule action not found
	 * @access public
	 */
	public function preProcess() {
		$this->ruleActionId = CRM_Utils_Request::retrieve('rule_action_id', 'Integer');
		$this->ruleAction = new CRM_Civirules_BAO_RuleAction();
		$this->action = new CRM_Civirules_BAO_Action();
		$this->rule = new CRM_Civirules_BAO_Rule();
		$this->ruleAction->id = $this->ruleActionId;
		if ($this->ruleAction->find(TRUE)) {
			$this->action->id = $this->ruleAction->action_id;
			if (!$this->action->find(TRUE)) {
				throw new Exception('CiviRules Could not find action with id '.$this->ruleAction->action_id);
			}
		} else {
			throw new Exception('CiviRules Could not find rule action with id '.$this->ruleActionId);
		}

		$this->rule->id = $this->ruleAction->rule_id;
		if (!$this->rule->find(TRUE)) {
			throw new Exception('Civirules could not find rule');
		}

		$this->triggerClass = CRM_Civirules_BAO_Trigger::getTriggerObjectByTriggerId($this->rule->trigger_id, TRUE);
		$this->triggerClass->setTriggerId($this->rule->trigger_id);

		$providedEntities = $this->triggerClass->getProvidedEntities();
		if (isset($providedEntities['Event'])) {
			$this->_hasEvent = TRUE;
		}

		if ( !empty( $this->ruleAction->action_params ) ) {
			$data = unserialize( $this->ruleAction->action_params );
			$this->_creditsAwarded = ( "1" == $data['credits_awarded'] ) ? true : false;
		}

		parent::preProcess();
	}

	public function addRules() {
		parent::addRules();
		$this->addFormRule(array('CRM_Training_CivirulesActions_Form_Create', 'validateCreateForm'));
	}

	/**
	 * Function to validate value of rule action form
	 *
	 * @param array $fields
	 * @return array|bool
	 * @access public
	 * @static
	 */
	static function validateCreateForm( $fields ) {
		$errors = array();
		$hasEvent = false;

		$ruleActionId = CRM_Utils_Request::retrieve('rule_action_id', 'Integer');
		$ruleAction = new CRM_Civirules_BAO_RuleAction();
		$ruleAction->id = $ruleActionId;
		if ( $ruleAction->find(true) ) {
			$rule = new CRM_Civirules_BAO_Rule();
			$rule->id = $ruleAction->rule_id;
			if ( $rule->find(true) ) {
				$triggerClass = CRM_Civirules_BAO_Trigger::getTriggerObjectByTriggerId( $rule->trigger_id, TRUE );
				$triggerClass->setTriggerId( $rule->trigger_id );

				$providedEntities = $triggerClass->getProvidedEntities();
				if ( isset( $providedEntities['Event'] ) ) {
					$hasEvent = TRUE;
				}
			}
		}

		if ( $fields['credits_awarded'] == 1 && empty( $fields['credits'] ) ) {
			$errors['credits'] = ts('Credits Awarded is a required field.');
		}

		if ( !$hasEvent && empty( $fields['label'] ) ) {
			$errors['label'] = ts('Training Name is required.');
		} elseif ( $hasEvent && !boolval( $fields['use_event'] ) && empty( $fiels['label'] ) ) {
			$errors['label'] = ts('Training Name is required.');
		}

		if (count($errors)) {
			return $errors;
		}

		return true;
	}

	public function buildQuickForm() {

		// add form elements
		$this->add('hidden', 'rule_action_id');
		$this->addEntityRef( 'type_id', E::ts('Training Type'), [
			'entity' => 'TrainingType',
			'select' => [ 'minimumInputLength' => 0 ]
		], TRUE );
		$this->add( 'checkbox', 'use_event', E::ts('Use Event For Label'));
		$this->add( 'text', 'label', E::ts('Training Name'), ['class' => 'huge'], FALSE );
		$this->add( 'textarea', 'description', E::ts('Training Description'), [ 'maxlength' => self::DESCRIPTION_MAX ] );
		$this->addYesNo( 'credits_awarded', E::ts( 'Award Credits?' ), '', FALSE );
		$this->add( 'text', 'credits', E::ts('Credits Awarded'), [ 'size' => 5, 'maxlength' => 5 ], FALSE );
		// $this->add( 'datepicker', 'entry_date', ts('Date Awarded'), [], FALSE, [ 'time' => FALSE ] );

		$this->addRule( 'credits', E::ts('Please enter a valid credit.'), 'regex', '/[0-9]{1,2}/' );

		$this->assign('credits_awarded', $this->_creditsAwarded);
		$this->assign('hasEvent', $this->_hasEvent);

		// add buttons
		$this->addButtons([
			['type' => 'next', 'name' => E::ts('Save'), 'isDefault' => TRUE,],
			['type' => 'cancel', 'name' => E::ts('Cancel')]
		]);

		// export form elements
		$this->assign('elementNames', $this->getRenderableElementNames());
		parent::buildQuickForm();
	}

	public function postProcess() {
		$values = $this->exportValues();

		$data['type_id'] = $values['type_id'];
		$data['label'] = $values['label'];
		$data['description'] = $values['description'];
		$data['credits_awarded'] = $values['credits_awarded'] ?? FALSE;
		$data['credits'] = $values['credits'];
		$data['use_event'] = $values['use_event'];
		// $data['entry_date'] = $values['entry_date'];

		$ruleAction = new CRM_Civirules_BAO_RuleAction();
		$ruleAction->id = $this->ruleActionId;
		$ruleAction->action_params = serialize($data);
		$ruleAction->save();

		$session = CRM_Core_Session::singleton();
		$session->setStatus('Action '.$this->action->label.' parameters updated to CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleAction->rule_id),
		'Action parameters updated', 'success');

		$redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->ruleAction->rule_id, TRUE);
		CRM_Utils_System::redirect($redirectUrl);
	}

	/**
	 * Get the fields/elements defined in this form.
	 *
	 * @return array (string)
	 */
	public function getRenderableElementNames() {
		// The _elements list includes some items which should not be
		// auto-rendered in the loop -- such as "qfKey" and "buttons".  These
		// items don't have labels.  We'll identify renderable by filtering on
		// the 'label'.
		$elementNames = array();
		foreach ($this->_elements as $element) {
		/** @var HTML_QuickForm_Element $element */
		$label = $element->getLabel();
		if (!empty($label)) {
			$elementNames[] = $element->getName();
		}
		}
		return $elementNames;
	}

	/**
	 * Overridden parent method to set default values
	 *
	 * @return array $defaultValues
	 * @access public
	 */
	public function setDefaultValues() {
		$data = [];
		$defaultValues = [];
		$defaultValues['rule_action_id'] = $this->ruleActionId;
		if ( !empty( $this->ruleAction->action_params ) ) {
			$data = unserialize($this->ruleAction->action_params);
		}
		if ( !empty( $data['type_id'] ) ) {
			$defaultValues['type_id'] = $data['type_id'];
		}
		if ( !empty( $data['use_event'] ) ) {
			$defaultValues['use_event'] = $data['use_event'];
		}
		if ( !empty( $data['label'] ) ) {
			$defaultValues['label'] = $data['label'];
		}
		if ( !empty( $data['description'] ) ) {
			$defaultValues['description'] = $data['description'];
		}
		if ( !empty( $data['credits_awarded'] ) ) {
			$defaultValues['credits_awarded'] = $data['credits_awarded'];
		}
		if ( !empty( $data['credits'] ) ) {
			$defaultValues['credits'] = $data['credits'];
		}
		// if ( !empty( $data['entry_date'] ) ) {
		// 	$defaultValues['entry_date'] = $data['entry_date'];
		// }

		return $defaultValues;
	}

	/**
	 * Method to set the form title
	 *
	 * @access protected
	 */
	protected function setFormTitle() {
		$title = 'CiviRules Edit Action parameters';
		$this->assign('ruleActionHeader', 'Edit action '.$this->action->label.' of CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleAction->rule_id));
		CRM_Utils_System::setTitle($title);
	}

}
