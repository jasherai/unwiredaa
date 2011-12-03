var callbackUrl, uploadUrl;

function reloadFiles() {
	$.ajax({
		url: callbackUrl,
		/*dataType: 'jsonp',*/
		success: function(data) {
			var ulist = $('<ul></ul>');
			$.each(data, function (idx, file){
				$(ulist).append('<li><input type="radio" name="selected_file" value="'+ file.path + '" /> ' + file.name + '</li>');
			});
			
			$('#fileBrowser').find('.fileList').html(ulist);
		}
	});
}

function createFileBrowser(cbkUrl, uplUrl)
{
	callbackUrl = cbkUrl;
	uploadUrl = uplUrl;
	
	$('body').append('<div id="fileBrowser"><div class="fileList"></div><div class="fileUpload"><form method="post" action="'
					 +uploadUrl+'" enctype="multipart/form-data"></form></div></div>');
	
	var fileBrowser = $('#fileBrowser').hide();
	
	$(fileBrowser).find('form').append('<label>File :</label><input type="file" name="file_upload" /> <span class="button small blue"><input type="submit" value="Upload" />');
	
	$(fileBrowser).find('form input[type=submit]').click(function(){
		$(fileBrowser).append('<iframe id="uploader_frame" name="uploader_frame" style="width: 0px; height: 0px; display: none"></iframe>');
		
		$(this).parents('form:first').attr('target', 'uploader_frame');
		
		$('#uploader_frame').load(function(){
			//alert($(document, frames['uploader_frame']).text());
			reloadFiles();
		});
	});
	
	$(fileBrowser).dialog({
		modal: true,
		title: 'File browser',
		draggable: false,
		autoOpen: false,
		create: function() {
			reloadFiles();
		},
		buttons: {
			'Cancel': function() {
				$(this).dialog('close');
			},
		}
	});
}

function openFileBrowser(elem, prefix)
{
	var buttons = $('#fileBrowser').dialog('option', 'buttons');
	
	if (!prefix) {
		prefix = '';
	}
	
	buttons['Select'] = function() {
		$(elem).val(prefix + $('#fileBrowser').find('input[type=radio]:checked').val());
		$(elem).change();
		$(this).dialog('close');
	}
	
	$('#fileBrowser').dialog('option', 'buttons', buttons)
					 .dialog('open');
}