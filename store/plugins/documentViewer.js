//get div
var content = "";
var pluginDiv = "";

function main() {
    pluginDiv = document.getElementById("plugin");
    content = "<style>#plugin{border: 2px solid black;border-radius:20px;background-color:white;height: 500px;}</style>";
    content += "<div class='row-fluid' style='height:100%;width:100%;padding-left: 50px;'>\
                <div class='col-md-3' id='list' style='height:100%;border-right: 2px solid black;'></div>\
                <div class='col-md-8' id='view' style='height:100%;border-left: 2px solid black;padding-left: 20px;'>\
                <iframe id='iframe' src='' width='100%' height='100%' style='border: none;margin : 0 auto; display : block;'></iframe>\
                </div></div>";
    pluginDiv.innerHTML = content;
    xdr(APIUrl + "list/0", "POST", null, displayData, displayData);
};

main()

function xdr(url, method, data, callback, errback) {
    var req;

    if (XMLHttpRequest) {
        req = new XMLHttpRequest();

        req.open(method, url, true);
        req.setRequestHeader("Accept", "application/json, text/plain, */*");
        req.setRequestHeader("Authorization", "Bearer " + token);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.onerror = errback;
        req.onreadystatechange = function () {
            if (req.readyState === 4) {
                if (req.status >= 200 && req.status < 400) {
                    callback(req.responseText);
                } else {
                    errback(new Error('Response returned with non-OK status'));
                }
            }
        };
        req.send(data);
    }
};


function displayData(data) {
    var musicList = document.getElementById("list");
    data = JSON.parse(data)
    if (data.files != null) {
        for (i = 0; i < data.files.length; i++) {
            if (data.files[i].info.mime_type == "pdf"
                || data.files[i].info.mime_type == "txt") {
                musicList.innerHTML += "<div style='cursor:pointer;margin-top: 5px;margin-bottom: 5px;' class='row' id='" +
                    data.files[i].info.id + "' onclick='play(\"" +
                    uploadUrl + data.files[i].info.pseudo_owner + '/' +
                    data.files[i].info.real_path + data.files[i].info.path +
                    "\", \"" + data.files[i].info.mime_type + "\",\"" +
                    data.files[i].info.name.replace("'", "").replace("\'", "").replace('"', "").replace('\"', "") + "\")'>" +
                    data.files[i].info.name + "</div>";
                //console.log(uploadUrl + data.files[i].info.pseudo_owner + '/' + data.files[i].info.real_path + data.files[i].info.path+"|||"+ data.files[i].info.mime_type);
            }
        }
    }
    if (data.folders != null) {
        console.log(data.folders);
        for (i = 0; i < data.folders.length; i++) {
            xdr(APIUrl + "list/" + data.folders[i].id, "POST", null, displayData, displayData);
        }
    }

    //console.log(JSON.parse(data));
};

function parseFileList(array) {

};

function play(url, mime, name) {
    console.log(mime);
    document.getElementById("iframe").src = "https://docs.google.com/viewer?embedded=true&url=" + encodeURIComponent(url);
    //document.getElementById("playing").innerHTML = name;
    //document.getElementById("player").load();
};