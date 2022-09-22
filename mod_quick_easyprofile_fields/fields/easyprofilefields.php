<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Digicoaching
 * @author     Medicol-Digiclinic <support@medicol.ch>
 * @copyright  2019 Medicol-Digiclinic
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.helper');
jimport('joomla.form.formfield');
jimport('joomla.html.html');
JFormHelper::loadFieldClass('list');

use \Joomla\CMS\Factory;

/**
 * Shows a customized list of EasyProfile fields.
 *
 * @since  1.6
 */
class JFormFieldEasyProfileFields extends JFormFieldList
{
  /**
   * The form field type.
   *
   * @var        string
   * @since    1.6
   */
  protected $type = 'easyprofilefields';

  public function getOptions() {
    $db = JFactory::getDBO();
    if ($db->setQuery('SHOW TABLES LIKE "%jsn_fields%"')->loadResult()) {
      $db->setQuery('SELECT a.`id` as `value`, a.`title` as `text` FROM `#__jsn_fields` AS a WHERE a.`alias` <> "root" AND (a.`published` IN (0, 1)) ORDER BY a.`lft` asc');
      $results = $db->loadObjectList();
      foreach($results as $result) {
        if (JText::_($result->text) != $result->text) {
          $result->text = JText::_($result->text);
        }
      }
      return $results;
    }
    return array();
  }

}
