package webservice 
{
	import com.greensock.events.LoaderEvent;
	import com.greensock.loading.LoaderMax;
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.events.EventDispatcher;
	import flash.external.ExternalInterface;
	//import wopi.GameManager;
	/**
	 * ...
	 * @author marco
	 */
	public class ServiceConfig extends EventDispatcher
	{
		private var _token:String="";
		private var _seed:String = "wopidom";
//		private var _baseUrl:String = "http://localhost/";
//		private var _baseUrl:String = "http://wopidom.com/fordummies/server/web/";
//		private var _baseUrl:String = "http://frozen-garden-4287.herokuapp.com/server/web/";
//		private var _baseUrl:String = "https://games.recette.gamespassport.com/games/fordummies/www/ws/";
		private var _baseUrl:String = "https://games.gamespassport.com/games/fordummies/www/ws/";
		
		private var _fbToken:String =
		"AAADdx9ww1oEBAFwjZA1csG4wC28xuBJaq9moExV9Q8E5UjhLY332toZCZAxj3pOUjn4mqXGCHBSsve31oGGwDxFtVUZACxFXZAf6EoktNuEbMA6ZCav7qa";
		/**
		 * DB connection details:
			 * host: us-cdbr-east-02.cleardb.com
			 * user: b590635813a535
			 * db: heroku_7c00e617d8000bf
			 * pass: fadf6557		
		*/
		public var popupWait:MovieClip;
		
		public function ServiceConfig() { }
		
		
		public function errorHandler(event:LoaderEvent):void 
		{
			trace("Error in " + event.target + ": " + event.text);
			trace("\nContent: " + event.target.content);
			trace("\nhttpStatus: " + event.target.httpStatus);
			trace(LoaderMax.getContent(event.target.name));
			//TODO: use a callback tu update somethig to the user.
		}
		
		public function startWait():void 
		{
			if (popupWait==null) 
			{
				popupWait = new PopupWait();
				popupWait.alpha = 0;
				GameManager.stage.addChild(popupWait);				
				TweenMax.to(popupWait, 0.3, { alpha:1 } );
			}
		}
		
		public function stopWait():void 
		{
			if (popupWait!=null) 
			{
				TweenMax.to(popupWait, 0.3, { alpha:0, onComplete:removePopupWait } );				
			}
		}
		
		private function removePopupWait():void 
		{
			GameManager.stage.removeChild(popupWait);
			popupWait = null;			
		}
		
		public function encrypt(string:String):String
		{
			//return string;
			return _encrypt(string, _seed);
			
			//if (string == null || string == "" || string === ""  ) return string;
			//if (string && string != null && string != "" && string.length > 0  ) 
			//	return _encrypt(string, _seed);
			//else
			//	return string;
		}
		
		private function _encrypt(string:String, key:String)
		{
			var returnString:String = "";
			//var charsArray:Array = String("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo_6.tvwJQ-R0OUrSak954fd2FYyuH~1lIBZ").split("");
			var charsArray:Array = String("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo").split("");
			var charsLength:uint = charsArray.length;
			var stringArray:Array = string.split("");
			var keyArray:Array = String(MD5.encrypt(key)).split("");
			var randomKeyArray:Array = new Array();
			while(randomKeyArray.length < charsLength){
				randomKeyArray.push(charsArray[Math.floor(Math.random()*charsLength)]);
			}
			var a:uint;
			var numeric:uint;
			for (a = 0; a < stringArray.length; a++){
				numeric = String(stringArray[a]).charCodeAt(0) + String(randomKeyArray[a%charsLength]).charCodeAt(0);
				returnString += charsArray[Math.floor(numeric/charsLength)];
				returnString += charsArray[numeric%charsLength];
			}
			var randomKeyEnc:String = "";
			for (a = 0; a < charsLength; a++){
				numeric = String(randomKeyArray[a]).charCodeAt(0) + String(keyArray[a%keyArray.length]).charCodeAt(0);
				randomKeyEnc += charsArray[Math.floor(numeric/charsLength)];
				randomKeyEnc += charsArray[numeric%charsLength];
			}
			return String(randomKeyEnc)+String(MD5.encrypt(string))+String(returnString);
	    }
		
		public function decrypt(string:String):String
		{
			//return string;
			return _decrypt(string, _seed);
		}		
		private function _decrypt(string:String, key:String)
		{
			var returnString:String = "";
			//var charsArray:Array = String("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo_6.tvwJQ-R0OUrSak954fd2FYyuH~1lIBZ").split("");
			var charsArray:Array = String("e7NjchMCEGgTpsx3mKXbVPiAqn8DLzWo").split("");
			var charsLength:uint = charsArray.length;
			var keyArray:Array = String(MD5.encrypt(key)).split("");
			var stringArray:Array = String(string.substr((charsLength*2)+32)).split("");
			var randomKeyArray:Array = String(string.substr(0,charsLength*2)).split("");
			var randomKeyDec:Array = new Array();
			var md5crc:String = string.substr(charsLength*2, 32);
			var a:uint;
			var numeric:uint;
			for (a = 0; a < charsLength * 2; a += 2) {
				numeric = charsArray.indexOf(randomKeyArray[a])*charsLength;
				numeric += charsArray.indexOf(randomKeyArray[a+1]);
				numeric -= String(keyArray[Math.floor(a/2)%keyArray.length]).charCodeAt(0);
				randomKeyDec.push(String.fromCharCode(numeric));
			}
			for (a = 0; a < stringArray.length; a+=2){
				numeric = charsArray.indexOf(stringArray[a])*charsLength;
				numeric += charsArray.indexOf(stringArray[a+1]);
				numeric -= String(randomKeyDec[Math.floor(a/2)%charsLength]).charCodeAt(0);
				returnString += String.fromCharCode(numeric);
			}
			if (md5crc != MD5.encrypt(returnString)) {
				return false;
			}else{
				return returnString;
			}
		}	
		
		public function get baseUrl():String 
		{
			return _baseUrl;
		}
		
		public function get fbToken():String 
		{
			return _fbToken;
		}
		
		public function get seed():String 
		{
			return _seed;
		}
		
		public function get token():String 
		{
			if (_token==null || _token.length < 20) {
				trace("NO TOKEN. PLEASE RELOAD THE GAME.");
				return "";
			} else {
				return _token;
			}
		}
		
		public function set token(value:String):void 
		{
			_token = value;
		}
		
	}

}