package
{
import flash.display.Sprite;
//import flash.filesystem.File;
import flash.system.Capabilities;
import starling.core.Starling;
import starling.events.Event;
import starling.textures.Texture;
import starling.utils.AssetManager;

// If you set this class as your 'default application', it will run without a preloader.
// To use a preloader, see 'Demo_Web_Preloader.as'.

[SWF(width="760",height="600",frameRate="60",backgroundColor="#CEECF4")]

public class Demo_Web extends Sprite
{
	[Embed(source="../../assets_system/quizdomBack.jpg")]
	private var Background:Class;
	
	private var mStarling:Starling;
	
	public function Demo_Web()
	{
		if (stage)
			start();
		else
			addEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
	}
	
	private function start():void
	{
		Starling.multitouchEnabled = true; // for Multitouch Scene
		Starling.handleLostContext = true; // required on Windows, needs more memory
		
		// To use the standard "baseline" mode, pass the profile manually:
		mStarling = new Starling(Game, stage);
		mStarling.simulateMultitouch = true;
		mStarling.enableErrorChecking = Capabilities.isDebugger;
		mStarling.start();
		
		// this event is dispatched when stage3D is set up
		mStarling.addEventListener(Event.ROOT_CREATED, onRootCreated);
	}
	
	private function onAddedToStage(event:*):void
	{
		removeEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
		start();
	}
	
	private function onRootCreated(event:Event, game:Game):void
	{
		// set framerate to 30 in software mode
		if (mStarling.context.driverInfo.toLowerCase().indexOf("software") != -1)
			mStarling.nativeStage.frameRate = 30;
		
		// define which resources to load
		var assets:AssetManager = new AssetManager();
		
		
		assets.verbose = Capabilities.isDebugger;
		assets.enqueue("../../assets/textures/ship_rod/texture.png");
     	assets.enqueue(EmbeddedAssets);
		
		// background texture is embedded, because we need it right away!
		var bgTexture:Texture = Texture.fromBitmap(new Background());
		
		// game will first load resources, then start menu
		game.start(bgTexture, assets);
	}
}
}