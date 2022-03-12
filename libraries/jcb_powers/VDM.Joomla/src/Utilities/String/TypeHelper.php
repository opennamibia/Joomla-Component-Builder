<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    30th April, 2015
 * @author     Llewellyn van der Merwe <http://www.joomlacomponentbuilder.com>
 * @gitea      Joomla Component Builder <https://git.vdm.dev/joomla/Component-Builder>
 * @github     Joomla Component Builder <https://github.com/vdm-io/Joomla-Component-Builder>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\Utilities\String;


use Joomla\CMS\Component\ComponentHelper;
use VDM\Joomla\Utilities\StringHelper;


/**
 * Control the naming of a field type
 * 
 * @since  3.0.9
 */
abstract class TypeHelper
{
	/**
	 * The field builder switch
	 * 
	 * @since  3.0.9
	 */
	protected static $builder = false;

	/**
	 * Making field type name safe
	 *
	 * @input	string       The you would like to make safe
	 *
	 * @returns string on success
	 * 
	 * @since  3.0.9
	 */
	public static function safe($string)
	{
		// get global value
		if (self::$builder === false)
		{
			self::$builder = ComponentHelper::getParams('com_componentbuilder')->get('type_name_builder', 1);
		}

		// use the new convention
		if (2 == self::$builder)
		{
			// 0nly continue if we have a string
			if (StringHelper::check($string))
			{
				// check that the first character is not a number
				if (is_numeric(substr($string, 0, 1)))
				{
					$string = StringHelper::numbers($string);
				}

				// Transliterate string
				$string = StringHelper::transliterate($string);

				// remove all and keep only characters and numbers and point (TODO just one point)
				$string = trim(preg_replace("/[^A-Za-z0-9\.]/", '', $string));

				// best is to return lower (for all string equality in compiler)
				return strtolower($string);
			}
			// not a string
			return '';
		}

		// use the default (original behaviour/convention)
		return StringHelper::safe($string);
	}

}

