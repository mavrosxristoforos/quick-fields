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

use Joomla\CMS\MVC\Factory\MVCFactory; // Joomla 4 only!

class QFHelper {

  public static function getModel() {
    $factory = new MVCFactory('\\Joomla\\Component\\Fields');
    return $factory->createModel('Field', 'Administrator');
  }
}