	var current_group = null;
	var current_content = null;
	var current_person = null;


	function confirmBulkDelete() { 
		return confirm('Are you sure you want to delete the checked items?');
	}
	function confirmBulkNotSpam() { 
		return confirm('Are you sure you want mark these items as not spam?');
	}



	function changeAuthor() {
	
		$('#author_edit').show();
		$('#changeAuthorLink').hide();

		$('#userId_autofill').autocomplete(PODROOT+'/admin/userAutocomplete.php',{
		}).result(function(event,data,formatted) {
			$('#userId').val(data[1]);
		});
		return false;
	
	}



	function selectAll(obj) {
	
		$('.enabler').attr('checked',$(obj).attr('checked'));
	}
	function addChildSearch() { 
	
		q = $('#addChild_q').val();
		command = PODROOT+'/admin/content/search.php?mode=addChild&q=' + q; 
		$('#addChildResults').html('Loading...');
		$('#addChildResults').show();
		$('#addChildResults').load(command);	
		return false;

	}			
	


	function addChildDoc(child) { 
	
		if (current_group) {
			command = PODROOT+'/admin/groups/addToGroup.php?group=' + current_group + '&doc=' + child;
		} else if (current_content) { 
			command = PODROOT+'/admin/content/addChild.php?parent=' + current_content + '&child=' + child;		
		}
		$('#child_documents').load(command);
		$('#addChildResults').hide();
		return false;
	}

	function removeChildDoc(child) { 
		if (current_group) {
			command = PODROOT+'/admin/groups/addToGroup.php?action=remove&group=' + current_group + '&doc=' + child;
		} else if (current_content) {
			command = PODROOT+'/admin/content/addChild.php?action=removeChild&parent=' + current_content + '&child=' + child;		
		}
		$('#child_documents').load(command);
		return false;
	}		



	function addMemberSearch() { 
	
		q = $('#addMember_q').val();
		command = PODROOT+'/admin/people/search.php?result_mode=addMember&q=' + q; 
		$('#addMemberResults').html('Loading...');
		$('#addMemberResults').show();
		$('#addMemberResults').load(command);	
		return false;

	}			

	function addMember(child) { 
		type = $('#member_type_' + child).val();
		command = PODROOT+'/admin/groups/addMember.php?group=' + current_group + '&person=' + child + "&type=" + type;
		$('#members').load(command);
		$('#addMemberResults').hide();
		return false;
	}

	function removeMember(child) { 
		command = PODROOT+'/admin/groups/addMember.php?action=remove&group=' + current_group + '&person=' + child;
		$('#members').load(command);	
		return false;
	}		



			function removeFlag(flag,type,itemId,userId) { 
			
				var command = PODROOT + "/admin/flags/?removeFlag=" + flag + "&type=" + type + "&itemId=" + itemId + "&userId=" + userId;
				$.getJSON(command,flagSuccess);
			
			}
					
			function addFlag(flag,type,itemId,userId) { 
			
				var command = PODROOT + "/admin/flags/?addFlag=" + flag + "&type=" + type + "&itemId=" + itemId + "&userId=" + userId;
				$.getJSON(command,flagSuccess);
			
			}			
			
			function flagSuccess(res) { 
					if (res.error) {
						alert(res.error);
					} else {
						id = res.new_action + "_" + res.flag + "_" + res.itemId + "_" + res.userId;
						$('#'+id).show();
						id = res.old_action + "_" + res.flag + "_" + res.itemId + "_" + res.userId;
						$('#'+id).hide();
					}
			}
				
			
		
	function repairField(obj,message) {
		if ($(obj).val()==message) {
			$(obj).css('color','#000000');
			$(obj).val('');
		} else {
			$(obj).css('color','#CCCCCC');
			$(obj).val(message);
		}
		return false;
	
	}
	
	function showOptional(obj,op) {
		$(obj).hide();
		$(op).show();
		return false;
	}
	
	
	function addMetaField() { 
	
		name = $('#new_meta_name').val();
		
		valid = (name!='');

		if (valid) { 
			$('#new_meta_name').val('');
			$('#new_meta').hide();
			$('#add_field_link').show();
			
			p = document.createElement('p');
			p.setAttribute('class','input');
			p.innerHTML = '<label for="meta_' + name + '">'+name+':</label><input type="text" name="meta_'+name+'" id="meta_'+name+'" class="text" />';
					
			$('#new_meta_fields').append(p);
		}		
		return false;
	
	}

	function changeType() { 
		$('#content_type').hide();
		$('#type').show();
		return false;
	}

	var CURRENT_EDITOR = null;

	function getScrollHeight() {
	   var h = window.pageYOffset ||
	           document.body.scrollTop ||
	           document.documentElement.scrollTop;
	   return h ? h : 0;
	}


	function fileBrowserList() { 
	
		$('#fileBrowser_details').hide();
		$('#fileBrowser_list').show();
		return false;
	}
	function fileBrowserDetails(docId,fileId) { 
	
		$('#fileBrowser_list').hide();
		$('#fileBrowser_details').html('Loading file...');
		$('#fileBrowser_details').show();
		
		var API_COMMAND = PODROOT + '/admin/files/browser.php?mode=details&docId='+docId+'&fileId='+fileId;
		$('#fileBrowser_details').load(API_COMMAND);
		return false;

	}
	
	function openFileBrowser(ed,docId) { 

		
		if (!docId) { 
			alert('Please save this content before attaching files.');
		} else { 
			CURRENT_EDITOR = ed;
	
			var API_COMMAND = PODROOT+'/admin/files/browser.php?mode=list&docId='+docId;
			if ($('#fileBrowser').length == 0) { 
				$('body').append('<div id="fileBrowser" style="position:absolute;">Loading</div>');
				$('body').append('<a href="#" style="position:absolute;"id="fileBrowser_cancel" onclick="return fileBrowserCancel();"><img src="'+PODROOT+'/themes/admin/img/close_x.png" alt="Cancel" border="0" width="42" height="42" /></a>');
			} else {
				$('#fileBrowser').html('Loading');
				$('#fileBrowser_cancel').show();
				$('#fileBrowser').show();
			}
			
			$('#fileBrowser').css('top',(getScrollHeight()+50)+'px');
			$('#fileBrowser').css('left','180px');

			$('#fileBrowser_cancel').css('top',(getScrollHeight()+30)+'px');
			$('#fileBrowser_cancel').css('left','160px');

			$('#fileBrowser').load(API_COMMAND);
		}
	}
	
	function fileBrowserCustomSize(changed)  {
	
		if ($('#fileBrowser_maintainAspectRatio').is(':checked')) { 
	
			h = $('#fileBrowser_original').attr('xheight');
			w = $('#fileBrowser_original').attr('xwidth');
		
			if (changed=='width') {
				nw = $('#fileBrowser_width').val();
				$('#fileBrowser_height').val(parseInt((h/w) * nw));		
			} else {
				nh = $('#fileBrowser_height').val();
				$('#fileBrowser_width').val(parseInt((w/h) * nh));			
			}
		
			$('#fileBrowser_custom').attr('checked',true);	
		}
	
		generateImageMarkup();
		
	
	
	}
	
	
	function generateImageMarkup()  {
	
		selected = $('input[name=fileBrowser_src]:checked');
		align = $('input[name=fileBrowser_align]:checked');
		caption = $('#fileBrowser_caption').val();
		caption = caption.replace(/\"/g,"&quot;");
		src = selected.val();
		alignment = align.val();
		height = selected.attr('xheight');
		width = selected.attr('xwidth');
		
		if (src=='custom') { 
		
			height = $('#fileBrowser_height').val();
			width = $('#fileBrowser_width').val();
			src = $('#fileBrowser_original').val();
		}
		
		
		markup = '<img src="' + src + '" height="' +height + '" width="' + width + '" title="'+caption+'" alt="'+caption+'"';
		
		if (alignment!='none') { 
			markup = markup + ' style="float:'+alignment+';" ';
		}
		
		markup = markup + ' />';
		
		$('#fileBrowser_markup').val(markup);
	}


	function insertMarkup(markup) {
		if (CURRENT_EDITOR) { 
			CURRENT_EDITOR.focus();
			CURRENT_EDITOR.selection.setContent(markup);
			CURRENT_EDITOR = null;
			fileBrowserCancel();
		}
		return false;
	}
	
	function fileBrowserCancel() {
	
		$('#fileBrowser').hide();
		$('#fileBrowser_cancel').hide();
		return false;
	}
	
	$().ready( function() { 
	
		$('#tags').tagsInput({delimiter:' ',autocomplete_url:PODROOT+'/admin/tagAutocomplete.php'});
		
		
		$('.repairField').blur(function() { 
			
			o = $(this);
			if (o.val()=='') { 
				//console.log('resetting default');
				o.css('color','#CCC');
				o.val(o.attr('default'));
			}
		});

		$('.repairField').focus(function() { 
			
			o = $(this);
			
			//console.log("focus "+o.attr('id'));
			 if (o.val()==o.attr('default')) {
				//console.log('ready for input');
				o.css('color','#000');
				o.val('');
			}
			

		});

		$('.repairField').blur();

	

		$('.meta_lookup').autocomplete(PODROOT+'/admin/metaAutocomplete.php',{
				autoFill: true
		}
		);


		$('textarea.tinymce').each(function() { 
		
			if ($(this).val().toLowerCase().indexOf('<p')==0) { 
				// this is formatted text, use tinymce
				fancyText(this);
			} else {
				// this is just normal text.
				$('<a href="#" class="textarea_control" onclick="return fancyText(\'#'+$(this).attr('id')+'\');">Use HTML Editor</a>').insertBefore(this);
				
			}
		
		});
		

		
		$('form.valid').validate();
	});


	function plainText(obj) {
		$('.textarea_control').remove();
		$('<a href="#" class="textarea_control" onclick="return fancyText(\'#'+$(obj).attr('id')+'\');">Use HTML Editor</a>').insertBefore(obj);
		tinyMCE.execCommand('mceRemoveControl', false,$(obj).attr('id'));
		return false;
	}
	
	function fancyText(obj) { 
		$('.textarea_control').remove();
		$('<a href="#" class="textarea_control" onclick="return plainText(\'#'+$(obj).attr('id')+'\');">Plain Text</a>').insertBefore(obj);

		$(obj).tinymce({
			script_url : PODROOT+'/themes/admin/js/tinymce/jscripts/tiny_mce/tiny_mce.js',
			theme: "advanced",
			
			valid_elements: "p[id|class],pre[id|class],blockquote[id|class],h1[id|class],h2[id|class],h3[id|class],h4[id|class],h5[id|class],h6[id|class],ol[id|class],ul[id|class],li[id|class],dl[id|class],dt[id|class],dd[id|class],table[id|class],tr[id|class],td[id|class],th[id|class],br[id|class],em[id|class],strong[id|class],i[id|class],u[id|class],b[id|class],strike[id|class],a[id|class|name|href|target|title],img[id|class|src|width|height|alt|border|title|align|style],object[id|class|width|height|classid],param[name|value],embed[id|class|src|type|width|height|allowfullscreen|allowscriptaccess|cachebusting|bgcolor|quality|flashvars],div[id|class|href|style]",
			plugins:"paste",
			paste_auto_cleanup_on_paste: true,
			paste_strip_class_attributes: "all",
			paste_remove_spans: true,
			paste_remove_styles: true,
			relative_urls:false,
			remove_script_host: false,
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",	
			theme_advanced_buttons1: "pastetext,formatselect,removeformat,separator,bold,italic,separator,bullist,numlist,separator,link,unlink,separator,undo,removeformat,separator,charmap,separator,files",
			theme_advanced_buttons2: "",
			theme_advanced_blockformats : "p,blockquote,h1,h2,h3,h4,h5,h6",
			content_css : PODROOT+"/themes/admin/tinymce.css",
			setup: function(ed) { 
				ed.addButton('files',
					{
						title: 'Insert file',
						image: PODROOT+'/themes/admin/img/insert_image.gif',
						onclick: function() { 
							openFileBrowser(ed,current_content);
						}
					}
				);
			}
		});
		return false;
	}
/****************************************************************************************/

