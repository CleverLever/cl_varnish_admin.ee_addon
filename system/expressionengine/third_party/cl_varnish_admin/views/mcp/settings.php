<style>
#mainContent .pageContents h2 { margin-bottom: 10px; }
#mainContent .pageContents table.mainTable { margin-bottom: 20px; }
.editAccordion td h4 { font-size: 13px; }
</style>
<script type="text/javascript">
$(function () {

});
</script>

<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_varnish_admin'.AMP.'method='.basename(__FILE__, '.php')) ?>

<h2>Settings</h2>
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<caption>Varnish Server Settings</caption>
	<tr>
		<td><label>Hostname or IP Address</label><div class="subtext">Varnish server hostname or IP address.</div></td>
		<td>
			<input type="text" name="settings[host]" value="<?php echo @$settings->get('host'); ?>">
		</td>
	</tr>
	<tr>
		<td><label>Port</label><div class="subtext">Varnish server port (default is usually 6082).</div></td>
		<td>
			<input type="text" name="settings[port]" value="<?php echo @$settings->get('port'); ?>">
		</td>
	</tr>
	<tr>
		<td><label>Secret</label><div class="subtext">Varnish server secret.</div></td>
		<td>
			<input type="text" name="settings[secret]" value="<?php echo @$settings->get('secret'); ?>">
		</td>
	</tr>
</table>

<!--<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
	<caption>Cron Settings</caption>
	<tr>
		<td><label>Warm URL</label><div class="subtext">Have a cron job hit this URL periodically to warm expired cached items.</div></td>
		<td>
			<input type="text" value="<?php echo @$settings->get_act_url('warm_expired_cached_item'); ?>" disabled />
		</td>
	</tr>
</table>-->

<div class="tableFooter">
	<div class="tableSubmit">
		<?php echo form_submit(array('name' => 'submit', 'value' => lang('save'), 'class' => 'submit'));?>
	</div>
</div>	
<?php echo form_close()?>