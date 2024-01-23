<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Digicoaching
 * @author     Medicol-Digiclinic <support@medicol.ch>
 * @copyright  2019 Medicol-Digiclinic
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Form\FormHelper;
use \Joomla\CMS\Form\Field\ListField;

/**
 * Shows a customized list of EasyProfile fields.
 *
 * @since  1.6
 */
class JFormFieldEasyProfileFields extends ListField
{
  /**
   * The form field type.
   *
   * @var        string
   * @since    1.6
   */
  protected $type = 'easyprofilefields';

  public function getOptions() {
    $db = Factory::getDBO();
    if ($db->setQuery('SHOW TABLES LIKE "%jsn_fields%"')->loadResult()) {
      $db->setQuery('SELECT a.`id` as `value`, a.`title` as `text` FROM `#__jsn_fields` AS a WHERE a.`alias` <> "root" AND (a.`published` IN (0, 1)) ORDER BY a.`lft` asc');
      $results = $db->loadObjectList();
      foreach($results as $result) {
        if (Text::_($result->text) != $result->text) {
          $result->text = Text::_($result->text);
        }
      }
      return $results;
    }
    return array();
  }

}
