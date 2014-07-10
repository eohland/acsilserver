var API = {
    url : "http://localhost:8181/",
    id : null,
    get : function () {
	return true;
    },
    update : function () {
	return true;
    },
    remove : function () {
	return true;
    },
    create : function () {
	return true;
    },
    authenticate : function () {
	return true;
    }
}

function User (tmp) {
    //class user
}


var utils = {
    createCookie : function(name,value,days) {
	if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
    },
    readCookie : function(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
    },
    eraseCookie : function(name) {
	createCookie(name,"",-1);
    },
    genUuid : function() {
	return ('xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
	    var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
	    return v.toString(16);
	}));
    },
    showTime : function(timestamp) {
	timestamp = new Date(parseInt(timestamp)*1000);
	var weekday = new Array(7);
	weekday[0]=  "Sunday";
	weekday[1] = "Monday";
	weekday[2] = "Tuesday";
	weekday[3] = "Wednesday";
	weekday[4] = "Thursday";
	weekday[5] = "Friday";
	weekday[6] = "Saturday";

	var timeString = timestamp.getMonth()+"/"+timestamp.getDate()+"/"+timestamp.getFullYear()+" "+timestamp.getHours()+":"+timestamp.getMinutes();
	return timeString;
    }
}
