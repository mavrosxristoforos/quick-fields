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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<div class="mod_quick_fields <?php print $params->get('moduleclass_sfx', ''); ?>">
  <?php if ($params->get('pre_text', '') != '') { ?>
    <div class="mod_quick_fields_pre_text"><?php print $params->get('pre_text', ''); ?></div>
  <?php } ?>
  <form name="quickfields<?php print $module->id; ?>" method="post">
    <?php print $form->renderFieldset('quickfields'); ?>

    <?php if (!empty($errors)) {
      HTMLHelper::_('bootstrap.alert');
      foreach($errors as $error) {
        ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php print $error; ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php print Text::_('MOD_QUICK_FIELDS_BUTTON_CLOSE_ARIA_TEXT'); ?>"></button>
          </div>
        <?php
      }
    } ?>

    <?php if (!empty($messages)) {
      HTMLHelper::_('bootstrap.alert');
      foreach($messages as $message) {
        ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php print $message; ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php print Text::_('MOD_QUICK_FIELDS_BUTTON_CLOSE_ARIA_TEXT'); ?>"></button>
          </div>
        <?php
      }
    } ?>

    <button type="submit" class="mod_quickfields_save btn btn-primary"><?php print $params->get('button_text', 'Save'); ?></button>
  </form>
</div>