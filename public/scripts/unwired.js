/**
 * Override alert()
 */
(function() {
  var proxied = window.alert;
  window.alert = function() {
	$('body').append('<div id="alertoverride">' + arguments[0] + '</div>');
	$('#alertoverride').dialog({
		modal: true,
		draggable: false,
		resizable: false,
		title: 'Alert',
		buttons: { "Ok": function() { $(this).dialog("close"); } },
		close: function(event, ui) { 
			$('#alertoverride').dialog("destroy");
			$('#alertoverride').remove();
		}
	});
  };
})();

/**
 * Initialize common events
 */
$(document).ready(function(){
	/**
	 * Hide empty elements from navigation
	 */
	$('ul.navigation li a[href="javascript:;"]').each(function(){
		if (!$(this).siblings().length) {
			$(this).parent().remove();
		}
	});
	
	/**
	 * Hook delete buttons
	 */
	$('.tools a.delete').live('click', function(){
		return confirm('Are you sure you want to delete this entry?');
	});
	
	/**
	 * Check if node is selectable
	 */
	$('.grouptree').bind("before.jstree", function(event, data) {
    	if (data.func != 'select_node') {
    		return;
    	}

    	/**
    	 * If selecting children is disabled check and show alert
    	 */
    	if ($(this).jstree("get_settings").ui.disable_selecting_children == true
    		&& typeof data.args[0] == 'object' && !$(data.args[0]).hasClass('jstree-clicked')) {
    		
    		var found = false;
    		
    		$(data.args[0]).parents('.jstree-open').each(function () {
    			if ($(this).children('a.jstree-clicked').length > 0) {
    				alert('There is a parent group selected! To be able to select child group you need to deselect the parent first.');
    				found = true;
    				return false;
    			}
    		});
		
    		if (found) {
    			return;
    		}
    		
			if ($(data.args[0]).parent().find('a.jstree-clicked').length > 0) {
				alert('There are child groups selected! To be able to select parent group you need to deselect its children first.');
			}
    		
			return;
    	}
    	
    	if (typeof adminGroups == 'undefined') {
    		return;
    	}
    	
    	/**
    	 * Check if current admin user has group privileges
    	 */
		var groupId;
		if (typeof data.args[0] == 'string') {
			groupId = data.args[0].replace(/[^\d]+/gi, '');
		} else {
			groupId = $(data.args[0]).parent().attr('id').replace(/[^\d]+/gi, '');
		}
		
    	event.result = false;
		$.each(adminGroups, function(idx, role){
		  	if (idx == groupId || $(data.args[0]).parents('#group_' + idx).length > 0) {
		  		event.result = undefined;
		  		return false;
		  	}
		});
		
	  	if (event.result == false) {
	  		alert('You don\'t have permissions to assign to that group!');
	  		event.stopImmediatePropagation();
	  		return false;
	  	}
    });
	
	$('table.listing thead tr th span.button button:last').click(function(){
		var url = $(this).parents('form:first').attr('action');
		
		var action = $(this).attr('name');

		var regexp = new RegExp(action.replace('/', '\/'), 'gi');
		if (action.length && !regexp.test(url)) {
			url = url + '/' + action;
		}
		
		$(this).parents('tr:first').find('th input').each(function(){
			var attr_name = $(this).attr('name');
			var value = $(this).val();
			
			if (!value.length) {
				return true;
			}
			url = url + '/' + attr_name + '/' + value;
		})
		
		window.location.href=url;
	});
});