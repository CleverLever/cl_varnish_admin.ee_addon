<style>
#mainContent .pageContents h2 { margin-bottom: 10px; }
#mainContent .pageContents table.mainTable { margin-bottom: 20px; }
.editAccordion td h4 { font-size: 13px; }
</style>
<script type="text/javascript">
$(function () {

});
</script>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method='.basename(__FILE__, '.php'))?>
<h2>Cached Items</h2>
<p>Items which have been cached via the {exp:varnish_admin:expire} tag.</p>
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<tr><th><input type="checkbox" name="select_all" value="true" class="toggle_all"></th><th>URL</th><th>Created</th><th>Expires</th><th>Warm?</th></tr>
	<? foreach($cached_items->collection()->result_array() as $cached_item): ?>
	<tr>
		<td><input type="checkbox" name="items[]" value="<?=$cached_item['hash'] ?>" class="toggle"></td>
		<td><?=$cached_item['uri'] ?></td>
		<td><?=$cached_item['created'] ?></td>
		<td><?=$cached_item['expires'] ?></td>
		<td><? if($cached_item['warm']): ?>Yes<? else: ?> No <? endif ?></td>
	</tr>
	<? endforeach ?>
	<tr>
		<td colspan="5">
			<select name="action">
				<option value="purge_and_warm">Purge and Warm</option>
				<option value="purge_and_force_warm">Purge and Force Warm</option>
				<option value="purge">Purge</option>
				<option value="delete">Delete</option>
			</select>
			<input type="submit" name="submit" value="Submit" class="submit">
		</td>	
	</tr>	
</table>
<?php echo form_close()?>