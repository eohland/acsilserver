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
		document.write('Js');
		var link = Routing.generate('_deleteaccount', { id: accountid })
		$('#deleteAccount a.confirm').attr('href', link);
	})
	
	/**
	 * File management
	 */

	 // Rename file
	$('.fileOptions a.renamefile').click(function() {
		//console.log($(this).attr('fileid'))
		var fileid = $(this).attr('fileid');
		var filename = $(this).attr('filename');

		var renameFormPath = Routing.generate('_renamefile', { id: fileid });
		console.log(renameFormPath);
		$('#renameFileForm').attr('action', renameFormPath);
	})
	
	// Rename folder
	$('.fileOptions a.renamefolder').click(function() {
		var fileid = $(this).attr('fileid');
		var filename = $(this).attr('filename');
		
		var renameFormPath = Routing.generate('_renamefolder', { id: fileid });
		console.log(renameFormPath);
		$('#renameFileForm').attr('action', renameFormPath);
	})
	
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
		console.log(shareFormPath);
		$('#shareFileForm').attr('action', shareFormPath);
		
		var sharedWith = $.parseJSON($(this).parent().find('.sharedWith').text());
		var lSharedWith = sharedWith.length;
		if (lSharedWith == 0) {
			var list = 
				"<div class='row-fluid'>"
				+ "No user yet"
				+"</div><!-- row fluid -->";
			$('#sharedWith').append(list);
		} else {
			for (var i = 0; i < lSharedWith; i++) {
				var switchRights = sharedWith[i].rights == 'VIEW' ? 'EDIT' : 'VIEW';
				var list = 
					"<div class='row-fluid'>"
					+	"<div class='span7'>" + sharedWith[i].email	+ " can " + sharedWith[i].rights + " file </div><!-- span8 -->"
					+	"<div class='span2'>"
					+		"<a href=" + Routing.generate('_updateuserrights', { fileId: fileid, userId: sharedWith[i].id, newRights: switchRights }) + "> allow " + switchRights + "</a>"
					+	"</div><!-- span2 -->"
					+	"<div class='span2'>"
					+		"<a href=" + Routing.generate('_updateuserrights', { fileId: fileid, userId: sharedWith[i].id, newRights: 'DELETE' }) + "> or DELETE </a>"
					+	"</div><!-- span2 -->"
					+"</div><!-- row fluid -->";
				$('#sharedWith').append(list);
			}
		}
	})
	
	$('.typeaheadUserMail').typeahead({
		source: $.parseJSON( $('.usersList').text() ),
		limit: 10
	})

})
	
$('a.changePwd').click(function(){
    var accountid = $(this).attr('accountid');

	var link = Routing.generate('_changepwd', { id: accountid });
	$('#changePwdForm').attr('action', link);
});

document.getElementById('upload').onchange = uploadOnChange;

function uploadOnChange() {
    var filename = this.value;
 filename = filename.substr(0, filename.lastIndexOf("."));
 
    var lastIndex = filename.lastIndexOf("\\");
    if (lastIndex >= 0) {
        filename = filename.substring(lastIndex + 1);
    }
    document.getElementById('filename').value = filename;
}
