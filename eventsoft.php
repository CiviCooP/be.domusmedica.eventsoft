<?php

require_once 'eventsoft.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eventsoft_civicrm_config(&$config) {
  _eventsoft_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eventsoft_civicrm_xmlMenu(&$files) {
  _eventsoft_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eventsoft_civicrm_install() {
  _eventsoft_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function eventsoft_civicrm_postInstall() {
  _eventsoft_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eventsoft_civicrm_uninstall() {
  _eventsoft_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eventsoft_civicrm_enable() {
  _eventsoft_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eventsoft_civicrm_disable() {
  _eventsoft_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eventsoft_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventsoft_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eventsoft_civicrm_managed(&$entities) {
  _eventsoft_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventsoft_civicrm_caseTypes(&$caseTypes) {
  _eventsoft_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function eventsoft_civicrm_angularModules(&$angularModules) {
  _eventsoft_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eventsoft_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventsoft_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function eventsoft_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function eventsoft_civicrm_navigationMenu(&$menu) {
  _eventsoft_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'be.domusmedica.eventsoft')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _eventsoft_civix_navigationMenu($menu);
} // */
function eventsoft_civicrm_buildForm($formName, &$form){
  if($formName == 'CRM_Event_Form_Participant'){
    $form->addElement('checkbox', 'is_different_contribution_contact', ts('Record Payment from a Different Contact?'),
      NULL,   array('onclick' => "return showHideByValue('is_different_contribution_contact','','record-different-contact','table-row','radio',false);"));
    $form->addSelect('soft_credit_type_id', array('entity' => 'contribution_soft'));
    $form->addEntityRef('soft_credit_contact_id', ts('Payment From'), array('create' => TRUE));
  }
}

function eventsoft_civicrm_postProcess($formName, &$form){
  if($formName == 'CRM_Event_Form_Participant') {
    if(!empty($form->_submitValues['soft_credit_contact_id'])) {
      try{
         $contributionId = civicrm_api3('ParticipantPayment', 'getvalue', array(
           'participant_id' => $form->_id,
           'return' => 'contribution_id'));
         $eventTitle = civicrm_api3('Event', 'getvalue', array(
           'return' => "title",
           'id' => $form->_submitValues['event_id']
         ));
         $displayName =  civicrm_api3('Contact', 'getvalue', array(
           'return' => "display_name",
           'id' => $form->_contactId,

         ));
         civicrm_api3('Contribution', 'create', array(
           'id' => $contributionId,
           'contact_id' => $form->_submitValues['soft_credit_contact_id'],
           'source' => $eventTitle . ' : ' . $displayName));
         $result = civicrm_api3('ContributionSoft', 'create', array(
           'contribution_id' => $contributionId,
           'amount' => $form->_submitValues['total_amount'] ,
           'contact_id' => $form->_contactId,
           'soft_credit_type_id' => $form->_submitValues['soft_credit_type_id'],

         ));
        // update label of lineitem too
        $newLabel = $eventTitle.' (t.b.v. '.$displayName.')';
        $query = 'UPDATE civicrm_line_item SET label = %1 WHERE contribution_id = %2';
        $queryParams = array(
          1 => array($newLabel, 'String'),
          2 => array($contributionId, 'Integer'),
        );
        try {
          CRM_Core_DAO::executeQuery($query, $queryParams);
        }
        catch (Exception $ex) {
          CRM_Core_Error::debug_log_message('Unable to change label into '.$newLabel. ' for line items with contribution id '.$contributionId);
        }
     } catch (CiviCRM_API3_Exception $ex){
        CRM_Core_Error::debug_var('Api Error in extension EventSoft', $ex->getExtraParams());
      }
    }
  }
  // issue 1961 add name to lineitem for membership soft credit
  if ($formName == 'CRM_Member_Form_Membership') {
    try {
      $betaaldDoorTypeId = civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => 'soft_credit_type',
        'name' => 'Betaald door',
        'return' => 'value',
      ));
      $formValues = $form->getVar('_params');
      $formAction = $form->getVar('_action');
      $contactId = $form->getVar('_contactID');
      if ($formAction == CRM_Core_Action::ADD || $formAction == CRM_Core_Action::UPDATE) {
        $membershipIds = $form->getVar('_membershipIDs');
        // if soft credit Betaald door, change line item for each membership
        if (isset($formValues['is_different_contribution_contact']) && $formValues['is_different_contribution_contact'] == TRUE && $formValues['soft_credit_type_id'] == $betaaldDoorTypeId) {
          $contactName = civicrm_api3('Contact', 'getvalue', array(
            'id' => $contactId,
            'return' => 'display_name',
          ));
          foreach ($membershipIds as $membershipId) {
            $membershipTypeId = civicrm_api3('Membership', 'getvalue', array(
              'id' => $membershipId,
              'return' => 'membership_type_id',
            ));
            $membershipType = civicrm_api3('MembershipType', 'getvalue', array(
              'id' => $membershipTypeId,
              'return' => 'name',
            ));
            $newLabel = $membershipType.' (t.b.v. '.$contactName.')';
            $query = 'UPDATE civicrm_line_item SET label = %1 WHERE entity_table = %2 AND entity_id = %3';
            $queryParams = array(
              1 => array($newLabel, 'String'),
              2 => array('civicrm_membership', 'String'),
              3 => array($membershipId, 'Integer'),
            );
            try {
              CRM_Core_DAO::executeQuery($query, $queryParams);
            }
            catch (Exception $ex) {
              CRM_Core_Error::debug_log_message('Unable to change label into '.$newLabel. ' for line items with membership id '.$membershipId);
            }
          }
        }
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }

  }
}






