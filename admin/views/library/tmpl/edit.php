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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$componentParams = $this->params; // will be removed just use $this->params instead
?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = jQuery('body');
	jQuery('<div id="loading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('components/com_componentbuilder/assets/images/import.gif') 50% 15% no-repeat")
		.css("top", outerDiv.position().top - jQuery(window).scrollTop())
		.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
		.css("width", outerDiv.width())
		.css("height", outerDiv.height())
		.css("position", "fixed")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.css("display", "none")
		.appendTo(outerDiv);
	jQuery('#loading').show();
	// when page is ready remove and show
	jQuery(window).load(function() {
		jQuery('#componentbuilder_loader').fadeIn('fast');
		jQuery('#loading').hide();
	});
</script>
<div id="componentbuilder_loader" style="display: none;">
<form action="<?php echo JRoute::_('index.php?option=com_componentbuilder&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('library.behaviour_above', $this); ?>
<div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'libraryTab', array('active' => 'behaviour')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'libraryTab', 'behaviour', JText::_('COM_COMPONENTBUILDER_LIBRARY_BEHAVIOUR', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('library.behaviour_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('library.behaviour_right', $this); ?>
			</div>
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('library.behaviour_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'libraryTab', 'files_folders_urls', JText::_('COM_COMPONENTBUILDER_LIBRARY_FILES_FOLDERS_URLS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('library.files_folders_urls_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'libraryTab', 'config', JText::_('COM_COMPONENTBUILDER_LIBRARY_CONFIG', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('library.config_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'libraryTab', 'linked', JText::_('COM_COMPONENTBUILDER_LIBRARY_LINKED', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('library.linked_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'libraryTab'; ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('core.edit.created_by') || $this->canDo->get('core.edit.created') || $this->canDo->get('library.edit.state') || ($this->canDo->get('library.delete') && $this->canDo->get('library.edit.state'))) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'libraryTab', 'publishing', JText::_('COM_COMPONENTBUILDER_LIBRARY_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('library.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('library.publlshing', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'libraryTab', 'permissions', JText::_('COM_COMPONENTBUILDER_LIBRARY_PERMISSION', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="library.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>

<div class="clearfix"></div>
<?php echo JLayoutHelper::render('library.behaviour_under', $this); ?>
</form>
</div>

<script type="text/javascript">

// #jform_how listeners for how_vvvvwci function
jQuery('#jform_how').on('keyup',function()
{
	var how_vvvvwci = jQuery("#jform_how").val();
	var target_vvvvwci = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwci(how_vvvvwci,target_vvvvwci);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var how_vvvvwci = jQuery("#jform_how").val();
	var target_vvvvwci = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwci(how_vvvvwci,target_vvvvwci);

});

// #jform_target listeners for target_vvvvwci function
jQuery('#jform_target').on('keyup',function()
{
	var how_vvvvwci = jQuery("#jform_how").val();
	var target_vvvvwci = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwci(how_vvvvwci,target_vvvvwci);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var how_vvvvwci = jQuery("#jform_how").val();
	var target_vvvvwci = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwci(how_vvvvwci,target_vvvvwci);

});

// #jform_how listeners for how_vvvvwck function
jQuery('#jform_how').on('keyup',function()
{
	var how_vvvvwck = jQuery("#jform_how").val();
	var target_vvvvwck = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwck(how_vvvvwck,target_vvvvwck);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var how_vvvvwck = jQuery("#jform_how").val();
	var target_vvvvwck = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwck(how_vvvvwck,target_vvvvwck);

});

// #jform_target listeners for target_vvvvwck function
jQuery('#jform_target').on('keyup',function()
{
	var how_vvvvwck = jQuery("#jform_how").val();
	var target_vvvvwck = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwck(how_vvvvwck,target_vvvvwck);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var how_vvvvwck = jQuery("#jform_how").val();
	var target_vvvvwck = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwck(how_vvvvwck,target_vvvvwck);

});

// #jform_how listeners for how_vvvvwcm function
jQuery('#jform_how').on('keyup',function()
{
	var how_vvvvwcm = jQuery("#jform_how").val();
	var target_vvvvwcm = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcm(how_vvvvwcm,target_vvvvwcm);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var how_vvvvwcm = jQuery("#jform_how").val();
	var target_vvvvwcm = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcm(how_vvvvwcm,target_vvvvwcm);

});

// #jform_target listeners for target_vvvvwcm function
jQuery('#jform_target').on('keyup',function()
{
	var how_vvvvwcm = jQuery("#jform_how").val();
	var target_vvvvwcm = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcm(how_vvvvwcm,target_vvvvwcm);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var how_vvvvwcm = jQuery("#jform_how").val();
	var target_vvvvwcm = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcm(how_vvvvwcm,target_vvvvwcm);

});

// #jform_how listeners for how_vvvvwco function
jQuery('#jform_how').on('keyup',function()
{
	var how_vvvvwco = jQuery("#jform_how").val();
	var target_vvvvwco = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwco(how_vvvvwco,target_vvvvwco);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var how_vvvvwco = jQuery("#jform_how").val();
	var target_vvvvwco = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwco(how_vvvvwco,target_vvvvwco);

});

// #jform_target listeners for target_vvvvwco function
jQuery('#jform_target').on('keyup',function()
{
	var how_vvvvwco = jQuery("#jform_how").val();
	var target_vvvvwco = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwco(how_vvvvwco,target_vvvvwco);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var how_vvvvwco = jQuery("#jform_how").val();
	var target_vvvvwco = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwco(how_vvvvwco,target_vvvvwco);

});

// #jform_how listeners for how_vvvvwcq function
jQuery('#jform_how').on('keyup',function()
{
	var how_vvvvwcq = jQuery("#jform_how").val();
	var target_vvvvwcq = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcq(how_vvvvwcq,target_vvvvwcq);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var how_vvvvwcq = jQuery("#jform_how").val();
	var target_vvvvwcq = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcq(how_vvvvwcq,target_vvvvwcq);

});

// #jform_target listeners for target_vvvvwcq function
jQuery('#jform_target').on('keyup',function()
{
	var how_vvvvwcq = jQuery("#jform_how").val();
	var target_vvvvwcq = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcq(how_vvvvwcq,target_vvvvwcq);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var how_vvvvwcq = jQuery("#jform_how").val();
	var target_vvvvwcq = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcq(how_vvvvwcq,target_vvvvwcq);

});

// #jform_target listeners for target_vvvvwcr function
jQuery('#jform_target').on('keyup',function()
{
	var target_vvvvwcr = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcr = jQuery("#jform_how").val();
	vvvvwcr(target_vvvvwcr,how_vvvvwcr);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var target_vvvvwcr = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcr = jQuery("#jform_how").val();
	vvvvwcr(target_vvvvwcr,how_vvvvwcr);

});

// #jform_how listeners for how_vvvvwcr function
jQuery('#jform_how').on('keyup',function()
{
	var target_vvvvwcr = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcr = jQuery("#jform_how").val();
	vvvvwcr(target_vvvvwcr,how_vvvvwcr);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var target_vvvvwcr = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcr = jQuery("#jform_how").val();
	vvvvwcr(target_vvvvwcr,how_vvvvwcr);

});

// #jform_how listeners for how_vvvvwcs function
jQuery('#jform_how').on('keyup',function()
{
	var how_vvvvwcs = jQuery("#jform_how").val();
	var target_vvvvwcs = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcs(how_vvvvwcs,target_vvvvwcs);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var how_vvvvwcs = jQuery("#jform_how").val();
	var target_vvvvwcs = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcs(how_vvvvwcs,target_vvvvwcs);

});

// #jform_target listeners for target_vvvvwcs function
jQuery('#jform_target').on('keyup',function()
{
	var how_vvvvwcs = jQuery("#jform_how").val();
	var target_vvvvwcs = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcs(how_vvvvwcs,target_vvvvwcs);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var how_vvvvwcs = jQuery("#jform_how").val();
	var target_vvvvwcs = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcs(how_vvvvwcs,target_vvvvwcs);

});

// #jform_target listeners for target_vvvvwct function
jQuery('#jform_target').on('keyup',function()
{
	var target_vvvvwct = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwct = jQuery("#jform_how").val();
	vvvvwct(target_vvvvwct,how_vvvvwct);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var target_vvvvwct = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwct = jQuery("#jform_how").val();
	vvvvwct(target_vvvvwct,how_vvvvwct);

});

// #jform_how listeners for how_vvvvwct function
jQuery('#jform_how').on('keyup',function()
{
	var target_vvvvwct = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwct = jQuery("#jform_how").val();
	vvvvwct(target_vvvvwct,how_vvvvwct);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var target_vvvvwct = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwct = jQuery("#jform_how").val();
	vvvvwct(target_vvvvwct,how_vvvvwct);

});

// #jform_how listeners for how_vvvvwcu function
jQuery('#jform_how').on('keyup',function()
{
	var how_vvvvwcu = jQuery("#jform_how").val();
	var target_vvvvwcu = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcu(how_vvvvwcu,target_vvvvwcu);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var how_vvvvwcu = jQuery("#jform_how").val();
	var target_vvvvwcu = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcu(how_vvvvwcu,target_vvvvwcu);

});

// #jform_target listeners for target_vvvvwcu function
jQuery('#jform_target').on('keyup',function()
{
	var how_vvvvwcu = jQuery("#jform_how").val();
	var target_vvvvwcu = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcu(how_vvvvwcu,target_vvvvwcu);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var how_vvvvwcu = jQuery("#jform_how").val();
	var target_vvvvwcu = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcu(how_vvvvwcu,target_vvvvwcu);

});

// #jform_target listeners for target_vvvvwcv function
jQuery('#jform_target').on('keyup',function()
{
	var target_vvvvwcv = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcv = jQuery("#jform_how").val();
	vvvvwcv(target_vvvvwcv,how_vvvvwcv);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var target_vvvvwcv = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcv = jQuery("#jform_how").val();
	vvvvwcv(target_vvvvwcv,how_vvvvwcv);

});

// #jform_how listeners for how_vvvvwcv function
jQuery('#jform_how').on('keyup',function()
{
	var target_vvvvwcv = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcv = jQuery("#jform_how").val();
	vvvvwcv(target_vvvvwcv,how_vvvvwcv);

});
jQuery('#adminForm').on('change', '#jform_how',function (e)
{
	e.preventDefault();
	var target_vvvvwcv = jQuery("#jform_target input[type='radio']:checked").val();
	var how_vvvvwcv = jQuery("#jform_how").val();
	vvvvwcv(target_vvvvwcv,how_vvvvwcv);

});

// #jform_target listeners for target_vvvvwcw function
jQuery('#jform_target').on('keyup',function()
{
	var target_vvvvwcw = jQuery("#jform_target input[type='radio']:checked").val();
	var type_vvvvwcw = jQuery("#jform_type input[type='radio']:checked").val();
	vvvvwcw(target_vvvvwcw,type_vvvvwcw);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var target_vvvvwcw = jQuery("#jform_target input[type='radio']:checked").val();
	var type_vvvvwcw = jQuery("#jform_type input[type='radio']:checked").val();
	vvvvwcw(target_vvvvwcw,type_vvvvwcw);

});

// #jform_type listeners for type_vvvvwcw function
jQuery('#jform_type').on('keyup',function()
{
	var target_vvvvwcw = jQuery("#jform_target input[type='radio']:checked").val();
	var type_vvvvwcw = jQuery("#jform_type input[type='radio']:checked").val();
	vvvvwcw(target_vvvvwcw,type_vvvvwcw);

});
jQuery('#adminForm').on('change', '#jform_type',function (e)
{
	e.preventDefault();
	var target_vvvvwcw = jQuery("#jform_target input[type='radio']:checked").val();
	var type_vvvvwcw = jQuery("#jform_type input[type='radio']:checked").val();
	vvvvwcw(target_vvvvwcw,type_vvvvwcw);

});

// #jform_target listeners for target_vvvvwcy function
jQuery('#jform_target').on('keyup',function()
{
	var target_vvvvwcy = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcy(target_vvvvwcy);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var target_vvvvwcy = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcy(target_vvvvwcy);

});

// #jform_target listeners for target_vvvvwcz function
jQuery('#jform_target').on('keyup',function()
{
	var target_vvvvwcz = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcz(target_vvvvwcz);

});
jQuery('#adminForm').on('change', '#jform_target',function (e)
{
	e.preventDefault();
	var target_vvvvwcz = jQuery("#jform_target input[type='radio']:checked").val();
	vvvvwcz(target_vvvvwcz);

});



<?php $numberAddconditions = range(0, count( (array) $this->item->addconditions) + 3, 1);?>

// for the values already set
jQuery(document).ready(function(){
<?php foreach($numberAddconditions as $fieldNr): ?>
	jQuery('#adminForm').on('change', '#jform_addconditions__addconditions<?php echo $fieldNr ?>__option_field',function (e) {
		e.preventDefault();
		getFieldSelectOptions(<?php echo $fieldNr ?>);
	});
<?php endforeach; ?>
	jQuery(document).on('subform-row-add', function(event, row){
		var groupName = jQuery(row).data('group');
		var fieldName = groupName.replace(/([0-9])/g, '');
		var fieldNr = groupName.replace(/([A-z_])/g, '');
		if ('addconditions' === fieldName) {
			jQuery('#adminForm').on('change', '#jform_addconditions__addconditions'+fieldNr+'__option_field',function (e) {
				e.preventDefault();
				getFieldSelectOptions(fieldNr);
			});
		}
	});
});

<?php
	$app = JFactory::getApplication();
?>
function JRouter(link) {
<?php
	if ($app->isClient('site'))
	{
		echo 'var url = "'.JURI::root().'";';
	}
	else
	{
		echo 'var url = "";';
	}
?>
	return url+link;
}

// nice little dot trick :)
jQuery(document).ready( function($) {
  var x=0;
  setInterval(function() {
	var dots = "";
	x++;
	for (var y=0; y < x%8; y++) {
		dots+=".";
	}
	$(".loading-dots").text(dots);
  } , 500);
});
</script>
