package  
{

	import be.boulevart.as3.security.Base64;
	import com.google.analytics.AnalyticsTracker;
	import com.google.analytics.GATracker;
	import com.greensock.events.LoaderEvent;
	import com.greensock.loading.LoaderMax;
	import com.greensock.loading.XMLLoader;
	import com.rafaelrinaldi.sound.sound;
	import fl.lang.Locale;
	import flash.display.Stage;
	import flash.display.StageQuality;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.external.ExternalInterface;
	import flash.net.SharedObject;
	import webservice.ExternalService;
	import webservice.WebService;
	
	/**
	 * General game data and management
	 * @author Marco
	 */	
	public class GameManager
	{
		static public const DEBUG_MODE:Boolean = false;
		
		// These are General configurations, set by xml:
		static private var _time:int;
		static private var _comboDuration:int;//seconds the combo lasts before it is reset to zero
		static private var _serverSyncLapse:int = 15; //secs
		static private var _lifeRegenTime:int = 6; //secs		
		static private var _comboChain:Vector.<int>;
		static private var _joker2Time:int = 20;
		static private var _joker2Questions:int = 10;
		static private var _wait:int = 3600; // wait time te resend a life, default 3600
		
		// references and instances of sevices for ease access (like a helper)
		static public var stage:Stage;
		static public var game:Game;
		static public var ws:WebService;
		static public var ps:ExternalService;
		static public var score:Score;
		static public var tracker:AnalyticsTracker;
		
		// data during the game
		static public var serverScore:Object;
		static public var playerInfo:Object;
		static public var questions:Object;	
		static public var fbData:Object;
		static public var fbAchievements:Object;
		static public var newRank:Object;
		static public var trophies:Object;		
		static public var friends:Object;
		static public var hints:Object;
		static public var inbox:Object;
		static public var slots:Object = [0, 0];
		static public var joker1used:Boolean = false;
		static public var joker2used:Boolean = false;
		static public var gotMaxCombo:Boolean = false;
		static public var joker1:String;
		static public var ansTime:Number=0;
		//static public var tutorialShown:Boolean = false;
		
		static private var _firstTime:Boolean = false;
		static private var _soundEnabled:Boolean;
		static private var _rootURL:String;		
		static private var _localDataStor:SharedObject;
		static private var _dispatcher:EventDispatcher = new EventDispatcher();
		
		public function GameManager() 
		{
			throw new Error("GameManager is a Singleton");
		}
		
		static public function init(_stage:Stage,_locale:String, _game:Game):void 
		{
			// set references
			stage = _stage;
			
			// this is a performance killer. we have to look other way to antialiasing.
			//stage.quality = "16X16LINEAR"; // or "16X16"
			
			game = _game;
			ws = new WebService();
			ps = new ExternalService();
			_rootURL = getRootURL();
			
			// if not availabe, use default values
			if (_locale!=null && _locale!=""){
				//locale = _locale;
			}else{
				_locale = "en-us";
			}
			
			/* locale, xml and webService can run in parallel. The flow contines after ws.getToken */
			
			// locale mgmt
			Locale.addXMLPath("fr-fr", _rootURL + "locale/fr-fr.xml");
			Locale.addXMLPath("pt-br", _rootURL + "locale/pt-br.xml");
			Locale.addXMLPath("en-us", _rootURL + "locale/en-us.xml");
			Locale.addXMLPath("en-uk", _rootURL + "locale/en-uk.xml");
			Locale.autoReplace = false;
			//Locale.setLoadCallback(localeCallback);
			Locale.loadLanguageXML(_locale.toLowerCase(), localeCallback);
			game.updateInitProgress(92);
			
			
			_comboChain = new Vector.<int>;
			
			// old implementation to get the configuration
			// configuration files xml
			//getXmlData();
			
			// init web service, get the token! (and general game settings)
			ws.getToken(onGetToken);
			
			// init sounds
			sound().add("musicLobby", HomeLoop1and2);
			//sound().group("home").add("home1",HomeLoop1);
			//sound().group("home").add("home2",HomeLoop2);
			
			sound().add("sndBtnClick", Button2);
			sound().add("sndBtn", SndBtnClick);
			sound().add("comboX1", ComboX1);
			sound().add("comboX2", ComboX2);
			sound().add("comboX3", ComboX3);
			sound().add("comboX4", ComboX4);
			sound().add("comboX5", ComboX5);
			sound().add("comboX6", ComboX6);
			sound().add("comboX7", ComboX7);
			sound().add("comboFire", ComboFire);
			sound().add("fillCombo", FillCombo);
			sound().group("levelMusic").add("lev1", InGame);
			//sound().group("levelMusic").add("lev2", SndBtnClick);
			//sound().group("levelMusic").add("lev3", SndBtnClick);
			//sound().group("levelMusic").add("lev4", SndBtnClick);
			//sound().group("levelMusic").add("lev5", SndBtnClick);
			//sound().add("QuestionRight", QuestionRight);
			sound().add("QuestionWrong", QuestionWrong);
			sound().add("boostBuy", BuyJokers);
			sound().add("fireworks", Fireworks);
			sound().add("showPopin", ShowPopin);
			sound().add("coins", Coins);
			sound().add("highScore", HighScore);
			sound().add("scoreScreen", ScoreScreen);
			sound().add("overRank", OverRank);
			sound().add("tournament", Tournament1);
			sound().add("freeze", Freeze);
			sound().add("TimeOut", Countdown);
			
			sound().add("boostReturn", SndBtnClick);
			
			sound().global().volume = 1.0; // or set to 1.2 to increase a little bit
			
			/* local Data Shared Object */
			_localDataStor = SharedObject.getLocal("localDataStor");
			if (_localDataStor.data.snd == undefined) {
				soundEnabled = true;
			}else {
				soundEnabled = _localDataStor.data.snd;
			}			
			
			game.updateInitProgress(91);
		}
		
		static private function getRootURL():String 
		{
			var url:String = new String();
			// local or web ?
			if (ExternalInterface.available) {
				url = stage.root.loaderInfo.loaderURL;
				var swf:String = new String();
				swf = url.slice(url.lastIndexOf('/') + 1, url.length);
				url = url.slice(0, url.length - swf.length);
			}
			return url;
		}
		
		static private function localeCallback(success:Boolean):void 
		{
			if(success){
				game.updateInitProgress(94);				
			}else {
				trace("error : Locale");
			}
		}
		
		static private function getXmlData():void 
		{
			var queue:LoaderMax = new LoaderMax({name:"mainQueue", onComplete:xmlLoadComplete, onProgress:xmlProgress, autoDispose:true});
			
			// config data
			queue.append( new XMLLoader(_rootURL + "configuration.xml", { name:"configuration-encoded" } ) );
			queue.load();
		}
		
		static private function xmlProgress(event:LoaderEvent):void 
		{
			game.updateInitProgress(93);
			//game.updateInitProgress(91+event.target.progress*3);
		}
		
		static private function xmlLoadComplete(event:LoaderEvent):void 
		{
			var data:XML = new XML(Base64.decode(LoaderMax.getContent("configuration-encoded")));
			
			// general settings
			_time = parseInt(data.general.time);
			_comboDuration = parseInt(data.general.comboDuration);
			_serverSyncLapse = parseInt(data.general.serverSyncLapse);
			_lifeRegenTime = parseInt(data.general.lifeRegenTime);
			for (var i:int = 0; i < data.general.comboChain.step.length(); i++) 
				_comboChain.push(data.general.comboChain.step[i]);
			_joker2Time = parseInt(data.general.joker2Time);
			_joker2Questions = parseInt(data.general.joker2Questions);
			
			game.updateInitProgress(96);
		}
		
		//old static private function onGetToken():void 
		static private function onGetToken(general:Object):void 
		{
			// general settings
			_time = parseInt(general.gameTime);
			_comboDuration = parseInt(general.comboTime);
			for (var i:int = 1; i < 8; i++) 
				_comboChain.push(general["combo"+i.toString()]);
			_serverSyncLapse = parseInt(general.synchTime);
			_lifeRegenTime = parseInt(general.lifeRegTime);
			_joker2Time = parseInt(general.freezeTime);
			_joker2Questions = parseInt(general.freezeQuestions);			
			_wait = parseInt(general.wait);			
			
			tracker = new GATracker(game, general.GAtrack, "AS3", false);
			
			game.updateInitProgress(99);
			ws.getFbData(onFbData);
		}
		
		static private function onFbData():void 
		{
			game.updateInitProgress(100);
			dispatchEvent(new GameEvent(Event.COMPLETE));
		}
		
		/* stores sound status in shared object on change */
		static public function get soundEnabled():Boolean { return _soundEnabled; }
		static public function set soundEnabled(value:Boolean):void 
		{
			_soundEnabled = value;
			_localDataStor.data.snd = _soundEnabled;
			value ? sound().global().unmute() : sound().global().mute();
		}
		
		/* firstTime the game was played to show Help Screen or tutorial automatically*/
		static public function get firstTime():Boolean { return _firstTime; }
		//static public function set firstTime(value:Boolean):void 
		//{
		//	_firstTime = value;
		//}
		
		static public function get comboDuration():int {
			return _comboDuration;
		}	
		static public function get time():int {
			return _time; //_time[_currentLevel - 1];
		}
		
		static public function get serverSyncLapse():int 
		{
			return _serverSyncLapse;
		}
		
		static public function get lifeRegenTime():int 
		{
			return _lifeRegenTime;
		}
		
		static public function get comboChain():Vector.<int> 
		{
			return _comboChain;
		}
		
		static public function get joker2Time():int 
		{
			return _joker2Time;
		}
		
		static public function get joker2Questions():int 
		{
			return _joker2Questions;
		}
		
		static public function get wait():int 
		{
			return _wait;
		}
		
		static public function set time(value:int):void 
		{
			_time = value;
		}
		
        public static function addEventListener(type:String, listener:Function, useCapture:Boolean = false, priority:int = 0, useWeakGameManager:Boolean = false):void {
            _dispatcher.addEventListener(type, listener, useCapture, priority, useWeakGameManager);
        }
        public static function removeEventListener(type:String, listener:Function, useCapture:Boolean = false):void {
            _dispatcher.removeEventListener(type, listener, useCapture);
        }
        public static function dispatchEvent(event:GameEvent):Boolean {
            return _dispatcher.dispatchEvent(event);
        }
        public static function hasEventListener(type:String):Boolean {
            return _dispatcher.hasEventListener(type);
        }
		
		static public function updateBoost1(result:int):void 
		{
			if (result < 0)
			{
				slots[0] = 0;
				var cond:Boolean = false;
				while (!cond) 
				{
					if (game != null) 
						game.disableBoost(1);
						cond = true;
				}
			}
		}
		static public function updateBoost2(result:int):void 
		{
			if (result < 0)
			{
				slots[1] = 0;
				var cond:Boolean = false;
				while (!cond) 
				{
					if (game != null) 
						game.disableBoost(2);
						cond = true;
				}
			}
		}
	}

}