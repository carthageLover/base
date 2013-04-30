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
	   
	   
	   	[Embed(source = "../../assets/textures/1x/ship_rod/skeleton.xml", mimeType = "application/octet-stream")]
		public static const SkeletonXMLData:Class;
	
		[Embed(source = "../../assets/textures/1x/ship_rod/texture.xml", mimeType = "application/octet-stream")]
		public static const TextureXMLData:Class;
	
		[Embed(source = "../../assets/textures/1x/ship_rod/texture.png")]
		public static const TextureData:Class;
	   
		private var mMovie:MovieClip;
		private var barco:Sprite;
		
		protected var brawler:SpriterClip;
		protected var spriterLoader:SpriterLoader;
		
		/** Current date. */
		private var _currentDate:Date;
		
	/*	private var xVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,-1.275,-2.55,-3.825,-2.63333,-1.44167,-0.25,-0.1875,-0.125,-0.0625,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var yVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,8.70833,17.4167,26.125,21.7,17.275,12.85,9.6375,6.425,3.2125,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        private var scaleXVect:Array = [1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.037800,1.075600,1.113400,1.075600,1.037800,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000];
		private var scaleYVect:Array = [1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,0.950943,0.901886,0.852829,0.901886,0.950943,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000];
		private var skewXVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.913203,1.82641,2.73961,1.82641,0.913203,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var skewYVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,-0.675233,-1.35047,-2.0257,-1.35047,-0.675233,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var rotationVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,3.2463,6.4926,9.7389,12.9852,16.2315,19.4778,14.6083,9.7389,4.86945,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var tween:Tween; */
		private var image2:Image;
		
		private var i:int = 0;
		
		public static var instance:TextureScene;

		private var factory:StarlingFactory;
		private var armature:Armature;
		private var armatureClip:Sprite;
		
        public function TextureScene()
        {
            // the flight textures are actually loaded from an atlas texture.
            // the "AssetManager" class wraps it away for us.
            
			//this.blendMode = BlendMode.NORMAL;
			
			addEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			/***********************************************************************************************************************/
			/*
			barco = new Sprite();
						
			var image1:Image = new Image(Game.assets.getTexture("ship_1"));
            image1.x = 173;
            image1.y = 354;
            barco.addChild(image1);
            
           /* image2 = new Image(Game.assets.getTexture("ship_2"));
            image2.x = 5;
            image2.y = 45;
            barco.addChild(image2);*/
            /*
            var image3:Image = new Image(Game.assets.getTexture("ship_3"));
            image3.x = 150;
            image3.y = 311;
            barco.addChild(image3);
			
			var image4:Image = new Image(Game.assets.getTexture("ship_4"));
            image4.x = 200;
            image4.y = 300;
            barco.addChild(image4);
			
			var image6:Image = new Image(Game.assets.getTexture("ship_6"));
            image6.x = 25;
            image6.y = 336;
            barco.addChild(image6);
			
			var image7:Image = new Image(Game.assets.getTexture("ship_7"));
            image7.x = 109;
            image7.y = 323;
            barco.addChild(image7);			

			var image8:Image = new Image(Game.assets.getTexture("ship_8"));
            image8.x = 67;
            image8.y = 24;
            barco.addChild(image8);   
			
			var image9:Image = new Image(Game.assets.getTexture("ship_9"));
            image9.x = 50;
            image9.y = 91;
            barco.addChild(image9);	
			
			var image10:Image = new Image(Game.assets.getTexture("ship_10"));
            image10.x = 0;
            image10.y = 0;
            barco.addChild(image10);			

			var image11:Image = new Image(Game.assets.getTexture("ship_11"));
            image11.x = 86;
            image11.y = 227;
            barco.addChild(image11);	
			
			var image12:Image = new Image(Game.assets.getTexture("ship_12"));
            image12.x = 157;
            image12.y = 247;
            barco.addChild(image12);
			
			var image13:Image = new Image(Game.assets.getTexture("ship_13"));
            image13.x = 51;
            image13.y = 293;
            barco.addChild(image13);
			
			var image14:Image = new Image(Game.assets.getTexture("ship_14"));
            image14.x = 54;
            image14.y = 341;
            barco.addChild(image14);
			
			var image15:Image = new Image(Game.assets.getTexture("ship_15"));
            image15.x = 96;
            image15.y = 361;
            barco.addChild(image15);
			
			var image5:Image = new Image(Game.assets.getTexture("ship_5"));
            image5.x = 20;
            image5.y = 267;
            barco.addChild(image5);
			
			addChild(barco);
		
			
			
			//Create TexturePacker Atlas
			var atlas:TextureAtlas = new TextureAtlas(Texture.fromBitmap(new TexturePackerBitmap()), new XML(new TexturePackerXml()));
			//Create Animation (note that with texturePacker, you must pass the parentFolder ("orc/") into the AnimationSet
			var animation:AnimationSet = new AnimationSet(XML(new OrcScml()), 1, "bandera");
			//Create Character
			createSriterClip(animation, atlas);
			
			
			//var textureScale:Number = 1;
				
			//Use the SpriterLoader class to load individual SCML files, generate a TextureAtlas, and create AnimationSets, all at once.
			//spriterLoader = new SpriterLoader();
		//	spriterLoader.completed.addOnce(onSpriterLoaderComplete);
		//	spriterLoader.load(["../../assets/textures/bandera.scml"], textureScale);
			
			
		
			//tween = new Tween(image2,1/15);
			
			//tween.animate("rotation", deg2rad(90)); // conventional 'animate' call
           // tween.moveTo(0, 30);                 // convenience method for animating 'x' and 'y'
           // tween.scaleTo(0.5);                     // convenience method for 'scaleX' and 'scaleY'
           // tween.onComplete = function():void { mStartButton.enabled = true; };
           // tween.animate("skewX", 0.913203);
			//tween.animate("skewY", -0.675233);
			//tween.animate("rotation", 3.2);
			
		
			//trace("xvect " + xVect[0]);
			//for (var i:int = 0; i < xVect.length; i++ )
		//	{
		/*	tween.animate("x", xVect[i]);
			tween.animate("y", yVect[i]);*/
			
			/*
			tween.moveTo( xVect[i], yVect[i]);
			tween.animate("scaleX",scaleXVect[i]);
			tween.animate("scaleY",scaleYVect[i]);
			tween.animate("skewX", skewXVect[i]);
			tween.animate("skewY", skewYVect[i]);
			tween.animate("rotation", deg2rad(rotationVect[i]));
			tween.onComplete = animalo;
			//	tween.onCompleteArgs = [1];

			Starling.juggler.add(tween);
			//}
            // the tween alone is useless -- for an animation to be carried out, it has to be 
            // advance once in every frame.            
            // This is done by the 'Juggler'. It receives the tween and will carry it out.
            // We use the default juggler here, but you can create your own jugglers, as well.            
            // That way, you can group animations into logical parts.  
          
            
			
			/*
		    var frames:Vector.<Texture> = Game.assets.getTextures("bandera");
            mMovie = new MovieClip(frames, 30);
            
            // add sounds
            // var stepSound:Sound = Game.assets.getSound("wing_flap");
            //  mMovie.setFrameSound(2, stepSound);
            
            // move the clip to the center and add it to the stage
            mMovie.x = 0; // Constants.CenterX - int(mMovie.width / 2);
            mMovie.y = 0; // Constants.CenterY - int(mMovie.height / 2);
            addChild(mMovie);
            
            // like any animation, the movie needs to be added to the juggler!
            // this is the recommended way to do that.
            addEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
            addEventListener(Event.REMOVED_FROM_STAGE, onRemovedFromStage);
			*/
			
           /* try
            {
                // display a compressed texture
                var compressedTexture:Texture = Game.assets.getTexture("compressed_texture");
                var image:Image = new Image(compressedTexture);
                image.x = Constants.CenterX - image.width / 2;
                image.y = 280;
                addChild(image);
            }
            catch (e:Error)
            {
                // if it fails, it's probably not supported
                var textField:TextField = new TextField(220, 128, 
                    "Update to Flash Player 11.4 or AIR 3.4 (swf-version=17) to see a compressed " +
                    "ATF texture instead of this boring text.", "Verdana", 14);
                textField.x = Constants.CenterX - textField.width / 2;
                textField.y = 280;
                addChild(textField);
            }*/
       
		/*
		private function animalo():void 
		{
			var tween:Tween = new Tween(image2,1/30);
			tween.moveTo( xVect[i], yVect[i]);
			tween.animate("scaleX",scaleXVect[i]);
			tween.animate("scaleY",scaleYVect[i]);
			tween.animate("skewX", skewXVect[i]);
			tween.animate("skewY", skewYVect[i]);
			tween.animate("rotation", deg2rad(rotationVect[i]));
			tween.onComplete = animalo;
		//	tween.onCompleteArgs = [i++];
			trace("i:" + i.toString());	
			
			Starling.juggler.add(tween);
			i++;
		}
		*/
		}
		
		private function onEnterFrameHandler(_e:EnterFrameEvent):void {
			/*if (stage && !stage.hasEventListener(TouchEvent.TOUCH)) {
				stage.addEventListener(TouchEvent.TOUCH, onMouseMoveHandler);
			}*/
			//updateSpeed();
			//updateWeapon();
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
		
		function deg2rad(degree) {
			return degree * (Math.PI / 180);
		}
		
        private function onAddedToStage():void
        {
            instance = this;
		
			factory = new StarlingFactory();
			
			//
			var skeletonData:SkeletonData = XMLDataParser.parseSkeletonData(XML(new SkeletonXMLData()));
			factory.addSkeletonData(skeletonData);
			
			//
			var textureAtlas:StarlingTextureAtlas = new StarlingTextureAtlas(
				Texture.fromBitmapData(new TextureData().bitmapData), 
				XML(new TextureXMLData())
			);
			
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
					//tween_bone:Tween;
					trace("start x=" +_armR.origin.x );
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
						
					var tween_bone2:Tween = new Tween(_armR.origin, 1.5, Transitions.EASE_OUT);
					tween_bone2.animate("x", _armR.origin.x + 600);
					tween_bone2.animate("y", _armR.origin.y + 300);
					tween_bone2.animate("rotation", deg2rad(720));
					
					tween_bone2.delay = tween_bone.totalTime-0.8;
					
					Starling.juggler.add(tween_bone);
					Starling.juggler.add(tween_bone2);
					
					}
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