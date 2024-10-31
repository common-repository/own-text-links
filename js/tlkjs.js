jQuery(function($) {
	$('#tlkdate').datetimepicker({
	timeFormat: 'hh:mm',
	dateFormat: 'mm/dd/yy'
	});
});

function tlk_editkey(id){
	if (jQuery("#editk").length > 0){
		jQuery("#editk").prev().show();
		jQuery("#editk").remove();
	}
	
	var o = jQuery('#key'+id);
	var k = o.find("td:eq(0)");
	var l = o.find("td:eq(1)");
	var d = o.find("td:eq(2)");
	var c = o.find("td:eq(3)");
	var e = o.find("td:eq(4)");
	var vk = k.text();
	var vl = l.text();
	var vd = d.text();
	var vc = c.text();
	var ve = e.html();
	 
	o.hide();
	
	o.after('<tr id="editk"><td class="key"><input type="text" name="tlk_txt_" size="20px" value="'+vk+'"/></td><td><input type="text" name="tlk_link_" size="40px" value="'+vl+'"/></td><td><input type="text" name="tlkdate_" id="tlkdate_" size="18px" value="'+vd+'"/></td><td>'+vc+'<input type="hidden" name="tlk_id_" id="tlk_id_" value="'+id+'"/></td><td><input class="button-primary" type="submit" name="submit" value="Change" /> <a class="button-primary" href="javascript:tlk_cancelkey('+id+')">Cancel</a></td></tr>');	
	jQuery('#tlkdate_').datetimepicker({
	timeFormat: 'hh:mm',
	dateFormat: 'mm/dd/yy'
	});
}

function tlk_cancelkey(id){
	var o = jQuery('#key'+id);
	o.next().remove();
	o.show();
}

function seltag(v){
	if (v != '') {
	jQuery('#thekey').val(v).css("color","#000000");

	}
}