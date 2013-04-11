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
	 * File management
	 */
	$('.fileOptions a').click(function() {
		//console.log($(this).attr('fileid'))
		var fileid = $(this).attr('fileid');
		var filename = $(this).attr('filename');
		
		// Delete file
		var link = Routing.generate('_deletefile', { id: fileid })
		$('#deleteFile a.confirm').attr('href', link);
	})
})
