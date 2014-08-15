<h2><?php echo empty($_GET['edit']) ? 'Add' : 'Edit'; ?> Phone</h2>
<hr />

<?php
	$dropdown_lines = polycomphones_dropdown_lines($_GET['edit']);
	$dropdown_attendant = polycomphones_dropdown_attendant();

	$newline = '
	<tr>
		<td class="sort"><img src="images/arrow_up_down.png" alt="sort" title="Drag up or down to reposition" /></td>
		<td class="index"></td>
		<td>'.form_dropdown('line[]', '', '', 'id="newline"').'</td>
		<td>'.form_dropdown('lineKeys[]', polycomphones_dropdown_numbers(1, 4, 1, true), '').'</td>	
		<td>'.form_dropdown('ringType[]', polycomphones_dropdown('ringType', true), '').'</td>	
		<td>'.form_dropdown('missedCallTracking[]', polycomphones_dropdown('disabled_enabled', true), '').'</td>
		<td>'.form_dropdown('callBackMode[]', polycomphones_dropdown('callBackMode', true), '').'</td>	
		<td><img src="images/trash.png" class="deleteline" style="cursor:pointer; float:none;" alt="remove" title="Click to delete line"></td>
	</tr>';
	
	$newattendant = '
	<tr>
		<td class="sort"><img src="images/arrow_up_down.png" alt="sort" title="Drag up or down to reposition" /></td>
		<td class="index"></td>
		<td>'.form_dropdown('attendant[]', '', '', 'id="newattendant"').'</td>
		<td>'.form_input('label[]', '', 'maxlength="30"').'</td>	
		<td><img src="images/trash.png" class="deleteattendant" style="cursor:pointer; float:none;" alt="remove" title="Click to delete attendent"></td>
	</tr>';
?>

<script type="text/javascript">
$(document).ready(function() {
	// MAC Validate
	$('input[name="mac"]').keyup(function() {
		$(this).removeClass("duplicate-exten");
		$(this).next("span").css("display", "none");
		var inputVal = $(this).val();
		var characterReg = /^([a-fA-F0-9]{12})$/;
		if(!characterReg.test(inputVal)) {
			$(this).addClass("duplicate-exten");
			$(this).next("span").css("display", "");
		}
	});
	
	$("form").submit(function( event ) {
		if($('input[name="mac"]').hasClass("duplicate-exten")) {
			event.preventDefault();
		}
	});

	// Functions
	var updateIndex = function(e, ui) {
		$('td.index', ui.item.parent()).each(function (i) {
			$(this).html(i + 1);
		});
	};

	var tableIndex = function(ui) {
		$('td.index', ui).each(function (i) {
			$(this).html(i + 1);
		});
	};
	
	var loadDropdown = function(ui, list) {
		$.each(list, function (key, cat) {
			var group = $('<optgroup>',{label:key});
			
			if(cat.length == 0) {
				$("<option/>",{value:key,text:cat}).appendTo(ui);
			} else {
				$.each(cat,function(subkey,item) {
					$("<option/>",{value:subkey,text:item}).appendTo(group);
				});
				group.appendTo(ui);
			}
		});
		ui.removeAttr('id');
	};

	// Lines
	$(".addline").on("click",function() {
		$("#lines").append('<?php echo json_encode($newline); ?>');
		loadDropdown($("#newline"), <?php echo json_encode($dropdown_lines); ?>);
		tableIndex($("#lines"));
	});

	$("#lines").delegate(".deleteline", "click", function() {
		var td = $(this).parent();
		var tr = td.parent();
		var table = tr.parent();
		tr.remove();
		tableIndex(table);
	});

	$("#lines tbody").sortable({
		handle: ".sort",
		stop: updateIndex
	});

	// Attendant Console
	$(".addattendant").on("click",function() {
		$("#attendants").append('<?php echo json_encode($newattendant); ?>');	
		loadDropdown($("#newattendant"), <?php echo json_encode($dropdown_attendant); ?>);
		tableIndex($("#attendants"));
	});

	$("#attendants").delegate(".deleteattendant", "click", function() {
		var td = $(this).parent();
		var tr = td.parent();
		var table = tr.parent();
		tr.remove();
		tableIndex(table);
	});

	$("#attendants tbody").sortable({
		handle: ".sort",
		stop: updateIndex
	});
});
</script>

<form name="polycomphones_phones_edit" method="post" action="config.php?type=setup&display=polycomphones&polycomphones_form=phones_edit&edit=<?php echo $_GET['edit'];?>">
<?php 
if(!empty($_GET['edit'])) { 
?>
<input type="button" value="Edit directory" onclick="location.href='config.php?type=setup&display=polycomphones&polycomphones_form=phones_directory&edit=<?php echo $device['mac'];?>'" />
<input type="button" value="Delete phone" title="Delete this phone" onclick="if(confirm('Are you sure you want to delete this phone?')) location.href='config.php?type=setup&display=polycomphones&polycomphones_form=phones_list&delete=<?php echo $_GET['edit'];?>'" />
<input type="button" value="Reboot phone" title="Reboot this phone" onclick="if(confirm('Are you sure you want to reboot this phone?')) location.href='config.php?type=setup&display=polycomphones&polycomphones_form=phones_list&checkconfig=<?php echo $_GET['edit'];?>'" />
<input type="button" value="Clear overrides" title="Clear local setting overrides on this phone" onclick="if(confirm('Are you sure you want to clear local setting overrides on this phone?')) location.href='config.php?type=setup&display=polycomphones&polycomphones_form=phones_list&clearoverrides=<?php echo $device['mac'];?>'" />
<?php 
} 
?>

<table>		
<tbody>
	<tr><td colspan="2"><h5><?php echo _("Phone Details")?><hr/></h5></td></tr>	
	<tr>
		<td width="175"><?php echo _("Phone Name")?></td>
		<td><?php echo form_input('name', $device['name'], 'size="30" maxlength="30"'); ?></td>	
	</tr>	
	<tr>
		<td><?php echo _("MAC Address")?></td>
		<?php if(!empty($_GET['edit'])) { ?>
		<td><?php echo $device['mac'] ?><input type="hidden" name="mac" value="<?php echo $device['mac'] ?>"></td>
		<?php } else { ?>
		<td>
			<?php echo form_input('mac', $device['mac'], 'size="15" maxlength="12"'); ?>
			<span style="display: none"><a href="#" title="Invalid MAC address">
				<img src="images/notify_critical.png" />
			</a></span>
		</td>	
		<?php } ?>
	</tr>	
	<tr><td colspan="3"><h5><?php echo _("Lines")?><hr/></h5></td></tr>	
	<tr>
		<td colspan="2">	
		<table id="lines">
		<thead>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo _("Line")?>*</td>
				<td><?php echo _("Line Keys")?><span class="help">?<span style="display: none;">Specify the number of line keys to use for a single registration.</span></span></td>
				<td><?php echo _("Ring Type")?></td>
				<td><?php echo _("Missed Call")?>*</td>
				<td><?php echo _("MWI Callback")?><span class="help">?<span style="display: none;">If 'Disabled', voice message retrieval and notification are disabled.</span></span></td>
				<td>&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=1;
			foreach($device['lines'] as $line) {
			?>
			<tr>
				<td class="sort"><img src="images/arrow_up_down.png" alt="sort" title="Drag up or down to reposition" /></td>
				<td class="index"><?php echo $i;?></td>
				<td><?php echo form_dropdown('line[]', $dropdown_lines, $line['line']); ?></td>
				<td><?php echo form_dropdown('lineKeys[]', polycomphones_dropdown_numbers(1, 4, 1, true), $line['settings']['lineKeys']); ?></td>	
				<td><?php echo form_dropdown('ringType[]', polycomphones_dropdown('ringType', true), $line['settings']['ringType']); ?></td>	
				<td><?php echo form_dropdown('missedCallTracking[]', polycomphones_dropdown('disabled_enabled', true), $line['settings']['missedCallTracking']); ?></td>
				<td><?php echo form_dropdown('callBackMode[]', polycomphones_dropdown('callBackMode', true), $line['settings']['callBackMode']); ?></td>	
				<td><img src="images/trash.png" class="deleteline" style="cursor:pointer; float:none;" alt="remove" title="Click to delete line"></td>
			</tr>
			<?php
				$i++;
			}
			?>
		</tbody>
		</table>
		<input type="button" class="addline" value="<?php echo _("Add Line")?>"/>
		</td>
	</tr>
	<tr><td colspan="3"><h5><?php echo _("Attendant Console")?><hr/></h5></td></tr>	
	<tr>
		<td colspan="2">	
		<table id="attendants">
		<thead>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo _("Attendant")?>*</td>
				<td><?php echo _("Custom Label")?>*</td>
				<td>&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=1;
			foreach($device['attendants'] as $attendant) {
			?>
			<tr>
				<td class="sort"><img src="images/arrow_up_down.png" alt="sort" title="Drag up or down to reposition" /></td>
				<td class="index"><?php echo $i;?></td>
				<td><?php echo form_dropdown('attendant[]', $dropdown_attendant, $attendant['attendant']); ?></td>
				<td><?php echo form_input('label[]', $attendant['label'], 'maxlength="30"'); ?></td>	
				<td><img src="images/trash.png" class="deleteattendant" style="cursor:pointer; float:none;" alt="remove" title="Click to delete attendent"></td>
			</tr>
			<?php
				$i++;
			}
			?>
		</tbody>
		</table>
		<input type="button" class="addattendant" value="<?php echo _("Add Attendant")?>"/>
		</td>
	</tr>
	<tr><td colspan="2"><h5><?php echo _("Phone Options")?><hr/></h5></td></tr>
	<?php 
	$phone_options = $device['settings'];
	$phone_default = true;
	require 'modules/polycomphones/views/polycomphones_phone_options.php'; 
	?>
	<tr><td colspan="2"><h5><?php echo _("Corporate Options")?><hr/></h5></td></tr>	
	<tr>
		<td><?php echo _("Corporate Directory")?></td>
		<td><?php echo form_dropdown('feature_corporateDirectory_enabled', polycomphones_dropdown('disabled_enabled', true), $device['settings']['feature_corporateDirectory_enabled']); ?></td>	
	</tr>
	<tr>
		<td><?php echo _("Exchange Calendar")?>*</td>
		<td><?php echo form_dropdown('feature_exchangeCalendar_enabled', polycomphones_dropdown('disabled_enabled', true), $device['settings']['feature_exchangeCalendar_enabled']); ?></td>	
	</tr>
</tbody>
</table>
<p>* Changing these fields will cause phone to restart</p>

<input type="hidden" name="action" value="edit">
<input type="submit" value="<?php echo _("Submit")?>">
</form>