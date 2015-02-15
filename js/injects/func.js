var Player = {}; 

Player.call = function(data){
	$.ajax({
		url:"/playerApi",
		method:"POST",
		data:data
	});
}

Player.playRemoteFile = function(playList, number){
	this.call({
		m:"play",
		list:JSON.stringify(playList),
		number:number
	});
};

Player.playPause = function(){
	this.call({
		m:"playPause"
	});
};
Player.next = function(){
	this.call({
		m:"next"
	});
};
Player.prev = function(){
	this.call({
		m:"prev"
	});
};