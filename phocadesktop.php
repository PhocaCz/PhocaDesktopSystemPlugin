<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Uri\UriImmutable;

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgSystemPhocaDesktop extends CMSPlugin
{

	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAfterRender() {

		$app 	= Factory::getApplication();


		if ($app->getName() != 'administrator') { return true;}

		$lang = $app->getLanguage();
		$lang->load('plg_system_phocadesktop');


		$format     = $app->input->get('format', '', 'string');
        $view 		= $app->input->get('view', '');
        $option 	= $app->input->get('option', '');
        $dashboard 	= $app->input->get('dashboard', '');

        if($option != 'com_cpanel') {
        	return true;
		}

        // Only main dashboard
		if ($dashboard != '') {
			return true;
		}

		if ($format == 'feed') { return true;}
		if ($format == 'pdf') { return true;}
		if ($format == 'raw') { return true;}
		if ($format == 'xml') { return true;}


		$shortcuts 			= $this->params->get('shortcuts', '');


		$o = [];
		if ($shortcuts != '') {
			$shortcuts = json_decode($shortcuts, true);

			if (!empty($shortcuts)) {
				foreach ($shortcuts as $k => $v) {

					$color = '#fff';
					$colorSvg = '';

					$o[] = '<div class="ph-shortcut-box">';
					if (isset($v['link']) && $v['link'] != ''){

						$attribute = '';
						if (isset($v['linkattribute']) && $v['linkattribute'] != ''){
							$attribute = strip_tags($v['linkattribute']);
						}

						$o[] = '<a href="' . Route::_($v['link']) . '" '.$attribute.'>';
					}


					if (isset($v['icon']) && $v['icon'] != '') {

						if (isset($v['color']) && $v['color'] != '') {
							$color = $v['color'];
						}

						if (isset($v['type']) && $v['type'] == 'add') {
							$o[] = '<div class="ph-shortcut-icon-pref"><span class="fas fa-plus-circle"></span></div>';
						}

						$o[] = '<div class="ph-shortcut-icon"><span class="' . $v['icon'] . '" style="color: '.$color.';" ></span></div>';
					} else if (isset($v['iconsvg']) && $v['iconsvg'] != '') {

						if (isset($v['color']) && $v['color'] != '') {
							$colorSvg = $v['color'];
						}

						if (isset($v['type']) && $v['type'] == 'add') {
							$o[] = '<div class="ph-shortcut-icon-pref"><span class="fas fa-plus-circle"></span></div>';
						}

						$style = '';
						if ($colorSvg != '') {
							$style = ' style="color: '.$colorSvg.'; fill: '.$colorSvg.'; stroke: '.$colorSvg.';"';
						}

						$o[] = '<div class="ph-shortcut-icon-svg"'.$style.'>'.$v['iconsvg'].'</div>';
					}

					if (isset($v['title']) && $v['title'] != '') {
						$o[] = '<div class="ph-shortcut-title">' . Text::_($v['title']) . '</div>';
					}

					if (isset($v['link']) && $v['link'] != '') {
						$o[] = '</a>';
					}

					$o[] = '</div>';


				}





				$old = '<div id="cpanel-modules">';
				$new = '<div id="ph-desktop">'.implode("\n", $o).'</div><div id="cpanel-modules">';

				$buffer = $app->getBody();
				$bufferNew = str_replace($old, $new, $buffer);
				$app->setBody($bufferNew);

			}
		}








		return true;
	}


	public function onAfterRoute() {

	}

	public function onAfterInitialise(){

	}

	public function onAfterDispatch() {

		$app 	= Factory::getApplication();
		$doc	= $app->getDocument();
		$wa = $doc->getWebAssetManager();

		if ($app->getName() != 'administrator') { return true;}

		$format     = $app->input->get('format', '', 'string');
        $view 		= $app->input->get('view', '');
        $option 	= $app->input->get('option', '');
        $dashboard 	= $app->input->get('dashboard', '');

        if($option != 'com_cpanel') {
        	return true;
		}

        // Only main dashboard
		if ($dashboard != '') {
			return true;
		}

		if ($format == 'feed') { return true;}
		if ($format == 'pdf') { return true;}
		if ($format == 'raw') { return true;}
		if ($format == 'xml') { return true;}

		$style 				= $this->params->get('style', 'desktop');
		$background_image 	= $this->params->get('background_image', '');

		if ($background_image != '' && $style != 'default') {


			$wa->addInlineStyle('
				#ph-desktop {
					background-image: url("'.Uri::root().$background_image.'");
					background-repeat: none;
					background-size: cover;
					background-position: center;
				}

			');
		}

		//HTMLHelper::_('stylesheet', 'media/plg_system_phocadesktop/css/'.htmlspecialchars(strip_tags($style)).'.css', array('version' => 'auto'));
		$wa->registerAndUseStyle('plg_system_phocadesktop.main', 'media/plg_system_phocadesktop/css/'.htmlspecialchars(strip_tags($style)).'.css', array('version' => 'auto'));

	}

}
?>
