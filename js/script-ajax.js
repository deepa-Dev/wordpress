jQuery(document).ready(function(){ 
	jQuery("#ugroup_list").change(function(){
		
		var group		=	jQuery(this).val(); 		
		var gType		=	document.getElementById('gType').value; 
		
		if(group !=''){
			jQuery('#show_users').show();
			jQuery('#loader').show();
			setTimeout( function(){
				jQuery.ajax({									
					url : groupMdlCntAjax.ajaxurl,
					type: "POST",					
					data:'action=ajaxGroupModule_Config&group='+group+'&gType='+gType,
					success:function(data){ 
						jQuery('#loader').hide();
						jQuery('#show_users').html(data);								
						jQuery('#show_users').show();
						jQuery('#loader').hide();					
					}
				});
			},400);
		} 
	});
					
});