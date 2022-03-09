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

namespace VDM\Joomla\Utilities;


/**
 * The json checker
 */
abstract class JsonHelper
{
	/**
	 * Check if you have a json string
	 *
	 * @input    string  $string  The json string to check
	 *
	 * @returns bool true on success
	 */
	public static function check($string): bool
	{
		if (StringHelper::check($string))
		{
			json_decode($string);
			return (json_last_error() === JSON_ERROR_NONE);
		}

		return false;
	}

	public static function string($value, $separator = ", ", $table = null, $id = 'id', $name = 'name')
	{
		// do some table foot work
		$external = false;
		if (strpos($table, '#__') !== false)
		{
			$external = true;
			$table = str_replace('#__', '', $table);
		}

		// check if string is JSON
		$result = json_decode($value, true);
		if (json_last_error() === JSON_ERROR_NONE)
		{
			// is JSON
			if (ArrayHelper::check($result))
			{
				if (StringHelper::check($table))
				{
					$names = array();
					foreach ($result as $val)
					{
						if ($external)
						{
							if ($_name = GetHelper::var(null, $val, $id, $name, '=', $table))
							{
								$names[] = $_name;
							}
						}
						else
						{
							if ($_name = GetHelper::var($table, $val, $id, $name))
							{
								$names[] = $_name;
							}
						}
					}
					if (ArrayHelper::check($names))
					{
						return (string) implode($separator, $names);
					}	
				}
				return (string) implode($separator, $result);
			}
			return (string) json_decode($value);
		}
		return $value;
	}

}

