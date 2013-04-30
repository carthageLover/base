/**
 *  
 */

package com.hsharma.hungryHero.ui
{
	import flash.display.BitmapData;
	import flash.geom.Rectangle;
	import starling.display.DisplayObject;
	import starling.events.Touch;
	import starling.events.TouchEvent;
	import starling.events.TouchPhase;
	
	import starling.core.Starling;
	import starling.display.Button;
	import starling.display.Image;
	import starling.display.MovieClip;
	import starling.events.Event;
	import starling.textures.Texture;
	
	/**
	 * This class is the sound/mute button.
	 *  
	 * @author hsharma
	 * 
	 */
	
	public class AnimButton extends Button
	{
		static public const MAX_DRAG_DIST:Number = 50; // 50?
		
		/** Animation shown when sound is playing.  */
		private var mcUnmuteState:MovieClip;
		
		private var mcUpState:MovieClip;
		private var mcHoverState:MovieClip;
		private var mcDownState:MovieClip;
		private var mcOffState:MovieClip;		
		private var mcOutState:MovieClip;		
		
		
		/** Image shown when the sound is muted. */
		private var imageMuteState:Image;
		
		public function AnimButton()
		{
			super(Texture.fromBitmapData(new BitmapData(Assets.getBotonAtlas().getTexture("BOTONASOon0000").width, Assets.getBotonAtlas().getTexture("BOTONASOon0000").height, true, 0)));
			
			this.addEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
		}
		
		/**
		 * On added to stage. 
		 * @param event
		 * 
		 */
		private function onAddedToStage(event:Event):void
		{
			this.removeEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
			
			
			
			setButtonTextures();
			//showUnmuteState();
			showUpState();
		}
		
		/**
		 * Set textures for button states. 
		 * 
		 */
		private function setButtonTextures():void
		{
			// Normal state
			//mcUnmuteState = new MovieClip(Assets.getAtlas().getTextures("soundOn"), 3);
			//Starling.juggler.add(mcUnmuteState);
			//this.addChild(mcUnmuteState);
			
			// Selected state
			//imageMuteState = new Image(Assets.getAtlas().getTexture("soundOff"));
			//this.addChild(imageMuteState);
			
			
			mcHoverState = new MovieClip(Assets.getBotonAtlas().getTextures("BOTONASOover"),24);
			mcHoverState.loop = false;
			Starling.juggler.add(mcHoverState);
			this.addChild(mcHoverState);			

			mcUpState = new MovieClip(Assets.getBotonAtlas().getTextures("BOTONASOon"));
			mcUpState.loop = false;
			Starling.juggler.add(mcUpState);
			this.addChild(mcUpState);			
			
			mcDownState = new MovieClip(Assets.getBotonAtlas().getTextures("BOTONASOdown"),24);
			mcDownState.loop = false;
			Starling.juggler.add(mcDownState);
			this.addChild(mcDownState);			
			
			mcOffState = new MovieClip(Assets.getBotonAtlas().getTextures("BOTONASOoff"));
			mcUpState.loop = false;
			Starling.juggler.add(mcOffState);
			this.addChild(mcOffState);
			
			mcOutState = new MovieClip(Assets.getBotonAtlas().getTextures("BOTONASOout"), 24);
			mcOutState.loop = false;
			Starling.juggler.add(mcOutState);
			this.addChild(mcOutState);
			
			
			addEventListener(TouchEvent.TOUCH, onTouch2);
		}

		public function showHoverState():void
		{
			if (!mcHoverState.visible) 
			{
				mcUpState.visible = false;
				mcHoverState.visible = true;
				mcDownState.visible = false;
				mcOffState.visible = false;
				mcOutState.visible = false;
				mcHoverState.currentFrame = 0;
				mcHoverState.play();
			}
			
//			if (!mcHoverState.isPlaying || !mcHoverState.isComplete) 
//			{
//				mcHoverState.currentFrame = 0;
//				mcHoverState.play();
//			}
		}
		
		public function showOutState():void
		{
			if (mcHoverState.visible) 
			{
				mcHoverState.visible = false;
				mcDownState.visible = false;
				mcOffState.visible = false;
				mcUpState.visible = false;
				mcOutState.visible = true;
				mcOutState.currentFrame = 0;
				mcOutState.play();
				// this mc should end equal to mcUpState
			}else {
				//showUpState();
			}
		}
		
		public function showUpState():void
		{			
			mcUpState.visible = true;
			mcHoverState.visible = false;
			mcDownState.visible = false;
			mcOffState.visible = false;
			mcOutState.visible = false;
			
			mcUpState.currentFrame = 0;
			mcUpState.play();
		}		
		
		public function showDownState():void
		{
			if (!mcDownState.visible) 
			{
				mcUpState.visible = false;
				mcHoverState.visible = false;
				mcDownState.visible = true;
				mcOffState.visible = false;
				mcOutState.visible = false;
				mcDownState.currentFrame = 0;
				mcDownState.play();
			}
		}		
		
		/**
		 * Show Off State - Show the mute symbol (sound is muted). 
		 * 
		 */
		public function showUnmuteState():void
		{
			mcUnmuteState.visible = true;
			imageMuteState.visible = false;
		}
		
		/**
		 * Show On State - Show the unmute animation (sound is playing). 
		 * 
		 */
		public function showMuteState():void
		{
			mcUnmuteState.visible = false;
			imageMuteState.visible = true;
		}
		
		//override protected function onTouch(event:TouchEvent):void
		private function onTouch2(event:TouchEvent):void
        {
			/**
				MouseEvent.MOUSE_DOWN -> TouchPhase.BEGAN
				MouseEvent.MOUSE_UP -> TouchPhase.ENDED
				MouseEvent.MOUSE_MOVE -> TouchPhase.MOVED (btn pressed) or TouchPhase.HOVER (btn unpressed)
				One global mouse/touch listener handle events from various objects. In this listener you need:
					1. to check touchEvent.target to filter object
					2. to get Touch object form event: touchEvent.getTouch(this) and
					3. filter touch.phase to indicate necessary mouse event
				Also it is possible to use touchEvent.getTouch(this, [touch phase]) or 
				touchEvent.getTouches(this, [touch phase]) to get touches filtered by phase.				
			 */
			
			//	var touch:Touch = event.getTouch(this);
			//  var outTouch:Touch = event.getTouch(event.target as DisplayObject, TouchPhase.HOVER);
			
			var touch:Touch = event.getTouch(this, TouchPhase.BEGAN);
			if (touch) { showDownState(); return; }
			
			touch = event.getTouch(this, TouchPhase.ENDED);
			if (touch) { 
				// see if mouse is over the btn or out: over use showHoverState, else showUpstate.
				
                var buttonRect:Rectangle = getBounds(stage);
                buttonRect.inflate(MAX_DRAG_DIST, MAX_DRAG_DIST);
                if (!buttonRect.contains(touch.globalX, touch.globalY))
					showUpState();
				else
					showHoverState();
				
				/*if (touch.tapCount > 0) 
					showHoverState();
				else
					showUpState();*/
				
				return;
				}
			
            // to see when you're hovering over the object w/o a button pressed.
            touch = event.getTouch(this, TouchPhase.HOVER);            
            touch ? showHoverState() : showOutState();
            //alpha = touch ? 0.8 : 1.0;			
			//trace("touch en sound");
		}
	}
}