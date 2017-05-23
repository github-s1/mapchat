var socket;

function init() {
	var host = "ws://192.168.1.123:9000/testwebsock.php"; // SET THIS TO YOUR SERVER
	try {
		socket = new WebSocket(host);
		//log('WebSocket - status '+socket.readyState);
		socket.onopen    = function(msg) {
							 //  log("Welcome - status "+this.readyState);
						   };
		socket.onmessage = function(e) {
							   log(e.data);
						   };
		socket.onclose   = function(msg) {
							  // log("Disconnected - status "+this.readyState);
						   };
	}
	catch(ex){
		log(ex);
	}
//	$("msg").focus();
}

function send(){
	var txt;
	txt = "go";

	if(!txt) {
		alert("Message can not be empty");
		return;
	}

//	txt.focus();
	try {
		socket.send(txt);
		//log('Sent: '+txt);
	} catch(ex) {
		log(ex);
	}
}
setInterval(send, 3000);

function quit(){
	if (socket != null) {
		log("Goodbye!");
		socket.close();
		socket=null;
	}
}

function reconnect() {
	quit();
	init();
}

// Utilities

function log(msg){
	updateTables(msg);

}
function onkey(event){ if(event.keyCode==13){ send(); } }
window.onload = init;

