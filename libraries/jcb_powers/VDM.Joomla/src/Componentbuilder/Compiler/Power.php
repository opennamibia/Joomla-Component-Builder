<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    30th April, 2015
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        Joomla Component Builder <https://git.vdm.dev/joomla/Component-Builder>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\Componentbuilder\Compiler;


use Joomla\CMS\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use VDM\Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\JsonHelper;
use VDM\Joomla\Utilities\GuidHelper;
use VDM\Joomla\Utilities\String\ClassfunctionHelper;
use VDM\Joomla\Utilities\String\NamespaceHelper;
use VDM\Joomla\Componentbuilder\Compiler\Factory as Compiler;
use VDM\Joomla\Componentbuilder\Compiler\Config;
use VDM\Joomla\Componentbuilder\Compiler\Placeholder;
use VDM\Joomla\Componentbuilder\Compiler\Customcode;
use VDM\Joomla\Componentbuilder\Compiler\Customcode\Gui;


/**
 * Compiler Power
 * 
 * @since 3.2.0
 */
class Power
{
	/**
	 * All loaded powers
	 *
	 * @var    array
	 * @since 3.2.0
	 **/
	public array $active = [];

	/**
	 * The state of all loaded powers
	 *
	 * @var    array
	 * @since 3.2.0
	 **/
	protected array $state = [];

	/**
	 * Compiler Config
	 *
	 * @var    Config
	 * @since 3.2.0
	 **/
	protected Config $config;

	/**
	 * Compiler Placeholder
	 *
	 * @var    Placeholder
	 * @since 3.2.0
	 **/
	protected Placeholder $placeholder;

	/**
	 * Compiler Customcode
	 *
	 * @var    Customcode
	 * @since 3.2.0
	 **/
	protected Customcode $customcode;

	/**
	 * Compiler Customcode in Gui
	 *
	 * @var    Gui
	 * @since 3.2.0
	 **/
	protected Gui $gui;

	/**
	 * Database object to query local DB
	 *
	 * @var    \JDatabaseDriver
	 * @since 3.2.0
	 **/
	protected \JDatabaseDriver $db;

	/**
	 * Database object to query local DB
	 *
	 * @var    CMSApplication
	 * @since 3.2.0
	 **/
	protected CMSApplication $app;

	/**
	 * Constructor.
	 *
	 * @param Config|null             $config       The compiler config object.
	 * @param Placeholder|null        $placeholder  The compiler placeholder object.
	 * @param Customcode|null         $customcode   The compiler customcode object.
	 * @param Gui|null                $gui          The compiler customcode gui object.
	 * @param \JDatabaseDriver|null   $db           The Database Driver object.
	 * @param CMSApplication|null     $app          The CMS Application object.
	 *
	 * @since 3.2.0
	 */
	public function __construct(?Config $config = null, ?Placeholder $placeholder = null,
		?Customcode $customcode = null, ?Gui $gui = null,
		?\JDatabaseDriver $db = null, ?CMSApplication $app = null)
	{
		$this->config = $config ?: Compiler::_('Config');
		$this->placeholder = $placeholder ?: Compiler::_('Placeholder');
		$this->customcode = $customcode ?: Compiler::_('Customcode');
		$this->gui = $gui ?: Compiler::_('Customcode.Gui');
		$this->db = $db ?: Factory::getDbo();
		$this->app = $app ?: Factory::getApplication();
	}

	/**
	 * load all the powers linked to this component
	 *
	 * @param array   $guids    The global unique ids of the linked powers
	 *
	 * @return void
	 * @since 3.2.0
	 */
	public function load(array $guids)
	{
		if (ArrayHelper::check($guids))
		{
			foreach ($guids as $guid => $build)
			{
				$this->get($guid, $build);
			}
		}
	}

	/**
	 * Get a power
	 *
	 * @param string   $guid    The global unique id of the power
	 * @param int        $build    Force build switch (to override global switch)
	 *
	 * @return mixed
	 * @since 3.2.0
	 */
	public function get(string $guid, int $build = 0)
	{
		if (($this->config->get('add_power', true) || $build == 1) && $this->set($guid))
		{
			return $this->active[$guid];
		}

		return false;
	}

	/**
	 * Set a power
	 *
	 * @param string   $guid    The global unique id of the power
	 *
	 * @return bool
	 * @since 3.2.0
	 */
	protected function set(string $guid): bool
	{
		// check if we have been here before
		if (isset($this->state[$guid]))
		{
			return $this->state[$guid];
		}
		elseif (GuidHelper::valid($guid))
		{
			// Create a new query object.
			$query = $this->db->getQuery(true);

			$query->select('a.*');
			// from these tables
			$query->from('#__componentbuilder_power AS a');
			$query->where($this->db->quoteName('a.guid') . ' = ' . $this->db->quote($guid));
			$this->db->setQuery($query);
			$this->db->execute();
			if ($this->db->getNumRows())
			{
				// make sure that in recursion we
				// don't try to load this power again
				$this->state[$guid] = true;
				// get the power data
				$this->active[$guid] = $this->db->loadObject();
				// make sure to add any language strings found to all language files
				// since we can't know where this is used at this point
				$tmp_lang_target = $this->config->lang_target;
				$this->config->lang_target = 'both';
				// we set the fix usr if needed
				$fix_url
					= '"index.php?option=com_componentbuilder&view=powers&task=power.edit&id='
					. $this->active[$guid]->id . '" target="_blank"';
				// set some keys
				$this->active[$guid]->target_type = 'P0m3R!';
				$this->active[$guid]->key         = $this->active[$guid]->id . '_' . $this->active[$guid]->target_type;
				// now set the name
				$this->active[$guid]->name = $this->placeholder->update(
					$this->customcode->add($this->active[$guid]->name),
					$this->placeholder->active
				);
				// now set the code_name and class name
				$this->active[$guid]->code_name = $this->active[$guid]->class_name = ClassfunctionHelper::safe(
					$this->active[$guid]->name
				);
				// set official name
				$this->active[$guid]->official_name = StringHelper::safe(
					$this->active[$guid]->name, 'W'
				);
				// set namespace
				$this->active[$guid]->namespace = $this->placeholder->update(
					$this->active[$guid]->namespace, $this->placeholder->active
				);
				// validate namespace
				if (strpos($this->active[$guid]->namespace, '\\') === false)
				{
					// we raise an error message
					$this->app->enqueueMessage(
						Text::sprintf('COM_COMPONENTBUILDER_HTHREES_NAMESPACE_ERROR_SHTHREEPYOU_MUST_ATLEAST_HAVE_TWO_SECTIONS_IN_YOUR_NAMESPACE_YOU_JUST_HAVE_ONE_THIS_IS_AN_UNACCEPTABLE_ACTION_PLEASE_SEE_A_HREFS_PSRFOURA_FOR_MORE_INFOPPTHIS_S_WAS_THEREFORE_REMOVED_A_HREFSCLICK_HEREA_TO_FIX_THIS_ISSUEP',
							ucfirst($this->active[$guid]->type), $this->active[$guid]->name, $this->active[$guid]->namespace,
							'"https://www.php-fig.org/psr/psr-4/" target="_blank"', $this->active[$guid]->type,
							$fix_url),
						'Error'
					);
					$this->state[$guid] = false;
					unset($this->active[$guid]);
					// reset back to starting value
					$this->config->lang_target = $tmp_lang_target;
					// we break out here
					return false;
				}
				else
				{
					// setup the path array
					$path_array = (array) explode('\\', $this->active[$guid]->namespace);
					// make sure all sub folders in src dir is set and remove all characters that will not work in folders naming
					$this->active[$guid]->namespace = NamespaceHelper::safe(str_replace('.', '\\', $this->active[$guid]->namespace));
					// make sure it has two or more
					if (ArrayHelper::check($path_array) <= 1)
					{
						// we raise an error message
						$this->app->enqueueMessage(
							Text::sprintf('COM_COMPONENTBUILDER_HTHREES_NAMESPACE_ERROR_SHTHREEPYOU_MUST_ATLEAST_HAVE_TWO_SECTIONS_IN_YOUR_NAMESPACE_YOU_JUST_HAVE_ONE_S_THIS_IS_AN_UNACCEPTABLE_ACTION_PLEASE_SEE_A_HREFS_PSRFOURA_FOR_MORE_INFOPPTHIS_S_WAS_THEREFORE_REMOVED_A_HREFSCLICK_HEREA_TO_FIX_THIS_ISSUEP',
								ucfirst($this->active[$guid]->type), $this->active[$guid]->name, $this->active[$guid]->namespace,
								'"https://www.php-fig.org/psr/psr-4/" target="_blank"', $this->active[$guid]->type,
								$fix_url),
							'Error'
						);
						$this->state[$guid] = false;
						unset($this->active[$guid]);
						// reset back to starting value
						$this->config->lang_target = $tmp_lang_target;
						// we break out here
						return false;
					}
					// get the file and class name (the last value in array)
					$file_name = array_pop($path_array);
					// src array bucket
					$src_array = array();
					// do we have src folders
					if (strpos($file_name, '.') !== false)
					{
						// we have src folders in the namespace
						$src_array = (array) explode('.', $file_name);
						// get the file and class name (the last value in array)
						$this->active[$guid]->file_name = array_pop($src_array);
						// namespace array
						$namespace_array = array_merge($path_array, $src_array);
					}
					else
					{
						// set the file name
						$this->active[$guid]->file_name = $file_name;
						// namespace array
						$namespace_array = $path_array;
					}
					// the last value is the same as the class name
					if ($this->active[$guid]->file_name !== $this->active[$guid]->class_name)
					{
						// we raise an error message
						$this->app->enqueueMessage(
							Text::sprintf('COM_COMPONENTBUILDER_PS_NAMING_MISMATCH_ERROR_SPPTHE_S_NAME_IS_BSB_AND_THE_ENDING_FILE_NAME_IN_THE_NAMESPACE_IS_BSB_THIS_IS_BAD_CONVENTION_PLEASE_SEE_A_HREFS_PSRFOURA_FOR_MORE_INFOPPA_HREFSCLICK_HEREA_TO_FIX_THIS_ISSUEP',
								ucfirst($this->active[$guid]->type), $this->active[$guid]->name, $this->active[$guid]->type, $this->active[$guid]->class_name, $this->active[$guid]->file_name,
								'"https://www.php-fig.org/psr/psr-4/" target="_blank"',
								$fix_url),
							'Error'
						);
						$this->state[$guid] = false;
						unset($this->active[$guid]);
						// reset back to starting value
						$this->config->lang_target = $tmp_lang_target;
						// we break out here
						return false;
					}
					// make sure the arrays are namespace safe
					$path_array      = array_map(function ($val) {
						return NamespaceHelper::safe($val);
					}, $path_array);
					$namespace_array = array_map(function ($val) {
						return NamespaceHelper::safe($val);
					}, $namespace_array);
					// set the actual class namespace
					$this->active[$guid]->_namespace = implode('\\', $namespace_array);
					// prefix values
					$this->active[$guid]->_namespace_prefix = $path_array;
					// get the parent folder (the first value in array)
					$prefix_folder = implode('.', $path_array);
					// make sub folders if still found
					$sub_folder = '';
					if (ArrayHelper::check($src_array))
					{
						// make sure the arrays are namespace safe
						$sub_folder = '/' . implode('/', array_map(function ($val) {
								return NamespaceHelper::safe($val);
							}, $src_array));
					}
					// now we set the paths
					$this->active[$guid]->path_jcb    = $this->config->get('jcb_powers_path', 'libraries/jcb_powers');
					$this->active[$guid]->path_parent = $this->active[$guid]->path_jcb . '/' . $prefix_folder;
					$this->active[$guid]->path        = $this->active[$guid]->path_parent . '/src' . $sub_folder;
				}
				// load use ids
				$use = array();
				$as = array();
				// check if we have use selection
				$this->active[$guid]->use_selection = (isset($this->active[$guid]->use_selection)
					&& JsonHelper::check(
						$this->active[$guid]->use_selection
					)) ? json_decode($this->active[$guid]->use_selection, true) : null;
				if ($this->active[$guid]->use_selection)
				{
					$use = array_values(array_map(function ($u) use(&$as) {
						// track the AS options
						if (empty($u['as']))
						{
							$as[$u['use']] = 'default';
						}
						else
						{
							$as[$u['use']] = (string) $u['as'];
						}
						// return the guid
						return $u['use'];
					}, $this->active[$guid]->use_selection));
				}
				// check if we have load selection
				$this->active[$guid]->load_selection = (isset($this->active[$guid]->load_selection)
					&& JsonHelper::check(
						$this->active[$guid]->load_selection
					)) ? json_decode($this->active[$guid]->load_selection, true) : null;
				if ($this->active[$guid]->load_selection)
				{
					// load use ids
					array_map(function ($l) {
						// just load it directly and be done with it
						return $this->set($l['load']);
					}, $this->active[$guid]->load_selection);
				}
				// see if we have implements
				$this->active[$guid]->implement_names = array();
				// does this implement
				$this->active[$guid]->implements = (isset($this->active[$guid]->implements)
					&& JsonHelper::check(
						$this->active[$guid]->implements
					)) ? json_decode($this->active[$guid]->implements, true) : null;
				if ($this->active[$guid]->implements)
				{
					foreach ($this->active[$guid]->implements as $implement)
					{
						if ($implement == -1
							&& StringHelper::check($this->active[$guid]->implements_custom))
						{
							$this->active[$guid]->implement_names[] = $this->placeholder->update(
								$this->customcode->add($this->active[$guid]->implements_custom),
								$this->placeholder->active
							);
							// just add this once
							unset($this->active[$guid]->implements_custom);
						}
						// does this extend existing
						elseif (GuidHelper::valid($implement))
						{
							// check if it was set
							if ($this->set($implement))
							{
								// get the name
								$this->active[$guid]->implement_names[] = $this->get($implement, 1)->class_name;
								// add to use
								$use[] = $implement;
							}
						}
					}
				}
				// does this extend something
				$this->active[$guid]->extends_name = null;
				// we first check for custom extending options
				if ($this->active[$guid]->extends == -1
					&& StringHelper::check($this->active[$guid]->extends_custom))
				{
					$this->active[$guid]->extends_name = $this->placeholder->update(
						$this->customcode->add($this->active[$guid]->extends_custom),
						$this->placeholder->active
					);
					// just add once
					unset($this->active[$guid]->extends_custom);
				}
				// does this extend existing
				elseif (GuidHelper::valid($this->active[$guid]->extends))
				{
					// check if it was set
					if ($this->set($this->active[$guid]->extends))
					{
						// get the name
						$this->active[$guid]->extends_name = $this->get($this->active[$guid]->extends, 1)->class_name;
						// add to use
						$use[] = $this->active[$guid]->extends;
					}
				}
				// set GUI mapper
				$guiMapper = array('table' => 'power', 'id' => (int) $this->active[$guid]->id, 'type' => 'php');
				// add the header script
				if ($this->active[$guid]->add_head == 1)
				{
					// set GUI mapper field
					$guiMapper['field'] = 'head';
					// base64 Decode code
					$this->active[$guid]->head = $this->gui->set(
							$this->placeholder->update(
								$this->customcode->add(
									base64_decode(
										$this->active[$guid]->head
									)
								), $this->placeholder->active
							),
							$guiMapper
						) . PHP_EOL;
				}
				// now add all the extra use statements
				if (ArrayHelper::check($use))
				{
					foreach (array_unique($use) as $u)
					{
						if ($this->set($u))
						{
							$add_use = $this->get($u, 1)->namespace;
							// check if it is already added manually, you know how some people are
							if (strpos($this->active[$guid]->head, $add_use) === false)
							{
								// check if it has an AS option
								if (isset($as[$u]) && StringHelper::check($as[$u]) && $as[$u] !== 'default')
								{
									$this->active[$guid]->head .= 'use ' . $add_use . ' as ' . $as[$u] . ';' . PHP_EOL;
								}
								else
								{
									$this->active[$guid]->head .= 'use ' . $add_use . ';' . PHP_EOL;
								}
							}
						}
					}
				}
				// now set the description
				$this->active[$guid]->description = (StringHelper::check($this->active[$guid]->description)) ? $this->placeholder->update(
					$this->customcode->add($this->active[$guid]->description),
					$this->placeholder->active
				) : '';
				// add the main code if set
				if (StringHelper::check($this->active[$guid]->main_class_code))
				{
					// set GUI mapper field
					$guiMapper['field'] = 'main_class_code';
					// base64 Decode code
					$this->active[$guid]->main_class_code = $this->gui->set(
						$this->placeholder->update(
							$this->customcode->add(
								base64_decode(
									$this->active[$guid]->main_class_code
								)
							), $this->placeholder->active
						),
						$guiMapper
					);
				}
				// reset back to starting value
				$this->config->lang_target = $tmp_lang_target;

				return true;
			}
		}
		// we failed to get the power,
		// so we raise an error message
		// only if guid is valid
		if (GuidHelper::valid($guid))
		{
			$this->app->enqueueMessage(
				Text::sprintf('COM_COMPONENTBUILDER_PPOWER_BGUIDSB_NOT_FOUNDP', $guid),
				'Error'
			);
		}
		// let's not try again
		$this->state[$guid] = false;

		return false;
	}
}

