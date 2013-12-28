<style>
#mainContent .pageContents h2 { margin-bottom: 10px; }
#mainContent .pageContents table.mainTable { margin-bottom: 20px; }
#mainContent .pageContents .delete { width: 20px; } 
#mainContent .pageContents button { margin: 0; padding: 0; background: transparent; border: 0; cursor: hand; cursor: pointer; } 

#mainContent .pageContents table tbody > tr:first-child > td > [data-role='delete'] { display: none; }
.editAccordion td h4 { font-size: 13px; }

.channel-settings .channel-action select { width: 100%; }

</style>
<script>
(function ($) {
	
	$.fn.toggler = function () {
		function bind(el) {
			el.bind('change', function () {
				reset(el);
				$(el).closest('tr').find('[data-role="toggleable"][data-group="' + el.data('group') + '"][data-value="' + el.val() + '"]').fadeIn("fast");
			});
		}
		
		function reset(el) {
			$(el).closest('tr').find('[data-role="toggleable"][data-group="' + el.data('group') + '"]').prop('disabled', true).hide();
			$(el).closest('tr').find('[data-role="toggleable"][data-group="' + el.data('group') + '"]').find(':input').prop('disabled', true);
			$(el).closest('tr').find('[data-role="toggleable"][data-group="' + el.data('group') + '"][data-value="' + el.val() + '"]').prop('disabled', false).fadeIn("fast");
			$(el).closest('tr').find('[data-role="toggleable"][data-group="' + el.data('group') + '"][data-value="' + el.val() + '"]').find(':input').prop('disabled', false);
		}
		
		this.each(function () {
			reset($(this));
			bind($(this));
		});
		
		return this;
	}

})(jQuery);

$(function () {
	
	init();
	
	function init() {
		$('[data-role="toggler"]').toggler();
		$("[data-role='delete']").click(function (e) {
			$(this).closest('tr').remove();
			e.preventDefault();
		});
		$("[data-role='add_url']").unbind('click').click(function (e) {
			$source = $(this).closest('.cache-clear-rules-options').find(".cache-clear-rules-options-url").last();
			$clone = $source.clone();
			$source.after($clone);

			$clone.find(':input').val('');

			var index = $(this).closest('.cache-clear-rules-options').find(".cache-clear-rules-options-url").length - 1;
			console.log(index);
			$clone.find(':input').each(function() { $(this).attr('name', $(this).attr('name').replace(/^(.+)(\[\w+\])(\[.+)$/, "$1["+index+"]$3")); }); 

			init();

			e.preventDefault();
		});

		$("[data-role='add_rule']").unbind('click').click(function (e) {
			$source = $(".cache-clear-rule").last();
			$clone = $source.clone();
			$source.after($clone);

			$clone.find(':input').val('');

			var index = $(".cache-clear-rule").length - 1;
			$clone.find(':input').each(function() { $(this).attr('name', $(this).attr('name').replace(/^(cache_clear_rules)\[\w+\](\[.+)$/, "$1["+index+"]$2")); }); 

			init();

			e.preventDefault();
		});
	}

});
</script>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method='.basename(__FILE__, '.php'))?>
<!-- crappy default enter key button --><button style="overflow: visible !important; height: 0 !important; width: 0 !important; margin: 0 !important; border: 0 !important; padding: 0 !important; display: block !important;" type="submit" value=""></button>

<h2>Cache Clear Settings</h2>

<table class="mainTable cache-clear-settings-rules" border="0" cellspacing="0" cellpadding="0">
	<caption>Settings</caption>
	<tr>
		<td><label>Global Template Update Action</label><div class="subtext">Specify what should be cleared when a template is updated. This is executed in addition to any rules below.</div></td>
		<td class="channel-action">
			<select data-role="toggler" data-group="update_action" name="settings[global_template_update_action]">
				<option value="">Do Nothing</option>
				<option value="template" <? if ($settings->get('global_template_update_action') == "template"): ?>selected<? endif ?>>Clear Template's URL</option>
				<option value="group" <? if ($settings->get('global_template_update_action') == "group"): ?>selected<? endif ?>>Clear Template Group's URL</option>
				<option value="site" <? if ($settings->get('global_template_update_action') == "site"): ?>selected<? endif ?>>Clear Site Cache</option>
				<option value="entire" <? if ($settings->get('global_template_update_action') == "entire"): ?>selected<? endif ?>>Clear Entire Cache</option>
			</select>
		</td>
	</tr>
</table>

<table class="mainTable cache-clear-settings-rules" border="0" cellspacing="0" cellpadding="0">
	<caption>Rules</caption>
	<thead>
		<tr>
			<th>Hook</th>
			<th>Target</th>
			<th>Action</th>
			<th>Options</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<? $i = 0; ?>
		<? $rules = $cache_clear_rules->get_rules()->result_array() ?>
		<? if (count($rules) > 0): ?>
			<? foreach($cache_clear_rules->get_rules()->result_array() as $rule): ?>
				<? include("cache_clear_settings/rule.php") ?>
				<? $i++; ?>
			<? endforeach ?>
		<? else: ?>
			<? include("cache_clear_settings/rule.php") ?>
		<? endif ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5" align="right"><div><a href="#" data-role="add_rule">+ Add Another Rule</a></div></td>
		</tr>
	</tfoot>
</table>

<div class="tableFooter">
	<div class="tableSubmit">
		<?php echo form_submit(array('name' => 'submit', 'value' => lang('save'), 'class' => 'submit'));?>
	</div>
</div>	
<?php echo form_close()?>