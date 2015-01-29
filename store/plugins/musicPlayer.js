//get div
var content = "";
var pluginDiv = "";

function main() {
    pluginDiv = document.getElementById("plugin");
    content = "<style>#plugin{border: 2px solid black;border-radius:20px;background-color:white;height: 500px;}</style>";
    content += "<div class='row-fluid' style='height:100%;width:100%;padding-left: 50px;'>\
                <div class='col-md-8' id='list' style='height:100%;border-right: 2px solid black;'>\
                </div><div class='col-md-3' id='view' style='height:100%;border-left: 2px solid black;padding-left: 20px; '>\
                <audio controls autoplay id='player' style='padding-top: 20px;'><source src='' type='audio/ogg' id='ogg'>\
                <source src='' type='audio/mpeg' id='mp3'><source src='' type='audio/wav' id='wav'></audio>\
                <img  src='data:image/png;base64,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' />\
                <div id='playing' style='width:100%; height:100%; word-wrap:break; text-align:center;'></div></div></div>";
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
            if (data.files[i].info.mime_type == "mp3"
                || data.files[i].info.mime_type == "mpeg"
                || data.files[i].info.mime_type == "mpga"
                || data.files[i].info.mime_type == "ogg"
                || data.files[i].info.mime_type == "wav") {
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
    if (mime == "mp3" || mime == "mpeg" || mime== "mpga") {
        document.getElementById("mp3").src = url;
    }
    else if (mime == "ogg") {
        document.getElementById("ogg").src = url;
    }
    else if (mime == "wav") {
        document.getElementById("wav").src = url;
    }
    else
        return;
    document.getElementById("playing").innerHTML = name;
    document.getElementById("player").load();
};