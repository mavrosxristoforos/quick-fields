<?php
/*------------------------------------------------------------------------
# mod_quick_easyprofile_fields - Quick EasyProfile Fields
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
\defined( '_JEXEC' ) or die( 'Restricted access' );

use \Joomla\CMS\Factory;
use \Joomla\CMS\Helper\ModuleHelper;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Form\Form;
use \Joomla\Registry\Registry;
//use \Joomla\CMS\Plugin\PluginHelper;

$field_ids = $params->get('fields', array());

$joomla_user = Factory::getUser();
if ($joomla_user->guest) {
  // Requires login
  return;
}

$db = Factory::getDBO();
$query = $db->getQuery(true);
$query->select('a.*')->from('#__jsn_users AS a')->where('a.id = '.$joomla_user->id);
$db->setQuery( $query );
$jsn_user = $db->loadObject();

if (!$jsn_user) { return; } // EasyProfile user required!

$app = Factory::getApplication();

if ($params->get('hide_on_other', '0') > '0') {
  if ((strtolower($app->input->getVar('option')) == 'com_jsn') && (strtolower($app->input->getVar('view')) == 'profile')) {
    $viewed_profile_id = $app->input->getInt('id', -1);
    if (($viewed_profile_id != -1) && ($viewed_profile_id != $joomla_user->id)) {
      return; // Hide on other people's profiles.
    }
  }
}

$query = $db->getQuery(true);
$query->select('a.*')->from('#__jsn_fields AS a')->where('a.level = 2')->where('a.published = 1')->where('a.id IN ('.implode(',', $field_ids).')');
$db->setQuery( $query );
$fields = $db->loadObjectList();

//PluginHelper::importPlugin('fields');
HTMLHelper::_('jquery.framework');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$form = new Form('easyprofile_quickfields');
$xml = new DOMDocument('1.0', 'UTF-8');
$fieldsNode = $xml->appendChild(new DOMElement('form'))->appendChild(new DOMElement('fields'));
$fieldsNode->setAttribute('name', 'mod_quick_easyprofile_fields');
$fieldset = $fieldsNode->appendChild(new DOMElement('fieldset'));
$fieldset->setAttribute('name', 'easyprofile_quickfields');
$fieldset_xpath = '//fieldset[@name="easyprofile_quickfields"]';

if (!HtmlHelper::isRegistered('jsn.email')) {
  HtmlHelper::register('jsn.email', array('JsnUsermailFieldHelper', 'usermail'));
}
foreach($fields as $field) {
  $field->params = new Registry($field->params);

  $class='Jsn'.ucfirst($field->type).'FieldHelper';
  if(class_exists($class)) $class::loadData($field, $jsn_user, $joomla_user);

  if (!HtmlHelper::isRegistered('jsn.'.$field->type)) {
    HtmlHelper::register('jsn.'.$field->type, array($class, $field->type));
  }
  $xmlstring = trim($class::getXml($field));
  $fragment = $xml->createDocumentFragment();
  $fragment->appendXML($xmlstring);
  $fieldset->appendChild($fragment);
}
$form->load($xml->saveXML());

if ($app->input->exists('mod_quick_easyprofile_fields')) {
  $data = $app->input->get('mod_quick_easyprofile_fields', array(), 'raw');
  foreach($fields as $field) {
    if (isset($data[$field->alias])) {
      if (is_array($data[$field->alias])) {
        $value = $db->escape(json_encode($data[$field->alias]));
      }
      else {
        $value = $db->escape($data[$field->alias]);
      }
      $db->setQuery('UPDATE `#__jsn_users` SET `'.$field->alias.'` = "'.$value.'" WHERE `id` = "'.$jsn_user->id.'"')->execute();
    }
  }
}

// Reload $jsn_user, for the new values.
$query = $db->getQuery(true);
$query->select('a.*')->from('#__jsn_users AS a')->where('a.id = '.$joomla_user->id);
$db->setQuery( $query );
$jsn_user = $db->loadObject();

foreach($fields as $field) {
  $formField = $form->getField($field->alias, 'mod_quick_easyprofile_fields');
  if (isset($jsn_user->{$field->alias})) {
    // Get value
    $value = $jsn_user->{$field->alias};
    if ($value === null) {
      continue;
    }
    $decoded = json_decode($value, true);
    if (is_array($decoded)) {
      $result = $form->setValue($field->alias, 'mod_quick_easyprofile_fields', $decoded);
    }
    else {
      $result = $form->setValue($field->alias, 'mod_quick_easyprofile_fields', $value);
    }
  }
}

if ((is_array($form->getFieldset('easyprofile_quickfields'))) && (count($form->getFieldset('easyprofile_quickfields')) > 0)) {
  require ModuleHelper::getLayoutPath('mod_quick_easyprofile_fields');
}

?>
