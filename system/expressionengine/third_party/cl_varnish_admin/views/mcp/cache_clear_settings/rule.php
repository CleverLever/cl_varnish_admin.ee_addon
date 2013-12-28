<tr class="cache-clear-rule">
	<!-- @group hook -->
	<td>
		<input type="hidden" name="cache_clear_rules[<?=$i?>][id]" value="<?= @$rule['id'] ?>" />
		<select data-role="toggler" data-group="hook" name="cache_clear_rules[<?=$i?>][hook]">
			<option value="">---</option>
			<option value="entry_submission_end" <? if (@$rule['hook'] == "entry_submission_end"): ?>selected<? endif ?>>Channel Entry Edit/Publish</option>
			<option value="update_template_end" <? if (@$rule['hook'] == "update_template_end"): ?>selected<? endif ?>>Template Update</option>
		</select>			
	</td>
	<!-- @end hook -->
	
	<!-- @group entry_submission_end -->
	<!-- @group target -->
	<td data-role="toggleable" data-group="hook" data-value="entry_submission_end">
		<select name="cache_clear_rules[<?=$i?>][target]">
			<? foreach($cache_clear_rules->get_sites()->result_array() as $site): ?>
				<optgroup label="<?= $site['site_label'] ?>">
					<? foreach($cache_clear_rules->get_channels($site['site_id'])->result_array() as $channel): ?>
						<option value="<?= $channel['channel_id'] ?>" <? if (@$rule['target'] == $channel['channel_id']): ?>selected<? endif ?>><?=$channel['channel_title']?></option>
					<? endforeach ?>
				</optgroup>
			<? endforeach ?>
		</select>
	</td>
	<!-- @end target -->

	<!-- @group action -->
	<td data-role="toggleable" data-group="hook" data-value="entry_submission_end">
			<select name="cache_clear_rules[<?=$i?>][action]" data-role="toggler" data-group="entry_submission_end_action">
				<option value="">---</option>
				<option value="custom" <? if (@$rule['hook'] == "entry_submission_end" && @$rule['action'] == "custom"): ?>selected<? endif ?>>Clear Custom URL(s)</option>
				<option value="site" <? if (@$rule['hook'] == "entry_submission_end" && @$rule['action'] == "site"): ?>selected<? endif ?>>Clear Site Cache</option>
				<option value="entire" <? if (@$rule['hook'] == "entry_submission_end" && @$rule['action'] == "entire"): ?>selected<? endif ?>>Clear Entire Cache</option>
			</select>
	</td>
	<!-- @end action -->

	<!-- @group options -->
	<td class="cache-clear-rules-options" data-role="toggleable" data-group="hook" data-value="entry_submission_end">
		<div data-role="toggleable" data-group="entry_submission_end_action" data-value="site">This site's cache will clear when this hook is called.</div>
		<div data-role="toggleable" data-group="entry_submission_end_action" data-value="entire">The entire cache will clear when this hook is called.</div>
		<table width="100%" data-role="toggleable" data-group="entry_submission_end_action" data-value="custom">
			<thead>
				<th class="editAccordion">VCL Expression</th><th class="editAccordion">Warm URL (optional)</th><th class="editAccordion"></th>
			</thead>
			<tbody>
				<? if (@$rule['hook'] == "entry_submission_end" && count(@$rule['options']) > 0): ?>
					<? foreach(@$rule['options'] as $ii => $option): ?>
					<tr class="cache-clear-rules-options-url">
						<td><input type="text" name="cache_clear_rules[<?=$i?>][options][<?=$ii?>][expression]" value="<?= @$option['expression'] ?>" placeholder="ex: ^/page/{url_title}$"> </td>
						<td>
							<input type="text" name="cache_clear_rules[<?=$i?>][options][<?=$ii?>][warm_url]" value="<?= @$option['warm_url'] ?>" placeholder="ex: /page/{url_title}"  data-role="toggleable" data-group="warm" data-value="1">
						</td>
						<td><a href="#" data-role="delete"><img src="<?php echo $this->config->item('theme_folder_url').'cp_themes/default/images/content_custom_tab_delete.png' ?>" alt="Delete"/></a></td>
					</tr>
					<?php endforeach ?>
				<? else: ?>
					<tr class="cache-clear-rules-options-url">
						<td><input type="text" name="cache_clear_rules[<?=$i?>][options][0][expression]" placeholder="ex: ^/page/{url_title}$"></td>
						<td>
							<input type="text" name="cache_clear_rules[<?=$i?>][options][0][warm_url]" value="" placeholder="ex: /page/{url_title}" data-role="toggleable" data-group="warm" data-value="1">
						</td>
						<td><a href="#" data-role="delete"><img src="<?php echo $this->config->item('theme_folder_url').'cp_themes/default/images/content_custom_tab_delete.png' ?>" alt="Delete"/></a></td>
					</tr>
				<? endif ?>
			</tbody>
			<tfoot>
				<td colspan="3" align="right">
					<div><a href="#" data-role="add_url">+ Add Another URL</a></div>
				</td>
			</tfoot>
		</table>
	</td>
	<!-- @end options -->
	<!-- @end entry_submission_end -->
	
	<!-- @group update_template_end -->
	<!-- @group target -->
	<td data-role="toggleable" data-group="hook" data-value="update_template_end">		
		<select name="cache_clear_rules[<?=$i?>][target]">
			<? foreach($cache_clear_rules->get_template_groups()->result_array() as $template_group): ?>
				<optgroup label="<?=$template_group['group_name']?>">
					<?php foreach($cache_clear_rules->get_templates(array(), array("exp_template_groups.group_id" => $template_group['group_id']))->result_array() as $template): ?>
						<option value="<?= $template['template_id'] ?>" <? if (@$rule['target'] == $template['template_id']): ?>selected<? endif ?>><?=$template['template_name']?></option>
					<?php endforeach ?>
				</optgroup>
			<? endforeach ?>
		</select>
	</td>
	<!-- @end target -->

	<!-- @group action -->
	<td data-role="toggleable" data-group="hook" data-value="update_template_end">		
		<select name="cache_clear_rules[<?=$i?>][action]" data-role="toggler" data-group="update_template_end_action">
			<option value="">---</option>
			<option value="custom" <? if (@$rule['hook'] == "update_template_end" && @$rule['action'] == "custom"): ?>selected<? endif ?>>Clear Custom URL(s)</option>
			<option value="site" <? if (@$rule['hook'] == "update_template_end" && @$rule['action'] == "site"): ?>selected<? endif ?>>Clear Site Cache</option>
			<option value="entire" <? if (@$rule['hook'] == "update_template_end" && @$rule['action'] == "entire"): ?>selected<? endif ?>>Clear Entire Cache</option>
		</select>
	</td>
	<!-- @end action -->

	<!-- @group options -->	
	<td class="cache-clear-rules-options" data-role="toggleable" data-group="hook" data-value="update_template_end">
		<div data-role="toggleable" data-group="update_template_end_action" data-value="site">This site's cache will clear when this hook is called.</div>
		<div data-role="toggleable" data-group="update_template_end_action" data-value="entire">The entire cache will clear when this hook is called.</div>
		<table width="100%" data-role="toggleable" data-group="update_template_end_action" data-value="custom">
			<thead>
				<th class="editAccordion">URL</th><th class="editAccordion">Warm?</th><th class="editAccordion"></th>
			</thead>
			<tbody>
				<? if (@$rule['hook'] == "update_template_end" && count(@$rule['options']) > 0): ?>
					<? foreach(@$rule['options'] as $ii => $option): ?>
					<tr class="cache-clear-rules-options-url">
						<td><input type="text" name="cache_clear_rules[<?=$i?>][options][<?=$ii?>][url]" value="<?= $option['url'] ?>" placeholder="ex: /my-url"></td>
						<td><input type="checkbox" name="cache_clear_rules[<?=$i?>][options][<?=$ii?>][warm]" value="1" <? if (@$option['warm']): ?>checked<? endif ?>></td>
						<td><a href="#" data-role="delete"><img src="<?php echo $this->config->item('theme_folder_url').'cp_themes/default/images/content_custom_tab_delete.png' ?>" alt="Delete"/></a></td>
					</tr>
					<?php endforeach ?>
				<? else: ?>
					<tr class="cache-clear-rules-options-url">
						<td><input type="text" name="cache_clear_rules[<?=$i?>][options][0][url]" placeholder="ex: /my-url"></td>
						<td><input type="checkbox" name="cache_clear_rules[<?=$i?>][options][0][warm]" value="1"></td>
						<td><a href="#" data-role="delete"><img src="<?php echo $this->config->item('theme_folder_url').'cp_themes/default/images/content_custom_tab_delete.png' ?>" alt="Delete"/></a></td>
					</tr>
				<? endif ?>
			</tbody>
			<tfoot>
				<td colspan="3" align="right">
					<div><a href="#" data-role="add_url">+ Add Another URL</a></div>
				</td>
			</tfoot>
		</table>
	</td>
	<!-- @end options -->
	<!-- @end update_template_end -->
	
	<td>
		<div><button name="delete" type="submit" value="<?= @$rule['id'] ?>" onclick="return confirm('Are you sure?');"><img src="<?php echo $this->config->item('theme_folder_url').'cp_themes/default/images/content_custom_tab_delete.png' ?>" alt="Delete"/></button></div>
	</td>
</tr>