

	$(window).ready(function() {
	
		$('#system_messages').slideDown('fast',function() {
			// want to do something after the system message appears?
		});
		
		$('a[href="#hideMessages"]').live('click',function(){

			hideMessages();
			return false; 
			
		});
		
		$('.repairField').blur(function() { 
			
			o = $(this);
			if (o.val()=='') { 
				o.css('color','#CCC');
				o.val(o.attr('data-default'));
			}
		});

		$('.repairField').focus(function() { 
			
			o = $(this);
			 if (o.val()==o.attr('data-default')) {
				o.css('color','#000');
				o.val('');
			}

		});

		$('.repairField').blur();
	
		$('form.valid').validate();


		$('textarea.expanding').live('focus',function() {
			$(this).css('height','100px');		
		});

		$('textarea.expanding').live('blur',function() {
			if ($(this).val()=='') { 
				$(this).css('height','25px');
			}
		});
		
		$('#add_comment .comment_submit').live('click', function() {
			var command = '';
                        var comment = $('#add_comment').children('[name=comment]').val();
			var comments_div = $('#add_comment').attr('data-comments');
                        var el_in= $('#add_comment');

                        $('#add_comment').children('[name=comment]').attr('disabled',true);
			startSpinner();

                        if ($('#add_comment').attr('data-content')) {
                                command = '/content.addComment?comment='+encodeURIComponent(comment)+'&content='+$('#add_comment').attr('data-content');
			} else if ($(this).attr('data-person')) {
				command = '/person.addComment?comment='+encodeURIComponent(comment)+'&person='+$(this).attr('data-person');
			}
                        console.log(command);

			(
				function(el,comments_div) {
                                        //console.log(el);
					$.getJSON(API+command,function(json) {
                                                console.log(json.html);
						stopSpinner();
						if (json.error) {
							$(el).children('[name=comment]').attr('disabled',false);
							complain(json.error);

						} else {
                                                        console.log('success');
							$(el).children('[name=comment]').attr('disabled',false);
							$(el).children('[name=comment]').val('');
							$(comments_div).html(json.html);

							complain('Comment added!','success');
						}

					});
				}
			)($('#add_comment'),comments_div);
			return false;
		});

		$('a[href="#deleteComment"]').live('click',function() { 
		
			if (confirm('Delete this comment forever?')) {		
				var command = '';
				var comment = $(this).attr('data-comment');
				command = '/comment.delete?comment='+encodeURIComponent(comment);
	
				(
					function(el,comment) {				
						$.getJSON(API+command,function(json) {
							if (json.error) {
								complain(json.error);
							} else {
								complain('Commented deleted','success');
								$('#comment'+comment).hide();
							}
						
						});
					}
				)(this,comment);	
			}
			return false;
				
		});
		
		$('a[href="#deleteContent"]').live('click',function() { 
	
			if (confirm('Delete this forever?')) {		
				var command = '';
				var content = $(this).attr('data-content');
				command = '/content.delete?content='+encodeURIComponent(content);
	
				(
					function(el,content) {				
						$.getJSON(API+command,function(json) {
							if (json.error) {
								complain(json.error);
							} else {
								$('#content'+content).hide();
								complain('Content deleted!','success');
							}
						
						});
					}
				)(this,content);	
			}
			return false;
				
		});

		$('a[href="#markAsRead"]').live('click',function() { 
	
			var command = '';
			if ($(this).attr('data-content')) { 
				var content = $(this).attr('data-content');
				command = '/content.markAsRead?content='+encodeURIComponent(content);
			} else if($(this).attr('data-alert')) {
				var alert = $(this).attr('data-alert');
				command = '/alert.markAsRead?alert='+encodeURIComponent(alert);			
			}
			
			(
				function(el,content) {				
					$.getJSON(API+command,function(json) {
						if (json.error) {
							complain(json.error);
						} else {
							if (json.content) { 
								$(el).html('No new comments');
								$('#content'+json.content.id+' .new_comments').slideUp();
							} else {
								$('#alert'+json.alert.id).slideUp();
							}
						}
					
					});
				}
			)(this,content);	
			return false;
				
		});

		$('a[href="#deleteFile"]').live('click',function() { 
	
			if (confirm('Delete this file forever?')) {		
				var command = '';
				var file = $(this).attr('data-file');
				command = '/file.delete?file='+encodeURIComponent(file);
	
				(
					function(el,file) {				
						$.getJSON(API+command,function(json) {
							if (json.error) {
								complain(json.error);
							} else {
								complain('File deleted','success');
								$('#file'+file).hide();
							}
						
						});
					}
				)(this,file);	
			}
			return false;
				
		});
		
		// Look for flag links of the format:
		// <a href="#toggleFlag" data-flag="flagname" data-active="active state text" data-inactive="inactive state text" data-content="id of content to flag" data-person="id of person to flag" data-group="id of group to flag" data-comment="id of comment to flag" data-file="id of file to flag">Link Text</a>	
		$('a[href="#toggleFlag"]').live('click',function() {
			var command = '';
                        var org_width= $(this).width();
                        var org_height= $(this).height();

			var flag = $(this).attr('data-flag');
			var on_state = $(this).attr('data-active');
			var off_state =$(this).attr('data-inactive');
			if ($(this).hasClass('active')) { 
				$(this).removeClass('active');
				$(this).html(off_state);
			} else {
				$(this).addClass('active');
				$(this).html(on_state);
			}

                        $(this).width(org_width);
                        $(this).height(org_height);
                        
			if ($(this).attr('data-content')) {
				command = '/content.toggleFlag?flag='+flag+'&content='+$(this).attr('data-content');
			} else if ($(this).attr('data-person')) {
				command = '/person.toggleFlag?flag='+flag+'&person='+$(this).attr('data-person');
			} else if ($(this).attr('data-comment')) {
				command = '/comment.toggleFlag?flag='+flag+'&comment='+$(this).attr('data-comment');
			} else if ($(this).attr('data-file')) {
				command = '/file.toggleFlag?flag='+flag+'&file='+$(this).attr('data-file');
			} else if ($(this).attr('data-group')) {
				command = '/group.toggleFlag?flag='+flag+'&group='+$(this).attr('data-group');
			}
	
			(
				function(el,on_state,off_state,flag,command) { 
					$.getJSON(API+command,function(json) {
						if (json.error) {
							complain(json.error);
						} else {
							if (json.state=='on') {
								$(el).html(on_state);
								$(el).addClass('active');
							} else {
								$(el).html(off_state);
								$(el).removeClass('active');
							}
						}
					
					});
				}
			)(this,on_state,off_state,flag,command);
			
			return false;
		});
	

		// Look for flag links of the format:
		// <a href="#toggleFlag" data-flag="flagname" data-active="active state text" data-inactive="inactive state text" data-content="id of content to flag" data-person="id of person to flag" data-group="id of group to flag" data-comment="id of comment to flag" data-file="id of file to flag">Link Text</a>	
		$('a[href="#addFlag"]').live('click',function() {
			var command = '';
			var flag = $(this).attr('data-flag');
			var on_state = $(this).attr('data-active');
			var off_state =$(this).attr('data-inactive');
			if (!$(this).hasClass('active')) { 
				$(this).addClass('active');
				$(this).html(on_state);
			}
	
			if ($(this).attr('data-content')) {
				command = '/content.addFlag?flag='+flag+'&content='+$(this).attr('data-content');
			} else if ($(this).attr('data-person')) {
				command = '/person.addFlag?flag='+flag+'&person='+$(this).attr('data-person');
			} else if ($(this).attr('data-comment')) {
				command = '/comment.addFlag?flag='+flag+'&comment='+$(this).attr('data-comment');
			} else if ($(this).attr('data-file')) {
				command = '/file.addFlag?flag='+flag+'&file='+$(this).attr('data-file');
			} else if ($(this).attr('data-group')) {
				command = '/group.addFlag?flag='+flag+'&group='+$(this).attr('data-group');
			}
	
			(
				function(el,on_state,off_state,flag,command) { 
					$.getJSON(API+command,function(json) {
						if (json.error) {
							complain(json.error);
						} else {
							if (json.state=='on') {
								$(el).html(on_state);
								$(el).addClass('active');
							} else {
								$(el).html(off_state);
								$(el).removeClass('active');
							}
						}
					
					});
				}
			)(this,on_state,off_state,flag,command);
			
			return false;
		});


		// Look for flag links of the format:
		// <a href="#toggleFlag" data-flag="flagname" data-active="active state text" data-inactive="inactive state text" data-content="id of content to flag" data-person="id of person to flag" data-group="id of group to flag" data-comment="id of comment to flag" data-file="id of file to flag">Link Text</a>	
		$('a[href="#removeFlag"]').live('click',function() {
			var command = '';
			var flag = $(this).attr('data-flag');
			var on_state = $(this).attr('data-active');
			var off_state =$(this).attr('data-inactive');
			if ($(this).hasClass('active')) { 
				$(this).removeClass('active');
				$(this).html(off_state);
			}
	
			if ($(this).attr('data-content')) {
				command = '/content.removeFlag?flag='+flag+'&content='+$(this).attr('data-content');
			} else if ($(this).attr('data-person')) {
				command = '/person.removeFlag?flag='+flag+'&person='+$(this).attr('data-person');
			} else if ($(this).attr('data-comment')) {
				command = '/comment.removeFlag?flag='+flag+'&comment='+$(this).attr('data-comment');
			} else if ($(this).attr('data-file')) {
				command = '/file.removeFlag?flag='+flag+'&file='+$(this).attr('data-file');
			} else if ($(this).attr('data-group')) {
				command = '/group.removeFlag?flag='+flag+'&group='+$(this).attr('data-group');
			}
	
			(
				function(el,on_state,off_state,flag,command) { 
					$.getJSON(API+command,function(json) {
						if (json.error) {
							complain(json.error);
						} else {
							if (json.state=='on') {
								$(el).html(on_state);
								$(el).addClass('active');
							} else {
								$(el).html(off_state);
								$(el).removeClass('active');
							}
						}
					
					});
				}
			)(this,on_state,off_state,flag,command);
			
			return false;
		});	
		// Initialize flag links based on whether or not they've got the active class set (which should be done in the template)
		$('a[href="#toggleFlag"],a[href="#addFlag"],a[href="#removeFlag"]').each(function() {
			var flag = $(this).attr('data-flag');
			var on_state = $(this).attr('data-active');
			var off_state =$(this).attr('data-inactive');
	
			if ($(this).hasClass('active')) { 
				$(this).html(on_state);
			} else {
				$(this).html(off_state);
			}
		
		});
		
		$('a[href="#reply"]').live('click',function() { 

			var comment = $(this).attr('data-comment');
			var author = $(this).attr('data-author');		
			
			$('form[action="#addComment"] [name="comment"]').val($('form[action="#addComment"] [name="comment"]').val() + '<a href="#' +comment + '">@' + author + '</a> ');
			return false;
		
		});

		$('a[href="#changeMemberType"]').live('click',function(){
			var group = $(this).attr('data-group');
			var person = $(this).attr('data-person');

			var current = $.trim($(this).html());
			var select = '';

			if (current=='invitee') { 
				select = select + '<option value="invitee">Currently: Invitee</option>';
				select = select + '<option value="member">+ Promote to member</option>';
				select = select + '<option value="manager">++ Promote to manager</option>';
				select = select + '<option value="">- Cancel Invitation</option>';
			}
			if (current=='member') { 
				select = select + '<option value="member">Currently: Member</option>';
				select = select + '<option value="manager">+ Promote to manager</option>';
				select = select + '<option value="">- Remove</option>';
			}
			if (current=='manager') { 
				select = select + '<option value="manager">Currently: Manager</option>';
				select = select + '<option value="member">- Demote to member</option>';
				select = select + '<option value="">- Remove</option>';
			}
			if (current=='owner') {
				complain("Owner's membership cannot be changed.",'notice');
				return false;
			}
			
			if ($('#changeMemberhip'+person).html()) { 
				$('#changeMemberhip'+person).html(select);
			} else {
				select = '<select id="changeMemberType'+person+'" class="changeMemberType" data-group="'+group+'" data-person="'+person+'">' + select + "</select>";		
				$(select).insertAfter(this);
			}
			$(this).hide();
			$('#changeMemberType'+person).focus();

			return false;
		});
		
		$('select.changeMemberType').live('change',function() {
			var group = $(this).attr('data-group');
			var person = $(this).attr('data-person');
			
			var updated = $(this).val();

			if (updated=='') {
				if (!confirm('Remove this member from the group?')) {
					return false;
				}
			}
			$(this).hide();
			$('#person'+person+ ' a[href="#changeMemberType"]').html(updated).show();
			
			var command = '/group.changeMemberType?group='+encodeURIComponent(group)+"&person="+encodeURIComponent(person)+"&membership="+encodeURIComponent(updated);
			(
				function(el) {
					
					$.getJSON(API+command,function(json){ 
						if (json.error) {
							$(el).html(json.membership);
							complain(json.error);
						} else {
							if (json.membership=='') { 
								$('#person'+json.person.id).hide();
							} else {
								$(el).html(json.membership);
							}
							complain('Membership updated','success');
						}
					
					});
				
				}
			
			)(this);			
		
		});

		$('a[href="#removeMember"]').live('click',function() {
			if (confirm('Remove this member from the group?')) { 
				var group = $(this).attr('data-group');
				var person = $(this).attr('data-person');
				
				$('#person'+person).hide();
				
				var command = '/group.removeMember?group='+encodeURIComponent(group)+"&person="+encodeURIComponent(person);
				(
					function(el) {
						
						$.getJSON(API+command,function(json){ 
							if (json.error) {
								$('#person'+json.person.id).show();
								complain(json.error);
							} else {
								complain('Member removed from group','success');
							}
						
						});
					
					}
				
				)(this);			
			} 
			return false;
		
		});	
		
		$('a.joinGroup').each(function() { 
			setGroupMembershipLink(this);
		});

		
		$('a.joinGroup').live('click',function() { 
			
			if (!$(this).attr('data-person')) { 
				return true;
			} else {
			
				var group = $(this).attr('data-group');
				var person = $(this).attr('data-person');
				
				if ($(this).hasClass('member')) {
					var command = '/group.removeMember?group='+encodeURIComponent(group)+"&person="+encodeURIComponent(person);
					(
						function(el) {
							
							$.getJSON(API+command,function(json){ 
								if (json.error) {
									complain(json.error);
								} else {
									$(el).html($(el).attr('data-default'));
									$(el).removeClass('member');
									complain('You quit this group!','success');
								}
							
							});
						
						}
					
					)(this);			
			
				} else {
	
					var command = '/group.addMember?group='+encodeURIComponent(group);
					if (person) { 
						command = command + "&person="+encodeURIComponent(person);
					}
					(
						function(el) {
							
							$.getJSON(API+command,function(json){ 
								if (json.error) {
									$('#person'+json.person.id).show();
									complain(json.error);
								} else {
									if (!$(el).hasClass(json.membership)) {
										$(el).html($(el).attr('data-'+json.membership));
										$(el).addClass(json.membership);
										complain('You joined this group!','success');
									}
								}
							
							});
						
						}
					
					)(this);			
				}
				return false;		
			}
		
		}); 
		

		$('input#tags').tagsInput({delimiter:' '});


	});


	function setGroupMembershipLink(el) {
	
		if ($(el).hasClass('member')) { 
			$(el).html($(el).attr('data-member'));
		} else if ($(el).hasClass('owner')) { 
			$(el).html($(el).attr('data-owner'));
		} else if ($(el).hasClass('manager')) { 
			$(el).html($(el).attr('data-manager'));
		} else if ($(el).hasClass('invitee')) { 
			$(el).html($(el).attr('data-invitee'));
		} else if ($(el).hasClass('applicant')) { 
			$(el).html($(el).attr('data-applicant'));
		} else { 
			$(el).html($(el).attr('data-default'));
		}

	}
	
	function togglePostOption(option) {
	
		add_option = $('#add_' + option);
		post_option = $('#post_' + option);
		
		if (add_option.hasClass('active')) { // option is off, need to turn it on
			add_option.removeClass('active');
			post_option.hide();
		} else {
			add_option.addClass('active');
			post_option.show();		
		}
		return false;
	}

/* Display an error or alert */
	function complain(message,type) {
		if (!$('#system_messages').html()) { 
			$('<section id="system_messages" class="grid" style="display:none;"><a href="#hideMessages" class="dismiss">OK</a><ul></ul></section>').insertAfter('header nav');
		}
		if (!type) { type = 'error'; } 
		$('#system_messages').removeClass().addClass(type).addClass('grid');
		if ($('#system_messages').is(':visible')) {
			$('#system_messages ul').prepend('<li class="'+type+'">'+message+'</li>');
		} else {
			$('#system_messages ul').html('<li class="'+type+'">'+message+'</li>');
		}
		$('#system_messages').slideDown('fast',function() {
		});
		if (type=='success') {
			setTimeout('hideMessages();',3000);
		}
		return false;
	}

	function hideMessages() {
		$('#system_messages').css('position','relative');
		$('#system_messages').animate(
			{
				opacity:0,
				top: '+=10',
			},400,function(){ 
				$('#system_messages').hide();
				$('#system_messages').css('top','0px');
				$('#system_messages').css('opacity','1.0');
			
			}
		);

	}

/* Post Comments Management */
		
		function startSpinner() {
		
			$('#spinner').html('<img src="' + themeRoot + '/img/spinner.gif" />');
		}
		function stopSpinner() {
		
			$('#spinner').html('FEED BACK');
		}

/* Formatting */
		
		function pluralize(count,singular,plural) {
		
			if (count == 1) {  return singular; } else { return plural; } 
		}
	