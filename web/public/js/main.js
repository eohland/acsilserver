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
		//console.log($(this).attr('fileid'))
		var fileid = $(this).attr('fileid');
		var filename = $(this).attr('filename');
		
		var link = Routing.generate('_deletefile', { id: fileid });
		$('#deleteFile a.confirm').attr('href', link);
		
		// En fait c'est plus simple que ce que je pensais
		var formPath = Routing.generate('_sharefile', { id: fileid });
		$('#shareFileForm').attr('action', formPath);
	})
	
	//console.log($.parseJSON( $('.usersList').text() ));
	
	$('.typeaheadUserMail').typeahead({
		source: $.parseJSON( $('.usersList').text() ),
		limit: 10
	})

})
