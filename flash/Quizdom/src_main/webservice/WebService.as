package webservice 
{
	import com.greensock.events.LoaderEvent;
	import com.greensock.loading.DataLoader;
	import com.greensock.loading.LoaderMax;
	//import wopi.GameManager;

	public class WebService extends ServiceConfig
	{
		// temporary references: remember to nullify onComplete		
		private var _getTokenCallback:Function;
		private var _getPlayerInfoCallback:Function;
		private var _getQuestionsCallback:Function;;
		private var _getTimeCallback:Function;
		private var _onProgressCallback:Function;
		private var _getScoreCallback:Function;
		private var _setScoreCallback:Function;
		private var _getFbDataCallback:Function;
		private var _canBuyCallback1:Function;
		private var _canBuyCallback2:Function;
		private var _newLifeInCallback:Function;
		private var _checkAchievementsCallback:Function;
		private var _purchaseItemsCallback:Function;
		private var _getRankingCallBack:Function;
		private var _trophiesRoomCallBack:Function;
		private var _getFriendsCallBack:Function;
		private var _getHintsCallBack:Function;
		private var _getInboxCallBack:Function;
		private var _acceptLifeCallBack:Function;
		private var _sendLifeCallBack:Function;
		
		public function WebService() {}
		
		
		public function getToken(getTokenCallback:Function) 
		{
			_getTokenCallback = getTokenCallback;
			var loader:DataLoader = new DataLoader( baseUrl + "gTk.json", { 
				name:"token", estimatedBytes:200,
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetToken, onError:errorHandler } );
			loader.load();
		}
		private function onGetToken(event:LoaderEvent):void 
		{
			var o:Object = JSON.parse(decrypt(LoaderMax.getContent("token")));
			token = o.token;
			if (_getTokenCallback!=null) _getTokenCallback(o.general);
			_getTokenCallback = null;
			LoaderMax.getLoader("token").unload();
			
			// old implementation
			//token = decrypt(LoaderMax.getContent("token"));
			//if (_getTokenCallback!=null) _getTokenCallback();
			//_getTokenCallback = null;
			//LoaderMax.getLoader("token").unload();
		}
		
		public function getPlayerInfo(getPlayerInfoCallback:Function) 
		{
			_getPlayerInfoCallback = getPlayerInfoCallback;
			var values:String = token + "," + GameManager.fbData.id;
			
			var loader:DataLoader = new DataLoader( baseUrl + "gPi.json?p=" + encrypt(values), { 
				name:"playerInfo", estimatedBytes:500,
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetPlayerInfo, onError:errorHandler } );
			loader.load();				
		}
		private function onGetPlayerInfo(event:LoaderEvent):void 
		{				
			var data = decrypt(LoaderMax.getContent("playerInfo"));
			//if (GameManager.DEBUG_MODE) 
			//	data = '[{"id":"31","name":"M\u00f3rc\u00f3","lastName":"Dondero","idFacebook":"801834185","XP":"1","coins":"2082","highScore":"2000","XPpoints":"80","jokersUsed":"38","lives":"5","gameSessions":"201","zone1Count":"0","zone2Count":"0","zone3Count":"0","zone4Count":"0","zone5Count":"0","zone6Count":"0","lastScore":"0","lastPosition":"0","tournamentRead":"0","coinsEarned":"0","livesEarned":"0","percentXP":"80","items0":[{"id":"1","itemId":"bag1","title":"1000 coins","description":"Use coins to buy boosts.","price":"5.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"1000","discount":"0"},{"id":"2","itemId":"bag2","title":"10000 coins","description":"Use coins to buy boosts.","price":"8.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"10000","discount":"-5"},{"id":"3","itemId":"bag3","title":"25000 coins","description":"Use coins to buy boosts.","price":"20.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"25000","discount":"-10"},{"id":"4","itemId":"bag4","title":"50000 coins","description":"Use coins to buy boosts.","price":"40.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"50000","discount":"-20"},{"id":"5","itemId":"bag5","title":"100000 coins","description":"Use coins to buy boosts.","price":"70.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"100000","discount":"-50"}],"items1":[{"id":"6","itemId":"life1","title":"3 lives","description":"Use lives to play","price":"5.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"3","discount":"0"},{"id":"7","itemId":"life2","title":"4 lives","description":"Use lives to play","price":"8.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"4","discount":"-5"},{"id":"8","itemId":"life3","title":"15 lives","description":"Use lives to play","price":"20.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"15","discount":"-10"},{"id":"9","itemId":"life4","title":"40 lives","description":"Use lives to play","price":"40.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"40","discount":"-20"},{"id":"10","itemId":"life5","title":"100 lives","description":"Use lives to play","price":"70.000","image":"http:\/\/www.facebook.com\/images\/gifts\/21.png","value":"100","discount":"-50"}]}]';
			
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.playerInfo = JSON.parse(data);
			else 
				trace("playerInfo data loaded: " + data);
			if (_getPlayerInfoCallback!=null) _getPlayerInfoCallback();
			_getPlayerInfoCallback = null;
			LoaderMax.getLoader("playerInfo").unload();
		}
		
		public function getQuestions(getQuestionsCallback:Function, onProgressCallback:Function=null):void
		{
			_getQuestionsCallback = getQuestionsCallback;
			_onProgressCallback = onProgressCallback;
			var loader:DataLoader = new DataLoader( baseUrl + "gQ.json?p=" + encrypt(token), { 
				name:"getQuestions", estimatedBytes:40000, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetQuestions, onProgress:progressHandler, onError:errorHandler});
			loader.load();
		}
		private function progressHandler(event:LoaderEvent):void 
		{
			//trace("in webservice:", event.target.progress, event.target.bytesLoaded, event.target.bytesTotal);//LoaderMax.getLoader("mainQueue").rawProgress
			var p:Number = event.target.bytesLoaded / 40000;
			if (p > 1 || event.target.bytesLoaded == 0) p = 1
			if (_onProgressCallback!=null) _onProgressCallback(p);
		}
		private function onGetQuestions(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("getQuestions"));
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.questions = JSON.parse(data);
			else 
				trace("questions data loaded: " + data);
			
			if (_onProgressCallback!=null) _onProgressCallback(event.target.progress);
			if (_getQuestionsCallback!=null) _getQuestionsCallback();
			_getQuestionsCallback = null;
			_onProgressCallback = null;
			LoaderMax.getLoader("getQuestions").unload();
		}
		
		public function getFbData(getFbDataCallback:Function):void
		{
			_getFbDataCallback = getFbDataCallback;
			var values:String =  token;
			if (GameManager.DEBUG_MODE) values = values.concat("," + fbToken);
			trace(values);
			var loader:DataLoader = new DataLoader( baseUrl + "loginFB.json?p=" + encrypt(values), { 
				name:"getFbData", estimatedBytes:5000,
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetFbData, onError:errorHandler});
			loader.load();
		  }
		private function onGetFbData(event:LoaderEvent):void 
		{
			/**
			* {
			   "id": "801834185",
			   "name": "Marco Dondero",
			   "first_name": "Marco",
			   "last_name": "Dondero",
			   "link": "http://www.facebook.com/marco.dondero.3",
			   "username": "marco.dondero.3",
			   "gender": "male",
			   "locale": "en_US"
			} */
			var data = decrypt(LoaderMax.getContent("getFbData"));
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.fbData = JSON.parse(data);
			else
				trace("FB data loaded: " + data);
			
			if (_getFbDataCallback!=null) _getFbDataCallback();
			_getFbDataCallback = null;
			LoaderMax.getLoader("getFbData").unload();
		}
		
		public function joker1(idQuestion:int):void
		{
			var values:String = token + "," + idQuestion.toString();
			var loader:DataLoader = new DataLoader( baseUrl + "gJ1.json?p=" + encrypt(values), { 
				name:"joker1", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler});
			loader.load();
		}
		
		public function joker2():void
		{
			var loader:DataLoader = new DataLoader( baseUrl + "gJ2.json?p=" + encrypt(token), { 
				name:"joker2", estimatedBytes:100, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler});
			loader.load();
		}
		
		public function canBuy1(canBuyCallback:Function):void
		{
			_canBuyCallback1 = canBuyCallback;
			var values:String = token + ",1";
			var loader:DataLoader = new DataLoader( baseUrl + "canBuy.json?p=" + encrypt(values), { 
				name:"canBuy1", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onCanBuy1, onError:errorHandler});
			loader.load();
		}
		private function onCanBuy1(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("canBuy1"));
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				var result:String = data; // JSON.parse("[" + data + "]");
			else
				trace("canBuy1 data loaded: " + data);
			
			if (_canBuyCallback1!=null) _canBuyCallback1(parseInt(result));
			_canBuyCallback1 = null;
			LoaderMax.getLoader("canBuy1").unload();
		}
		public function canBuy2(canBuyCallback:Function):void
		{
			_canBuyCallback2 = canBuyCallback;
			var values:String = token + ",2";
			var loader:DataLoader = new DataLoader( baseUrl + "canBuy.json?p=" + encrypt(values), { 
				name:"canBuy2", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onCanBuy2, onError:errorHandler});
			loader.load();
		}
		private function onCanBuy2(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("canBuy2"));
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				var result:String = data; // JSON.parse("[" + data+ "]");
			else
				trace("canBuy data loaded: " + data);
			
			if (_canBuyCallback2!=null) _canBuyCallback2(parseInt(result));
			_canBuyCallback2 = null;
			LoaderMax.getLoader("canBuy2").unload();
		}
		
		public function setScore(setScoreCallback:Function):void
		{
			_setScoreCallback = setScoreCallback;
			var loader:DataLoader = new DataLoader( baseUrl + "sSFB.json?p=" + encrypt(token), { 
				name:"setScore", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onSetScore, onError:errorHandler});
			loader.load();
		}		
		private function onSetScore(event:LoaderEvent):void 
		{
			//var data = decrypt(LoaderMax.getContent("setScore"));
			//if ( data != "Invalid Token" && data != null && data != "undefined" )
			//	trace("setScore data loaded: " + data);
			//else 
			//	trace("setScore data loaded: " + data);
			
			if (_setScoreCallback!=null) _setScoreCallback();
			_setScoreCallback = null;
			LoaderMax.getLoader("setScore").unload();
		}
		
		public function getScore(getScoreCallback:Function):void
		{
			_getScoreCallback = getScoreCallback;			
			var loader:DataLoader = new DataLoader( baseUrl + "gS.json?p=" + encrypt(token), { 
				name:"getScore", estimatedBytes:400, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetScore, onError:errorHandler});
			loader.load();
		}
		private function onGetScore(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("getScore"));
			//score, bonus, coins, xpPercent, xpLevel
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.serverScore = JSON.parse(data);
			else 
				trace("server score data loaded: " + data);
			
			if (_getScoreCallback!=null) _getScoreCallback();
			_getScoreCallback = null;
			LoaderMax.getLoader("getScore").unload();
		}
		
		/**
		 * Sends answer to server for validation
		 * @param	ansCallback
		 * @param	idAnswer
		 * @param	idQuestion
		 */
		public function sendAnswer(idAnswer:int, idQuestion:int,points:int):void 
		{
			var values:String = token + "," + idAnswer.toString() + "," + idQuestion.toString() + "," + points.toString() + "," + GameManager.ansTime.toFixed(4);
			var loader:DataLoader = new DataLoader( baseUrl + "sA.json?p=" + encrypt(values), { 
				name:"sendAns", estimatedBytes:500,
				auditSize:false, allowMalformedURL:true, autoDispose:true,				
				onError:errorHandler});
			loader.load();
		}
		
		/**
		 * Get the server time for game sync.
		 * @param	getTimeCallback: the callback when server answered.
		 */
		public function getTime(getTimeCallback:Function):void 
		{
			_getTimeCallback = getTimeCallback;
			var loader:DataLoader = new DataLoader( baseUrl + "gT.json?p=" + encrypt(token), { 
				name:"getTime", estimatedBytes:100, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,				
				onComplete:onGetTime, onError:errorHandler});
			loader.load();			
		}
		private function onGetTime(event:LoaderEvent):void 
		{
			//var data = decrypt(LoaderMax.getContent("getTime"));
			var data = LoaderMax.getContent("getTime");
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				if(_getTimeCallback!=null) _getTimeCallback( data );
			else 
				trace("server Get Time: " + data);
			
			_getTimeCallback = null;
			LoaderMax.getLoader("getTime").unload();
		}		
		
		/**
		 * gets the time left to regenerate a new life
		 * @param	newLifeInCallback
		 */
		public function newLifeIn(newLifeInCallback:Function):void 
		{
			_newLifeInCallback = newLifeInCallback;
			var loader:DataLoader = new DataLoader( baseUrl + "nL.json?p=" + encrypt(token), { 
				name:"newLifeIn", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,				
				onComplete:onNewLifeIn, onError:errorHandler});
			loader.load();			
		}
		private function onNewLifeIn(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("newLifeIn"));
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				if(_newLifeInCallback!=null) _newLifeInCallback( data );
			else 
				trace("server new Life In : " + data);
			_newLifeInCallback = null;
			LoaderMax.getLoader("newLifeIn").unload();
		}
		
		public function checkAchievements(checkAchievementsCallback:Function) 
		{
			_checkAchievementsCallback = checkAchievementsCallback;
			var loader:DataLoader = new DataLoader( baseUrl + "checkAchievements.json?p=" + encrypt(token), { 
				name:"checkAchievements", estimatedBytes:800,
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onCheckAchievements, onError:errorHandler } );
			loader.load();
		}
		private function onCheckAchievements(event:LoaderEvent):void 
		{
			/* on return
			 [{"id":"4","stars":1,"name":"Celebrities",         "XP":"110","coins":"1077","lives":"9"},
			  {"id":"2","stars":1,"name":"Movies",              "XP":"110","coins":"1077","lives":"9"},
			  {"id":"6","stars":1,"name":"General Knowledge",   "XP":"110","coins":"1077","lives":"9"},
			  {"id":"1","stars":2,"name":"History \/ Geography","XP":"110","coins":"1077","lives":"9"},
			  {"id":"3","stars":1,"name":"Music",               "XP":"110","coins":"1077","lives":"9"},
			  {"id":"5","stars":1,"name":"Sports",              "XP":"110","coins":"1077","lives":"9"}]
			*/
			var data = decrypt(LoaderMax.getContent("checkAchievements"));
			
			if (GameManager.DEBUG_MODE) 
			{
				data = '[{"id":"4","stars":1,"name":"Celebrities","XP":"110","coins":"1077","lives":"9"},\
				{"id":"2", "stars":1, "name":"Movies",              "XP":"110", "coins":"1077", "lives":"9" },\
				{"id":"6", "stars":1, "name":"General Knowledge",   "XP":"110", "coins":"1077", "lives":"9" },\
				{"id":"1", "stars":2, "name":"History \/ Geography", "XP":"110", "coins":"1077", "lives":"9" },\
				{"id":"3", "stars":1, "name":"Music",               "XP":"110", "coins":"1077", "lives":"9" },\
				{"id":"5","stars":1,"name":"Sports",              "XP":"110","coins":"1077","lives":"9"}]';
			}
			
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.fbAchievements = JSON.parse(data);
			else
				trace("checkAchievements data loaded: " + data);
			
			if (_checkAchievementsCallback!=null) _checkAchievementsCallback();
			_checkAchievementsCallback = null;
			LoaderMax.getLoader("checkAchievements").unload();
		}
		
		public function checkOG() : void
		{
			var loader:DataLoader = new DataLoader( baseUrl + "checkOG.json?p=" + encrypt(token), { 
				name:"checkOG", estimatedBytes:50,
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler } );
			loader.load();
		}
		
		/**
		 * Called when user reached the MAX of the combo bar
		 */
		public function setCombo(idQuestion:String):void
		{
			var values:String = token + "," + idQuestion;
			var loader:DataLoader = new DataLoader( baseUrl + "setCombo.json?p=" + encrypt(values), { 
				name:"setCombo", estimatedBytes:50,
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler } );
			loader.load();
		}
		
		public function consumeLife():void 
		{
			var loader:DataLoader = new DataLoader( baseUrl + "consumeLife.json?p=" + encrypt(token), { 
				name:"consumeLife", estimatedBytes:50,
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler } );
			loader.load();
		}
		
		public function purchaseItems(idItem:String, purchaseItemsCallback:Function):void 
		{
			_purchaseItemsCallback = purchaseItemsCallback;
			var values:String = token + "," + idItem.toString()   ;
			var loader:DataLoader = new DataLoader( baseUrl + "purchaseItems.json?p=" + encrypt(values), { 
				name:"purchaseItems", estimatedBytes:100,
				auditSize:false, allowMalformedURL:true, autoDispose:true,				
				onComplete:onPpurchaseItems, onError:errorHandler});
			loader.load();
		}
		private function onPpurchaseItems(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("purchaseItems"));
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				if(_purchaseItemsCallback!=null) _purchaseItemsCallback( parseInt(data) );
			else 
				trace("server purchase Items: " + data);				
			
			_purchaseItemsCallback = null;
			LoaderMax.getLoader("purchaseItems").unload();
		}	
		
		public function getRanking(getRankingCallBack:Function):void 
		{
			_getRankingCallBack = getRankingCallBack;
			var loader:DataLoader = new DataLoader( baseUrl + "getRanking.json?p=" +  encrypt(token), { 
				name:"getRanking", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetRanking, onError:errorHandler});
			loader.load();
		}
		private function onGetRanking(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("getRanking"));
			if (GameManager.DEBUG_MODE) {
				data = '[{"position": 1,"name": "Messi","score": "150203","id": 676049625},\
						 {"position": 2, "name": "Diego", "score": 90000, "id": 801834185 } ]';
			}
			
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.newRank = JSON.parse(data);
			else
				trace("getRanking data loaded: " + data);
			
			if (_getRankingCallBack!=null) _getRankingCallBack();
			_getRankingCallBack = null;
			LoaderMax.getLoader("getRanking").unload();
		}
		
	
		public function trophiesRoom(trophiesRoomCallBack:Function):void 
		{
			_trophiesRoomCallBack = trophiesRoomCallBack;
			
			var loader:DataLoader = new DataLoader( baseUrl + "trophiesRoom.json?p=" + encrypt(token), { 
				name:"trophiesRoom", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onTrophiesRoom, onError:errorHandler});
			loader.load();
		}
		private function onTrophiesRoom(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("trophiesRoom"));
			//if (GameManager.DEBUG_MODE) {
			//	data = '[{"position": 1,"name": "Messi","score": "150203","id": 676049625},\
			//			 {"position": 2, "name": "Diego", "score": 90000, "id": 801834185 } ]';
			//}
			
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.trophies = JSON.parse(data);
			else
				trace("trophiesRoom data loaded: " + data);
			
			if (_trophiesRoomCallBack!=null) _trophiesRoomCallBack();
			_trophiesRoomCallBack = null;
			LoaderMax.getLoader("trophiesRoom").unload();
		}
		
		public function postPass(pointsDiff:int, friendFBId:String):void 
		{
			var values:String =  token + "," + pointsDiff.toString() + "," + friendFBId;
			var loader:DataLoader = new DataLoader( baseUrl + "postPass.json?p=" + encrypt(values), { 
				name:"postPass", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler});
			loader.load();
		}		
		
		public function postEarn(position:int):void 
		{
			var values:String =  token + "," + position.toString();
			var loader:DataLoader = new DataLoader( baseUrl + "postEarn.json?p=" + encrypt(values), { 
				name:"postEarn", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler});
			loader.load();			
		}
		
		public function getFriends(type:String, idFBSelected:String, getFriendsCallBack:Function):void 
		{
			_getFriendsCallBack = getFriendsCallBack;
			/** type: invites or lives */
			var values:String =  token + "," + type + "," + idFBSelected;
			var loader:DataLoader = new DataLoader( baseUrl + "getFriends.json?p=" + encrypt(values), { 
				name:"getFriends", estimatedBytes:5000, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetFriends, onError:errorHandler});
			loader.load();
		}
		private function onGetFriends(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("getFriends"));
			
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.friends = JSON.parse(data);
			else
				trace("getFriends data loaded: " + data);
			
			if (_getFriendsCallBack!=null) _getFriendsCallBack();
			_getFriendsCallBack = null;
			LoaderMax.getLoader("getFriends").unload();
		}
		
		public function getHints(getHintsCallBack:Function):void 
		{
			_getHintsCallBack = getHintsCallBack;
			var values:String =  token;
			var loader:DataLoader = new DataLoader( baseUrl + "getHints.json?p=" + encrypt(values), { 
				name:"getHints", estimatedBytes:5000, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetHints, onError:errorHandler});
			loader.load();
		}
		private function onGetHints(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("getHints"));
			
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.hints = JSON.parse(data);
			else
				trace("getHints data loaded: " + data);
			
			if (_getHintsCallBack!=null) _getHintsCallBack();
			_getHintsCallBack = null;
			LoaderMax.getLoader("getHints").unload();
		}
		
		public function insertInbox(data:String, type:String, isReply:int=0):void 
		{
			var values:String =  token + ";" + data + ";" + type + ";" + isReply.toString();
			var loader:DataLoader = new DataLoader( baseUrl + "insertInbox.json?p=" + encrypt(values), { 
				name:"insertInbox", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler});
			loader.load();
		}
		
		public function getInbox(getInboxCallBack:Function):void 
		{
			_getInboxCallBack = getInboxCallBack;
			var values:String =  token;
			var loader:DataLoader = new DataLoader( baseUrl + "getInbox.json?p=" + encrypt(values), { 
				name:"getInbox", estimatedBytes:5000, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onGetInbox, onError:errorHandler});
			loader.load();
		}
		private function onGetInbox(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("getInbox"));
			
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				GameManager.inbox = JSON.parse(data);
			else
				trace("getInbox data loaded: " + data);
			
			if (_getInboxCallBack!=null) _getInboxCallBack();
			_getInboxCallBack = null;
			LoaderMax.getLoader("getInbox").unload();
		}
		
		public function acceptLife(data:String, acceptLifeCallBack:Function):void
		{
			_acceptLifeCallBack = acceptLifeCallBack;
			var values:String =  token + ";" + data;
			var loader:DataLoader = new DataLoader( baseUrl + "acceptLife.json?p=" + encrypt(values), { 
				name:"acceptLife", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onAcceptLife, onError:errorHandler});
			loader.load();
		}
		private function onAcceptLife(event:LoaderEvent):void 
		{
			var data = decrypt(LoaderMax.getContent("acceptLife"));
			if ( data != "Invalid Token" && data != null && data != "undefined" )
				if(_acceptLifeCallBack!=null) _acceptLifeCallBack( parseInt(data) );
			else 
				trace("acceptLife data: " + data);				
			
			_acceptLifeCallBack = null;
			LoaderMax.getLoader("acceptLife").unload();
		}
		
		public function rejectLife(data:String):void 
		{
			var values:String =  token + ";" + data;
			var loader:DataLoader = new DataLoader( baseUrl + "rejectLife.json?p=" + encrypt(values), { 
				name:"rejectLife", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onError:errorHandler});
			loader.load();
		}
		
		public function sendLife(data:String, sendLifeCallBack:Function):void 
		{
			_sendLifeCallBack = sendLifeCallBack;
			var values:String =  token + ";" + data;
			var loader:DataLoader = new DataLoader( baseUrl + "sendLife.json?p=" + encrypt(values), { 
				name:"sendLife", estimatedBytes:200, 
				auditSize:false, allowMalformedURL:true, autoDispose:true,
				onComplete:onSendLife, onError:errorHandler});
			loader.load();
		}
		private function onSendLife(event:LoaderEvent):void 
		{
			//var data = decrypt(LoaderMax.getContent("sendLife"));
			//if ( data != "Invalid Token" && data != null && data != "undefined" )
				if(_sendLifeCallBack!=null) _sendLifeCallBack();
			//else 
			//	trace("sendLife data: " + data);				
			
			_sendLifeCallBack = null;
			LoaderMax.getLoader("sendLife").unload();
		}
	}

}