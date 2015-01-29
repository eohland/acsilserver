var grid;

(function () {

    function loadScript(url, callback) {

        var script = document.createElement("script")
        script.type = "text/javascript";

        if (script.readyState) { //IE
            script.onreadystatechange = function () {
                if (script.readyState == "loaded" || script.readyState == "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else { //Others
            script.onload = function () {
                callback();
            };
        }

        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
    }

    loadScript("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js", function () {

        loadScript("https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js", function () {

            //jQuery loaded
            console.log('jquery loaded');
             $(function () { $('#count').width($('#controler').width()); $('#tabs').width($('#game_conteneur').width() - 8); grid = new Grid(14, 14); grid.generate(); grid.display('game_canvas'); $('#tabs').tabs(); $('#tabs').tabs('select', '2'); grid.score_load(); });


        });

    });


})();


function main() {
    pluginDiv = document.getElementById("plugin");
    content = "<style>body { margin: 0; background-color: white; } .color-1{ background-color: blue; } .color-2{ background-color: red; } .color-3{ background-color: yellow; } .color-4{ background-color: purple; } .color-5{ background-color: green; } .color-6{ background-color: brown; }  #game_conteneur { width: 550px; margin:0 auto; } #game_canvas div{ /*    cursor: pointer; */    width: 15px; height: 15px; float: left; } #game_canvas, #controler, #count { float:left; border: solid #aaaaaa 1px; box-shadow: 2px 2px 10px #aaaaaa; margin: 10px; padding: 5px; } #FB { float:left; width: 100px; margin-left: 40px; } #count { font-size: 50px; height: 60px; text-align: center; } #controler button { width: 30px; height: 30px; cursor: pointer; margin: 2px; } #retry { display:none; float: left; border: solid #dddddd 1px; border-radius: 6px; padding: 5px; padding-bottom: 10px; margin-left: 10px; color: #222; } #retry img { margin-bottom: -10px; } #retry a { cursor: pointer; } button { border: 1px solid #aaaaaa; border-radius: 6px; margin: 1px; box-shadow: 2px 2px 2px #aaaaaa; padding: 5px; } #tabs { width: 500px; margin: 0 auto; } .clear { visibility: hidden; clear: both; margin: 0px; } h1 { font-family: ubuntu, sans-serif; margin: 5px; padding: 10px; /*color: white;*/ color: #2d2d2d; border-radius: 6px; box-shadow: 2px 2px 10px #aaaaaa; text-shadow: 1px 1px 10px white; /*                background-color: orange; background-image: linear-gradient(bottom, #D17213 46%, #F58C23 100%); background-image: -o-linear-gradient(bottom, #D17213 46%, #F58C23 100%); background-image: -moz-linear-gradient(bottom, #D17213 46%, #F58C23 100%); background-image: -webkit-linear-gradient(bottom, #D17213 46%, #F58C23 100%); background-image: -ms-linear-gradient(bottom, #D17213 46%, #F58C23 100%);  background-image: -webkit-gradient( linear, left bottom, left top, color-stop(0.46, #D17213), color-stop(1, #F58C23) );*/ } .gradient-gray { /* Anciens navigateurs */ background: #aaaaaa top; -o-background-size: 100% 100%; -moz-background-size: 100% 100%; -webkit-background-size: 100% 100%; background-size: 100% 100%; /* Navigateurs récents */ background: -webkit-gradient( linear, left top, left bottom, from(#ffffff), to(#aaaaaa) ); background: -webkit-linear-gradient( top, #ffffff, #aaaaaa ); background: -moz-linear-gradient( top, #ffffff, #aaaaaa ); background: -o-linear-gradient( top, #ffffff, #aaaaaa ); background: linear-gradient( top, #ffffff, #aaaaaa ); }</style>";
    pluginDiv.innerHTML = content;
};

main()

function random(min, max) {
    return (Math.floor((Math.random() * (max - (min - 1))) + min ));
}

function Grid(x, y) {
    this.grid = '';
    this.x = x;
    this.y = y;
    this.bloc_width = 24;
    this.bloc_height = 24;
    this.game_width = this.bloc_width * this.x;
    this.game_height = this.bloc_height * this.y;
    this.count = 0;
    this.actual_color = 0;
    this.div = "game_canvas";
    this.clickable_color = [];
    this.bot_value =   {len : 999,
                        history: ''};
    
    this.generate = function() {
                        var iy = 0;
                        var ix;
                        this.grid = '';
                        this.count = 0;
                        //console.log("start generating iy= " + iy + " y= " + this.y);
                        while (iy < this.y) {
                            //console.log("new line");
                            ix = 0;
                            while (ix < this.x) {
                                //console.log("new case");
                                color = random(1, 6);
                                this.grid += "<div id='" + iy + "-" + ix + "'class='color-" + color + "'></div>"; //onclick='grid.color(" + color + ")'
                                ix++;
                            }
                            iy++;
                        }
                    }
                    
    this.display_map = function (map) {
        this.grid = '';
        for (var iy = 0; iy < map.length; iy++) {
            for (var ix = 0; ix <map[iy].length; ix++) {
                this.grid += "<div id='" + iy + "-" + ix + "'class='color-" + map[iy][ix].color + "'></div>";   
            }
        }
        this.display();
                    
    }
    this.display =  function(div) {
                        $('#retry').hide('fast');
                        if (div){this.div = div};
                        div = this.div;
                        $('#' + div).html(this.grid);
                        $('#' + div).css({
                            'height':   (this.bloc_height * this.y) + 'px',
                            'width' :   (this.bloc_width * this.x) + 'px'
                        });
                        $('#' + div + ' div').css({
                            'height':   this.bloc_height + "px",
                            'width' :   this.bloc_width + "px"
                        });
                        this.actual_color = parseInt($("#0-0").attr('class').charAt(6));
                        $('#count').css('color', 'black');
                        $('#count').text('0/25');

                    }
                    
    this.color =    function(color) {
                        if (color != this.actual_color) {
                            this.count++;
                            $('#count').text(this.count + "/25");
                            this.color_recurse(color, 0, 0);
                            this.actual_color = color;
                            if ($('#' + this.div + ' .color-' + this.actual_color).length == this.x * this.y) {
                                this.win();
                            } 
                            if (this.count == 25) {
                                $('#count').css('color', '#777777');
                                $('#retry').show('fast');
                            }
                        }
                    }
                    
    this.color_recurse = function(color, y, x) {
                            if ($('#' + y + '-' + x).attr('class') == "color-" + this.actual_color) {
                                $('#' + y + '-' + x).attr('class', 'color-' + color);
                                
                                if (y > 0){
                                    this.color_recurse(color, y - 1, x);
                                }
                                if (x > 0) {
                                    this.color_recurse(color, y, x - 1);
                                }
                                if (y < this.y - 1) {
                                    this.color_recurse(color, y + 1, x);
                                }
                                if (x < this.x - 1) {
                                    this.color_recurse(color, y, x + 1);
                                }
                            }
                        }
                        
    this.win =      function() {
                        this.generate();
                        this.display();
                        }
                        
    this.lose = function()  {
                    this.count = 0;
                    this.generate();
                    this.display();
                    $('#score').val("0/25");

                }
    
    this.score_load = function () { }
                    
    this.map_generate = function() { //generate this.map
        var x = 0;
        var y = 0;
        map = [];
        
        while (y < this.y) {
            x = 0;
            map[y] = [];
            
            while (x < this.x) {
                map[y][x] = { color : $('#' + y + "-" + x).attr('class')[6], checked : 0};
                x++;
            }
            y++;
        }
        return (map);
    }
    
    this.search_color_clickable = function(map, color, y, x) {
        var first = 0;
        if (!color) {
            color = map[0][0].color;
            x = 0;
            y = 0;
            this.clickable_color = [];
            first = 1;
        }
        
        if (map[y][x].checked == 1)
            return;
        map[y][x].checked = 1;
        if (color != map[y][x].color) {
            if (jQuery.inArray(map[y][x].color, this.clickable_color) == -1) {
                this.clickable_color.push(map[y][x].color);
            }
            return;
        }
        
        if (y > 0) {
            this.search_color_clickable(map, color, y - 1, x);
        }
        if (x > 0) {
            this.search_color_clickable(map, color, y, x - 1);
        }
        if (y < this.y - 1) {
            this.search_color_clickable(map, color, y + 1, x);
        }
        if (x < this.x - 1) {
            this.search_color_clickable(map, color, y, x + 1);
        }
        if (first) {
            var out = this.clickable_color.join(',').split(',');
            return ((!(out.length == 1 && out[0] == "")) ? out : []);
        }
    }
    
    this.bot_color = function (map, actualColor, color, y, x) {
        if (map[y][x].color == actualColor) {
            map[y][x].color = color;
            if (y > 0){
                this.bot_color(map, actualColor, color, y - 1, x);
            }
            if (x > 0) {
                this.bot_color(map, actualColor, color, y, x - 1);
            }
            if (y < this.y - 1) {
                this.bot_color(map, actualColor, color, y + 1, x);
            }
            if (x < this.x - 1) {
                this.bot_color(map, actualColor, color, y, x + 1);
            }
        }
    }
    
    this.bot_recurse = function(map, color, history, len) {
        var nouvelleMap;
        this.bot_color(map, map[0][0].color, color, 0, 0);

        history = history + color;
        len = len + 1;
        //console.log(len +" " + history);
        if (len == 26) {
            return;
        }
        var clickable_color = this.search_color_clickable(map);
        if (clickable_color.length == 0) {
            console.log(" -- " + len + " " + history);
            this.bot_value.history = history;
            this.bot_value.len = len;
        }
        if (len == this.bot_value.len - 1 ) {
            return;
        }
        for (var i = 0; i < clickable_color.length; i++) {
            nouvelleMap = this.map_duplicate(map);
            this.bot_recurse(nouvelleMap, clickable_color[i], history, len);
            delete nouvelleMap;
        }
    }
    
    this.map_duplicate = function (map) {
        var nouvelleMap = [];
        for (var i = 0; i < map.length; i++) {
            nouvelleMap[i] = [];
            for (var i1=0; i1 < map[i].length; i1++) {
                nouvelleMap[i][i1] = { color : map[i][i1].color, checked : 0};
            }
        }
        return (nouvelleMap)
    }
    
    this.bot = function() {
        this.bot_value = {  history: '',
                            len: 0}
        var map = this.map_generate();
        var clickable_color = this.search_color_clickable(map);
        for (var i = 0; i < clickable_color.length; i++) {
            var map_test = this.map_duplicate(map);
            this.bot_recurse(map_test, clickable_color[i], '', 0)
            delete map_test;
        }
    }
    
}