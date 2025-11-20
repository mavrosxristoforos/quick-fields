<?php
/*------------------------------------------------------------------------
# mod_quick_easyprofile_fields - Quick EasyProfile Fields
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

?>
<div class="mod_quick_fields <?php print $params->get('moduleclass_sfx', ''); ?>">
  <?php if ($params->get('pre_text', '') != '') { ?>
    <div class="mod_quick_fields_pre_text"><?php print $params->get('pre_text', ''); ?></div>
  <?php } ?>
  <form name="easyprofile_quickfields<?php print $module->id; ?>" method="post">
    <?php print $form->renderFieldset('easyprofile_quickfields'); ?>
    <button type="submit" class="mod_quickfields_save btn btn-primary"><?php print $params->get('button_text', 'Save'); ?></button>
  </form>
</div>