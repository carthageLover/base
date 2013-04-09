package scenes
{
	import fl.motion.AnimatorFactory;
	import fl.motion.Motion;
	import fl.motion.MotionBase;
	import flash.geom.Point;
	import starling.animation.Transitions;
	import starling.animation.Tween;
	import starling.core.Starling;
    import starling.display.Image;
	import starling.display.MovieClip;
	import starling.display.Sprite;
	import starling.events.Event;
    import starling.text.TextField;
    import starling.textures.Texture;
	


    public class TextureScene extends Scene
    {
		private var mMovie:MovieClip;
		private var barco:Sprite;
		
		private var xVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,-1.275,-2.55,-3.825,-2.63333,-1.44167,-0.25,-0.1875,-0.125,-0.0625,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var yVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,8.70833,17.4167,26.125,21.7,17.275,12.85,9.6375,6.425,3.2125,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        private var scaleXVect:Array = [1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.037800,1.075600,1.113400,1.075600,1.037800,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000];
		private var scaleYVect:Array = [1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,0.950943,0.901886,0.852829,0.901886,0.950943,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000,1.000000];
		private var skewXVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.913203,1.82641,2.73961,1.82641,0.913203,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var skewYVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,-0.675233,-1.35047,-2.0257,-1.35047,-0.675233,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var rotationVect:Array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,3.2463,6.4926,9.7389,12.9852,16.2315,19.4778,14.6083,9.7389,4.86945,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		private var tween:Tween; 
		private var image2:Image;
        public function TextureScene()
        {
            // the flight textures are actually loaded from an atlas texture.
            // the "AssetManager" class wraps it away for us.
            
			barco = new Sprite();
			
			
			var image1:Image = new Image(Game.assets.getTexture("ship_1"));
            image1.x = 173;
            image1.y = 354;
            barco.addChild(image1);
            
            image2 = new Image(Game.assets.getTexture("ship_2"));
            image2.x = 5;
            image2.y = 45;
            barco.addChild(image2);
            
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
		
			tween = new Tween(image2,1/15);
			
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
			
			var i:int = 0;
			tween.moveTo( xVect[i], yVect[i]);
			tween.scaleTo(scaleXVect[i]);
			tween.scaleTo(scaleYVect[i]);
			tween.animate("skewX", skewXVect[i]);
			tween.animate("skewY", skewYVect[i]);
			tween.animate("rotation", deg2rad(rotationVect[i]));
			tween.onComplete = animalo;
			tween.onCompleteArgs = [1];
			
			
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
        }
		
		private function animalo(i:int):void 
		{
			var tween:Tween = new Tween(image2,1/15);
			/*tween.moveTo( xVect[i], yVect[i]);
			tween.scaleTo(scaleXVect[i]);
			tween.scaleTo(scaleYVect[i]);
			tween.animate("skewX", skewXVect[i]);
			tween.animate("skewY", skewYVect[i]);*/
			tween.animate("rotation", deg2rad(rotationVect[i]));
			tween.onComplete = animalo;
			tween.onCompleteArgs = [i++];
			
			
			  Starling.juggler.add(tween);
		}
		
		function deg2rad(degree) {
			return degree * (Math.PI / 180);
		}
		
        private function onAddedToStage():void
        {
            Starling.juggler.add(mMovie);
		
        }
		
		private function onRemovedFromStage():void
        {
            Starling.juggler.remove(mMovie);
        }
    }
}