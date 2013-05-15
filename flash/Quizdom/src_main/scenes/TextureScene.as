package scenes
{
	/*import fl.motion.AnimatorFactory;
	import dragonBones.factorys.StarlingFactory;
	import dragonBones.factorys.StarlingFactory;
	import dragonBones.factorys.StarlingFactory;
	import dragonBones.factorys.StarlingFactory;
	import dragonBones.factorys.StarlingFactory;
	import dragonBones.objects.SkeletonData;
	import dragonBones.objects.SkeletonData;
	import fl.motion.Motion;
	import fl.motion.MotionBase;*/
	import flash.geom.Point;
	import flashx.textLayout.formats.Float;
	import starling.animation.Transitions;
	import starling.animation.Tween;
	import starling.core.Starling;
    import starling.display.Image;
	import starling.display.MovieClip;
	import starling.display.Sprite;
	import starling.events.Event;
    import starling.text.TextField;
    import starling.textures.Texture;
	import starling.textures.TextureAtlas;
	import starling.display.BlendMode;
	import treefortress.spriter.AnimationSet;
	
	import treefortress.spriter.SpriterClip;
	import treefortress.spriter.SpriterLoader;
	
	import dragonBones.animation.WorldClock;
	import dragonBones.Armature;
	import dragonBones.Bone;
	import dragonBones.factorys.StarlingFactory;
	import dragonBones.objects.SkeletonData;
	import dragonBones.objects.XMLDataParser;
	import dragonBones.textures.StarlingTextureAtlas;
	import starling.events.EnterFrameEvent;
	import starling.events.KeyboardEvent;		

    public class TextureScene extends Scene
    {
		
		[Embed(source="../../assets/textures/4x/ship.scml", mimeType="application/octet-stream")]
	   public static const OrcScml:Class;
		
		[Embed(source="../../assets/textures/4x/ship.xml", mimeType="application/octet-stream")]
	   public static const TexturePackerXml:Class;
		
		[Embed(source="../../assets/textures/4x/ship.png")]
	   public static const TexturePackerBitmap:Class;
	   
	   /*
	   	[Embed(source = "../../assets/textures/1x/ship_rod/skeleton.xml", mimeType = "application/octet-stream")]
		public static const SkeletonXMLData:Class;
	
		[Embed(source = "../../assets/textures/1x/ship_rod/texture.xml", mimeType = "application/octet-stream")]
		public static const TextureXMLData:Class;
		*/
		
		[Embed(source = "../../assets/textures/ship_rod/skeleton.xml", mimeType = "application/octet-stream")]
		public static const SkeletonXMLData:Class;
 
		[Embed(source = "../../assets/textures/ship_rod/texture.xml", mimeType = "application/octet-stream")]
		public static const TextureXMLData:Class;
	
		//[Embed(source = "../../assets/textures/1x/ship_rod/texture.png")]
		//public static const TextureData:Class;
	   
		private var mMovie:MovieClip;
		private var barco:Sprite;
		
		protected var brawler:SpriterClip;
		protected var spriterLoader:SpriterLoader;
		
		/** Current date. */
		private var _currentDate:Date;
		
		private var image2:Image;
		
		private var i:int = 0;
		
		public static var instance:TextureScene;

		private var factory:StarlingFactory;
		private var armature:Armature;
		private var armatureClip:Sprite;
		
        public function TextureScene(){	
			addEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
		}
		
		private function onEnterFrameHandler(_e:EnterFrameEvent):void {
			WorldClock.clock.advanceTime(-1);
		}
		
		protected function createSriterClip(animation:AnimationSet, atlas:TextureAtlas):void {
			//Create Character
			/*var orc:SpriterClip = new SpriterClip(animation, atlas); 
			orc.play("fall");
			orc.scaleX = orc.scaleY = 1;
			orc.x = 100;
			orc.y = 200;
			addChild(orc);
			Starling.juggler.add(orc);*/
		}	
		
		protected function onSpriterLoaderComplete(loader:SpriterLoader):void {
			
			//Add Orc 1
			/*orc = spriterLoader.getSpriterClip("bandera");
			orc.play("fall", 0);
			orc.scaleX = -1;
			orc.y = 50;
			orc.x = 300;
			orc.playbackSpeed = 1;
			addChild(orc);*/
			
			//For performance reasons, SpriterClips will not update themselves, they must externally ticked each frame. 
			//The Starling Juggler is a simple way to do that.
			//Starling.juggler.add(orc);
			
			//Add a "Brawler"
			/*brawler = spriterLoader.getSpriterClip("bandera");
			brawler.setPosition(200, 200);
			brawler.play("fall");
			addChild(brawler);
			Starling.juggler.add(brawler);
			*/
			//Add Touch Support to each Sprite
			//orc.touchable = true;
			//orc.addEventListener(TouchEvent.TOUCH, onCharacterTouched);
			//brawler.touchable = true;
			//brawler.addEventListener(TouchEvent.TOUCH, onCharacterTouched);
			
		}
		
		private function deg2rad(degree:Number):Number {
			return degree * (Math.PI / 180);
		}
		
        private function onAddedToStage():void
        {
           
			//Game.assets.enqueue("../../assets/textures/ship_rod/texture.png");
			instance = this;
		
			factory = new StarlingFactory();

			//
			var skeletonData:SkeletonData = XMLDataParser.parseSkeletonData(XML(new SkeletonXMLData()));
			factory.addSkeletonData(skeletonData);
		
			
			var texture:Texture = Game.assets.getTexture("texture");
			var textureAtlas:StarlingTextureAtlas = new StarlingTextureAtlas(texture, XML(new TextureXMLData()));
			
			factory.addTextureAtlas(textureAtlas);
			
			armature = factory.buildArmature("masterShip");
			armatureClip = armature.display as Sprite;
			armatureClip.x = 0;
			armatureClip.y = 0;
			addChild(armatureClip);
			
			WorldClock.clock.add(armature);
			addEventListener(EnterFrameEvent.ENTER_FRAME, onEnterFrameHandler);
			
			this.addEventListener(Event.ENTER_FRAME, floatingAnimation);
			this.removeEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
			stage.addEventListener(KeyboardEvent.KEY_DOWN, onKeyEventHandler);
			stage.addEventListener(KeyboardEvent.KEY_UP, onKeyEventHandler);
			//Starling.juggler.add(mMovie);
        }
		
		private function floatingAnimation(event:Event):void
		{
			_currentDate = new Date();
			armatureClip.y = 10 + (Math.cos(_currentDate.getTime() * 0.002)) * 15;
		//	playBtn.y = 340 + (Math.cos(_currentDate.getTime() * 0.002)) * 10;
		//	aboutBtn.y = 460 + (Math.cos(_currentDate.getTime() * 0.002)) * 10;
		}
		
		private function onKeyEventHandler(e:KeyboardEvent):void {
			trace(e.keyCode );
			switch (e.keyCode) {
				case 97 :
					armature.animation.gotoAndPlay("cannon1");
					break;
				case 98 :
					armature.animation.gotoAndPlay("cannon2");
					break;
				case 99 :
					armature.animation.gotoAndPlay("cannon3");
					break;	
				case 100 :
					armature.animation.gotoAndPlay("cannon4");
					break;	
				case 101 :
					trace("entro 101");
					armature.animation.gotoAndPlay("cannon5");
					break;	
				case 90 :
					
					trace("90!!!!!!");
					
					var _armR:Bone = armature.getBone("ship_3");
				
					if (_armR != null)
					{
					var tween_bone:Tween;
					tween_bone = new Tween(_armR.origin, 1, Transitions.EASE_OUT);
					tween_bone.animate("x", _armR.origin.x -50);
					tween_bone.animate("y", _armR.origin.y - 500);
					tween_bone.animate("rotation", deg2rad(360));
					
					//tween_bone.fadeTo(1);   
					tween_bone.onComplete = function():void { 
						trace("complete");
						armature.removeBoneByName("ship_3");

						};
						
					Starling.juggler.add(tween_bone);
					}
					break;
				case 88 :	
					trace("90!!!!!!"); 
					
					
					_armR = armature.getBone("ship_4"); 
					_armR.origin.x = 500;
					
					//tween_bone:Tween;
					/*trace("start x=" +_armR.origin.x );
					if (_armR != null)
					{
					tween_bone = new Tween(_armR.origin, 1.5, Transitions.EASE_OUT);
					tween_bone.animate("x", _armR.origin.x + 300);
					tween_bone.animate("y", _armR.origin.y - 300);
					tween_bone.animate("rotation", deg2rad(360));
					//Starling.juggler.add(tween_bone);
					//tween_bone.fadeTo(1);   
					/*tween_bone.onComplete = function():void { 
						tween_bone = new Tween(_armR.origin, 1.5, Transitions.EASE_OUT);
						trace("complete x=" +_armR.origin.x );
						//armature.removeBoneByName("ship_4");
						tween_bone.animate("x", _armR.origin.x + 400);
					    tween_bone.animate("y", _armR.origin.y + 400);
					    tween_bone.animate("rotation", deg2rad(360));
						};*/
						
					/*var tween_bone2:Tween = new Tween(_armR.origin, 1.5, Transitions.EASE_OUT);
					tween_bone2.animate("x", _armR.origin.x + 600);
					tween_bone2.animate("y", _armR.origin.y + 300);
					tween_bone2.animate("rotation", deg2rad(720));
					
					tween_bone2.delay = tween_bone.totalTime-0.8;
					
					Starling.juggler.add(tween_bone);
					Starling.juggler.add(tween_bone2);*/
					
					//}
					break;	
				case 67 :	
					trace("67!!!!!!");
					_armR = armature.getBone("ship_5");
					
					//tween_bone:Tween;
					if (_armR != null)
					{
					tween_bone = new Tween(_armR.origin, 1.5, Transitions.EASE_OUT);
					tween_bone.animate("x", _armR.origin.x + 800);
					tween_bone.animate("y", _armR.origin.y - 200);
					tween_bone.animate("rotation", deg2rad(360));
					
					//tween_bone.fadeTo(1);   
					tween_bone.onComplete = function():void { 
						trace("complete");
						armature.removeBoneByName("ship_5");

						};
						
					Starling.juggler.add(tween_bone);
					}
					break;		
			}
		}
		
		private function onRemovedFromStage():void
        {
            Starling.juggler.remove(mMovie);
        }
    }
}