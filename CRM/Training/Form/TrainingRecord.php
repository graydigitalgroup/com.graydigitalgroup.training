<?php

use CRM_Training_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Training_Form_TrainingRecord extends CRM_Core_Form {
  // Maximum length of description field
  const DESCRIPTION_MAX = 255;
  // Years either side of present for start/end date selection
  const YEARS_RANGE = 20;

  protected $_id;

  protected $_trainingRecord;

  public function getDefaultEntity() {
    return 'TrainingRecord';
  }

  public function getDefaultEntityTable() {
    return CRM_Training_DAO_TrainingRecord::$_tableName;
  }

  public function getEntityId() {
    return $this->_id;
  }

  /**
   * Preprocess form.
   *
   * This is called before buildForm. Any pre-processing that
   * needs to be done for buildForm should be done here.
   *
   * This is a virtual function and should be redefined if needed.
   */
  public function preProcess() {
    parent::preProcess();

    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this);
    $this->assign('action', $this->_action);

    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive', $this, FALSE);
    CRM_Utils_System::setTitle('Add Training Record');
    if ($this->_id) {
      CRM_Utils_System::setTitle('Edit Training Record');
      $records = civicrm_api4('TrainingRecord', 'get', ['where' => [['id', '=', $this->_id]], 'limit' => 1]);
      $this->_trainingRecord = $records->first();
      $this->assign('trainingRecord', $this->_trainingRecord);

      $session = CRM_Core_Session::singleton();
      $session->replaceUserContext(CRM_Utils_System::url('civicrm/trainings/record', ['id' => $this->getEntityId(), 'action' => 'update']));
    } else {

    }
  }

  public function buildQuickForm() {

    $this->assign('id', $this->getEntityId());
    $this->add('hidden', 'id');
    if ($this->_action != CRM_Core_Action::DELETE) {
      $this->assign( 'contact_id', CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, FALSE ) );
      $this->add( 'hidden', 'contact_id' );
      error_log( 'buildQuickForm::trainingRecord: ' . print_r( $this->_trainingRecord, true ) );
      $this->addEntityRef( 'type_id', E::ts('Training Type'), [
        'entity' => 'TrainingType',
        'select' => [ 'minimumInputLength' => 0 ]
      ], TRUE );
      $this->add( 'text', 'label', E::ts('Training Name'), ['class' => 'huge'], TRUE );
      $this->add( 'textarea', 'description', E::ts('Training Description'), [ 'maxlength' => self::DESCRIPTION_MAX ] );
      $this->addYesNo( 'credits_awarded', E::ts( 'Award Credits?' ), '', FALSE );
      $this->add( 'text', 'credits', E::ts('Credits Awarded'), [ 'size' => 5, 'maxlength' => 5 ], TRUE );
      $this->add( 'datepicker', 'entry_date', ts('Date Awarded'), [], TRUE, [ 'time' => FALSE ] );

      $this->addRule( 'credits', E::ts('Please enter a valid credit.'), 'regex', '/[0-9]{1,2}/' );

      $this->addButtons([
        [
          'type'      => 'upload',
          'name'      => E::ts('Submit'),
          'isDefault' => TRUE,
        ],
      ]);
    } else {
      $this->addButtons([
        [
          'type'      => 'submit',
          'name'      => E::ts('Delete'),
          'isDefault' => TRUE
        ],
        [
          'type' => 'cancel',
          'name' => E::ts('Cancel')
        ]
      ]);
    }
    parent::buildQuickForm();
  }

  /**
   * This virtual function is used to set the default values of various form
   * elements.
   *
   * @return array|NULL
   *   reference to the array of default values
   */
  public function setDefaultValues() {
    if ($this->_trainingRecord) {
      $defaults = $this->_trainingRecord;
    } else {
      $defaults['label']           = '';
      $defaults['description']     = '';
      $defaults['credits']         = 0;
      $defaults['credits_awarded'] = 0;
      $defaults['entry_date']      = date('Y-m-d');
    }

    if ( empty( $defaults['contact_id'] ) ) {
      $defaults['contact_id'] = CRM_Utils_Request::retrieve('cid', 'Integer');
    }

    $this->assign( 'credits_awarded', $defaults['credits_awarded'] );
    return $defaults;
  }

  public function postProcess() {
    if ($this->_action == CRM_Core_Action::DELETE) {
      civicrm_api4('TrainingRecord', 'delete', ['where' => [['id', '=', $this->_id]]]);
      CRM_Core_Session::setStatus(E::ts('Removed Training Record'), E::ts('Training Record'), 'success');
    } else {
      $values = $this->controller->exportValues();
      $action = 'create';
      if ($this->getEntityId()) {
        $params['id'] = $this->getEntityId();
        $action       = 'update';
      }
      $this->assign( 'credits_awarded', $values['credits_awarded'] );

      $params['type_id']         = $values['type_id'];
      $params['contact_id']      = $values['contact_id'];
      $params['label']           = $values['label'];
      $params['description']     = $values['description'];
      $params['credits_awarded'] = $values['credits_awarded'];
      $params['credits']         = $values['credits'];
      $params['entry_date']      = $values['entry_date'];

      if ( 0 === $values['credits_awarded'] ) {
        $params['credits'] = 0;
      }

      civicrm_api4('TrainingRecord', $action, ['values' => $params]);
    }
    parent::postProcess();
  }

}
