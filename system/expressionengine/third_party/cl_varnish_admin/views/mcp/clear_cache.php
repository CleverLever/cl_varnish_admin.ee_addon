<style>
#mainContent .pageContents h2 { margin-bottom: 10px; }
#mainContent .pageContents table.mainTable { margin-bottom: 20px; }
.editAccordion td h4 { font-size: 13px; }
</style>
<script type="text/javascript">
$(function () {

});
</script>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method=clear_cache')?>
<h2>Clear Cache</h2>
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<tr><th colspan="2">Clear Items</th></th><th></th></tr>
	<tr>
		<td><label>Clear URL</label><div class="subtext">Clear items that BEGIN with the specified URL.</td>
		<td>
			<input type="text" name="url" value="" placeholder="ex: /pages">
		</td>
		<td align="right">
			<button name="action" type="submit" value="url" class="submit" style="cursor: pointer">Clear</button>
		</td>
	</tr>
	<tr>
		<td colspan="2"><label>Clear Site Cache</label></td>	
		<td align="right">
			<button name="action" type="submit" value="site" class="submit" style="cursor: pointer">Clear Site Cache</button>
		</td>
	</tr>
	<tr>
		<td colspan="2"><label>Clear Entire Cache</label></td>	
		<td align="right">
			<button name="action" type="submit" value="all" class="submit" style="cursor: pointer">Clear Entire Cache</button>
		</td>
	</tr>
</table>
<?php echo form_close()?>