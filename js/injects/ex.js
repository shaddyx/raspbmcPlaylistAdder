var getById=function(id){
	return document.getElementById(id);
};
var createElement = function(target, type, params){
	var el = document.createElement(type);
	for (var k in params.attrs){
		el.setAttribute(k, params.attrs[k]);
	}
	for (var k in params.style){
		el.style[k] = params.style[k];
	}
	target.appendChild(el);
};
var findLinks=function(){
	var links =[];
	var counter = 0;
	$("td[class=small]").prev().prev().find("a[rel=nofollow]")
	.filter(function(){
		var href = $(this).attr("href")
		var chunks = href.split("url=")
		$(this).attr("realUrl", chunks[1]);
		return href.indexOf('/get/') != -1
	}).each(function(){

		this.playList = links;
		this.fileUrl = $(this).attr("realUrl");
		this.counter = counter;
		links.push(this.fileUrl);
		this.setAttribute("href","javascript:console.log('ok')");
		console.log(this);
		$(this).click(function(){
			Player.playRemoteFile(this.playList, this.counter);
			return false;
		});
		counter ++;
	});
	
	//for (var k in )
	
};
console.log("ex.js injection complete");
$(function(){	
	var form = getById("search_form");
	if (form){
		createElement(form, "input",{
			attrs:{
				type:"hidden",
				name:"url",
				value:"http://ex.ua/search"
			}
		});
		createElement(form, "input",{
			attrs:{
				type:"hidden",
				name:"site",
				value:"Ex"
			}
		});
		console.log("Forms injection complete");
	}

	findLinks();
});
