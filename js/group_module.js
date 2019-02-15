function addGroupForm() {	
	jQuery('#select1 option:selected').each( function(e) {
		jQuery('#select2').append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
		jQuery(this).remove();
	});	
}

function removeGroupForm() {	
	jQuery('#select2 option:selected').each( function(e) {		
		jQuery('#select1').append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
		jQuery(this).remove();
	});	
}

function submitGroupForm() {
	var optionValues = [];
	jQuery('#select2 option').each(function() {
		optionValues.push(jQuery(this).val());
	});
	var results = optionValues.join(","); 	
	jQuery('#selResult').val(results);
}