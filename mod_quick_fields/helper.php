<?php
/*------------------------------------------------------------------------
# mod_quick_fields - Quick Fields
# ------------------------------------------------------------------------
# author    Christopher Mavros (https://mavxr.com)
# copyright Copyright (C) 2008 Mavxr.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
# Websites: https://mavxr.com
# Technical Support: https://mavxr.com/contact
# FAQ: https://mavxr.com/faq/111-nearestplacesfaq
# Forum: https://mavxr.com/forum
-------------------------------------------------------------------------*/

// no direct access
\defined( '_JEXEC' ) or die( 'Restricted access' );

use \Joomla\CMS\MVC\Factory\MVCFactory; // Joomla 4 only!

class QFHelper {

  public static function getModel() {
    $factory = new MVCFactory('\\Joomla\\Component\\Fields');
    return $factory->createModel('Field', 'Administrator');
  }
}