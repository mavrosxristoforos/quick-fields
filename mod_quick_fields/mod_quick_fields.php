<?php
/*------------------------------------------------------------------------
# mod_quick_fields - Quick Fields
# ------------------------------------------------------------------------
# author    Christopher Mavros (www.mavrosxristoforos.com)
# copyright Copyright (C) 2020 Mavrosxristoforos.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
# Websites: https://mavrosxristoforos.com
# Technical Support: https://mavrosxristoforos.com/contact
# FAQ: https://mavrosxristoforos.com/faq/111-nearestplacesfaq
# Forum: https://mavrosxristoforos.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$field_ids = $params->get('fields', array());

JPluginHelper::importPlugin('fields');
JHtml::_('jquery.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$form = new JForm('quickfields');
$xml = new DOMDocument('1.0', 'UTF-8');
$fieldsNode = $xml->appendChild(new DOMElement('form'))->appendChild(new DOMElement('fields'));
$fieldsNode->setAttribute('name', 'mod_quickfields');
$fieldset = $fieldsNode->appendChild(new DOMElement('fieldset'));
$fieldset->setAttribute('name', 'quickfields');

if (version_compare(JVERSION, '4', '>=')) {
  require_once(JPATH_SITE.'/modules/mod_quick_fields/helper.php');
  $model = QFHelper::getModel();
}
else {
  JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_fields/models/');
  $model = JModelLegacy::getInstance('Field', 'FieldsModel');
}

if (!$model) {
  print JText::_('MOD_QUICK_FIELDS_MODEL_NOT_FOUND');
  return;
}

$user = JFactory::getUser();

$fields = array();
foreach($field_ids as $field_id) {
  $field = $model->getItem($field_id);
  $field->params = new JRegistry($field->params);
  $field->fieldparams = new JRegistry($field->fieldparams);
  if($field->context == 'com_users.user') {
    if (!$user->guest) {
      $fieldValues = $model->getFieldValues($field_ids, $user->id);
      if (isset($fieldValues[$field_id])) {
        $field->value = $fieldValues[$field_id];
        $field->rawvalue = $field->value;

        $context = $field->context;
        $item = $user;

        if (version_compare(JVERSION, '4', '>=')) {
          $app->triggerEvent('onCustomFieldsBeforePrepareField', array($context, $item, &$field));
          $value = $app->triggerEvent('onCustomFieldsPrepareField', array($context, $item, &$field));
          if (is_array($value)) {
            $value = implode(' ', $value);
          }
          $app->triggerEvent('onCustomFieldsAfterPrepareField', array($context, $item, $field, &$value));
          $field->value = $value;
        }
        else {
          $dispatcher = JEventDispatcher::getInstance();
          $dispatcher->trigger('onCustomFieldsBeforePrepareField', array($context, $item, &$field));
          $value = $dispatcher->trigger('onCustomFieldsPrepareField', array($context, $item, &$field));
          if (is_array($value)) {
            $value = implode(' ', $value);
          }
          $dispatcher->trigger('onCustomFieldsAfterPrepareField', array($context, $item, $field, &$value));
          $field->value = $value;
        }
      }
    }
    else {
      // This field requires log-in
      continue;
    }
  }
  $app->triggerEvent('onCustomFieldsPrepareDom', array($field, $fieldset, $form));
  $fields[] = $field;
}
$form->load($xml->saveXML());

// Process posts. This affects the values.
if ($app->input->exists('mod_quickfields')) {
  $data = $app->input->get('mod_quickfields', array(), 'raw');
  foreach($fields as $field) {
    if (isset($data[$field->name])) {
      if($field->context == 'com_users.user') {
        if (!$user->guest) {
          $model->setFieldValue($field->id, $user->id, $data[$field->name]);
        }
        else {
          // This field requires log-in
        }
      }
    }
  }
}

// Loop through the fields again to set the value
foreach($fields as $field) {
  $data = null;
  if($field->context == 'com_users.user') {
    if (!$user->guest) {
      $data = $user;
    }
    else {
      // This field requires log-in
      continue;
    }
  }
  if (!is_null($data)) {
    $value = $model->getFieldValue($field->id, $data->id);
    if ($value === null) {
      continue;
    }
    if (!is_array($value) && $value !== '') {
      $formField = $form->getField($field->name, 'mod_quickfields');
      if ($formField && $formField->forceMultiple) {
        $value = (array) $value;
      }
    }

    $form->setValue($field->name, 'mod_quickfields', $value);
    $field->new_rawvalue = $value;
  }
}

// If we need notifications about field value changes, we need to loop through the fields again, so that the new values
// are parsed again into our $fields array, which is not affected just by calling $model->setFieldValue. That only alters the form we present later.
if ($params->get('enable_notifications', '1')) {
  $msg_fields_body = '';
  $row = 0;
  foreach($fields as $field) {
    if (!in_array($field->id, $params->get('notification_fields', array()))) { continue; }
    $field->original_value = $field->value;
    $field->value = $field->new_rawvalue;
    $field->rawvalue = $field->value;

    $context = $field->context;
    $item = $user;

    if (version_compare(JVERSION, '4', '>=')) {
      $app->triggerEvent('onCustomFieldsBeforePrepareField', array($context, $item, &$field));
      $value = $app->triggerEvent('onCustomFieldsPrepareField', array($context, $item, &$field));
      if (is_array($value)) {
        $value = implode(' ', $value);
      }
      $app->triggerEvent('onCustomFieldsAfterPrepareField', array($context, $item, $field, &$value));
      $field->value = $value; // This is the new value, nicely formatted
    }
    else {
      $dispatcher = JEventDispatcher::getInstance();
      $dispatcher->trigger('onCustomFieldsBeforePrepareField', array($context, $item, &$field));
      $value = $dispatcher->trigger('onCustomFieldsPrepareField', array($context, $item, &$field));
      if (is_array($value)) {
        $value = implode(' ', $value);
      }
      $dispatcher->trigger('onCustomFieldsAfterPrepareField', array($context, $item, $field, &$value));
      $field->value = $value; // This is the new value, nicely formatted
    }
    if ($field->value != $field->original_value) {
      $msg_fields_body .= '<tr'.(($row == 1) ? ' style="background-color: #f9f9f9;"' : '').'><td>'.$field->title.'</td><td>'.$field->original_value.'</td><td>'.$field->value.'</td><td>'.date('r').'</td></tr>';
      $row = 1-$row;
    }
  }
  
  if ($msg_fields_body != '') {
    // Create email
    $msg_fields_body = '<table width="80%" border="1"><tr style="background-color: #ccc; font-weight: bold;"><td>'.JText::_('MOD_QUICK_FIELDS_NOTIFICATION_HEADER_FIELD_TITLE').'</td><td>'.JText::_('MOD_QUICK_FIELDS_NOTIFICATION_HEADER_PREVIOUS_VALUE').'</td><td>'.JText::_('MOD_QUICK_FIELDS_NOTIFICATION_HEADER_CURRENT_VALUE').'</td><td>'.JText::_('MOD_QUICK_FIELDS_NOTIFICATION_HEADER_CHANGED_ON').'</td></tr>'.$msg_fields_body.'</table>';

    if (trim($params->get('notification_email', '')) != '') {
      // TODO: Create Email!
      $mail = JFactory::getMailer();
      $mail->isHTML(true);
      // Mail Recipients
      $recipients = $params->get('notification_email', '');
      foreach(explode(';', $recipients) as $recipient) {
        $mail->addRecipient($recipient);
      }
      // Sender
      $mail->setSender(array($app->getCfg('mailfrom'),$app->getCfg('fromname')));

      // Subject
      $subject = str_replace('%user%', $user->name.' (ID: '.$user->id.')', $params->get('notification_subject', 'Quick Fields Notification: %user% has changed some of his fields'));
      $mail->setSubject($subject);

      // Prepare Body
      $body = str_replace('%user%', $user->name.' (ID: '.$user->id.', Email: '.$user->email.')', $params->get('notification_body', '<h3>Quick Fields Notification</h3><p>Some changes have occured on fields you want to monitor.</p><p>User: %user%</p><p>%fields%</p>'));
      $preg_count = 0;
      $body = preg_replace('/<(p|span)>\s*%fields%\s*<\/\1>/i', $msg_fields_body, $body, -1, $preg_count);
      $str_count = 0;
      $body = str_replace('%fields%', $msg_fields_body, $body, $str_count);
      if (($str_count + $preg_count) == 0) {
        $body .= JText::_('MOD_QUICK_FIELDS_NO_FIELDS_TAG_IN_BODY_WARNING');
      }
      $mail->setBody($body);

      if ($mail->Send() !== true) {
        print JText::_('MOD_QUICK_FIELDS_FAILED_SENDING_NOTIFICATION_EMAIL');
      }
    }
    else {
      print JText::_('MOD_QUICK_FIELDS_NOTIFICATION_EMAIL_COULD_NOT_BE_SENT_BECAUSE_NO_RECIPIENT_SPECIFIED');
    }
  }
}

if ((is_array($form->getFieldset('quickfields'))) && (count($form->getFieldset('quickfields')) > 0)) {
  require JModuleHelper::getLayoutPath('mod_quick_fields');
}

?>
