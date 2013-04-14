$(document).ready(function() {
	
	// Focus on one tile at a time
	$('.linkedit').click(function(e) {
		var parent = $(this).parent();
		var span = parent.find('span');
		
		// Getting tile values
		var userId = parent.find('span.userId').text();
		var userPicture = parent.find('span.userPicture').text();
		var firstname = parent.find('span.firstname').text();
		var lastname = parent.find('span.lastname').text();
		var email = parent.find('span.email').text();
		var role = parent.find('span.role').text();
		console.log(userId, userPicture, firstname, lastname, email, role)
		
		// Setting values to the form
		$('#userSettings form span.name').text(firstname + ' ' + lastname);
		$('#userSettings form input.email').val(email);
		$('#userSettings form select.role').val(role);
		console.log(role);
	});
	
	/**
	 * Account management
	 */
	
	//Delete account
	$('.userOptions a.deleteaccount').click(function() {
		var accountid = $(this).attr('accountid');
		
		var link = Routing.generate('_deleteaccount', { id: accountid })
		$('#deleteAccount a.confirm').attr('href', link);
	})
	
	/**
	 * File management
	 */
	
	// Delete file
	$('.fileOptions a.deletefile').click(function() {
		//console.log($(this).attr('fileid'))
		var fileid = $(this).attr('fileid');
		var filename = $(this).attr('filename');
		
		var link = Routing.generate('_deletefile', { id: fileid })
		$('#deleteFile a.confirm').attr('href', link);
	})
	
	// Share file
	$('.fileOptions a.sharefile').click(function() {
		$('#sharedWith').text('');
		//console.log($(this).attr('fileid'))
		var fileid = $(this).attr('fileid');
		var filename = $(this).attr('filename');
		
		var link = Routing.generate('_deletefile', { id: fileid });
		$('#deleteFile a.confirm').attr('href', link);
		
		var shareFormPath = Routing.generate('_sharefile', { id: fileid });
		$('#shareFileForm').attr('action', shareFormPath);
		
		var sharedWith = $.parseJSON($(this).parent().find('.sharedWith').text());
		var lSharedWith = sharedWith.length;
		for (var i = 0; i < lSharedWith; i++) {
			var switchRights = sharedWith[i].rights == 'VIEW' ? 'EDIT' : 'VIEW';
//			var deleteRights = 'DELETE';'
			var form = 
				"<form action="+ shareFormPath +" method='post' {{ form_enctype(shareForm) }}>"
				+"<div class='row-fluid'>"
				+	"<div class='span7'>"
				+		sharedWith[i].email	+ " can " + sharedWith[i].rights + " file"
				+	"</div><!-- span8 -->"
				+	"<div class='span2'>"
				+		"<a href=" + Routing.generate('_updateuserrights', { fileId: fileid, userId: sharedWith[i].id, newRights: switchRights }) + "> allow " + switchRights + "</a>"
				+	"</div><!-- span2 -->"
				+	"<div class='span2'>"
				+		"<a href=" + Routing.generate('_updateuserrights', { fileId: fileid, userId: sharedWith[i].id, newRights: 'DELETE' }) + "> or DELETE </a>"
				+	"</div><!-- span2 -->"
				+"</div><!-- row fluid -->"
				+ "</form>";
			$('#sharedWith').append(form);
		}
	})
	
	//console.log($.parseJSON( $('.usersList').text() ));
	
	$('.typeaheadUserMail').typeahead({
		source: $.parseJSON( $('.usersList').text() ),
		limit: 10
	})

})
