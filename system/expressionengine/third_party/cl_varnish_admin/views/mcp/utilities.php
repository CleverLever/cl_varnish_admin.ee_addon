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
<h2>Utilities</h2>
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<tr><th colspan="2">Purge Item</th></th><th></th></tr>
	<tr>
		<td><label>Purge URL</label><div class="subtext">Purging an item deletes it from Varnish's memory.</div></td>
		<td>
			<input type="text" name="purge_url" value="" placeholder="ex: http://mydomain.com/">
		</td>
		<td align="right">
			<button name="action" type="submit" value="purge" class="submit" style="cursor: pointer">Purge</button>
		</td>
	</tr>
	<tr><th colspan="2">Ban Items</th></th><th></th></tr>	
	<tr>
		<td><label>Ban Path Expression</label><div class="subtext">Bans paths matching the provided VCL expression.</div></td>
		<td>
			<input type="text" name="path" value="" placeholder="ex: ^/pages$">
		</td>
		<td align="right">
			<button name="action" type="submit" value="ban_path" class="submit" style="cursor: pointer">Ban</button>
		</td>
	</tr>
	<tr>
		<td colspan="2"><label>Ban Site Cache</label><div class="subtext">Bans items matching the current site url.</div></td>	
		<td align="right">
			<button name="action" type="submit" value="ban_site" class="submit" style="cursor: pointer">Ban Site Cache</button>
		</td>
	</tr>
	<tr>
		<td colspan="2"><label>Ban Entire Cache</label><div class="subtext">Bans all items.</div></td>	
		<td align="right">
			<button name="action" type="submit" value="ban_all" class="submit" style="cursor: pointer">Ban Entire Cache</button>
		</td>
	</tr>
	<tr><th colspan="2">Refresh Item</th></th><th></th></tr>	
	<tr>
		<td><label>Refresh URL</label><div class="subtext">Requests the specified URL but forces a cache miss so the item is refreshed.</div></td>
		<td>
			<input type="text" name="refresh_url" value="" placeholder="ex: http://mydomain.com/">
		</td>
		<td align="right">
			<button name="action" type="submit" value="refresh" class="submit" style="cursor: pointer">Refresh</button>
		</td>
	</tr>

</table>
<?php echo form_close()?>