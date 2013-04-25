/**
 *
 * Hungry Hero Game
 * http://www.hungryherogame.com
 * 
 * Copyright (c) 2012 Hemanth Sharma (www.hsharma.com). All rights reserved.
 * 
 * This ActionScript source code is free.
 * You can redistribute and/or modify it in accordance with the
 * terms of the accompanying Simplified BSD License Agreement.
 *  
 */

package com.hsharma.hungryHero.screens
{
	import com.hsharma.hungryHero.customObjects.Font;
	import com.hsharma.hungryHero.events.NavigationEvent;
	import starling.display.MovieClip;
	
	import flash.media.SoundMixer;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	
	import starling.animation.Transitions;
	import starling.animation.Tween;
	import starling.core.Starling;
	import starling.display.BlendMode;
	import starling.display.Button;
	import starling.display.Image;
	import starling.display.Sprite;
	import starling.events.Event;
	import starling.text.TextField;
	import starling.utils.HAlign;
	import starling.utils.VAlign;
	
	import feathers.controls.Button;
	
	/**
	 * This is the welcome or main menu class for the game.
	 *  
	 * @author hsharma
	 * 
	 */
	public class Welcome extends Sprite
	{
		/** Background image. */
		private var bg:Image;
		
		/** Game title. */
		private var title:Image;
		
		/**
		 * The Feathers Button control that we'll be creating.
		 */
		private var fButton:feathers.controls.Button;
		private var mcHoverState:MovieClip;
		
		
		/** Play button. */
		private var playBtn:starling.display.Button;
		
		/** Quiz button. */
		private var playBtn1:starling.display.Button;
		
		
		/** About button. */
		private var aboutBtn:starling.display.Button;
		
		/** Hero artwork. */
		private var hero:Image;

		/** About text field. */
		private var aboutText:TextField;
		
		/** hsharma.com button. */
		private var hsharmaBtn:starling.display.Button;
		
		/** Starling Framework button. */
		private var starlingBtn:starling.display.Button;
		
		/** Back button. */
		private var backBtn:starling.display.Button;
		
		/** Screen mode - "welcome" or "about". */
		private var screenMode:String;

		/** Current date. */
		private var _currentDate:Date;
		
		/** Font - Regular text. */
		private var fontRegular:Font;
		
		/** Hero art tween object. */
		private var tween_hero:Tween;
		
		public function Welcome()
		{
			super();
			this.visible = false;
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
			
			drawScreen();
		}
		
		/**
		 * Draw all the screen elements. 
		 * 
		 */
		private function drawScreen():void
		{
			// GENERAL ELEMENTS
			
			bg = new Image(Assets.getTexture("BgWelcome"));
			bg.blendMode = BlendMode.NONE;
			this.addChild(bg);
			/*
			title = new Image(Assets.getTexture(("logo")));
			title.x = 600;
			title.y = 65;
			this.addChild(title);
			*/
			// WELCOME ELEMENTS
			
			hero = new Image(Assets.getTexture("pirate"));
			hero.x = -hero.width;
			hero.y = 130;
			this.addChild(hero);
			/*
			playBtn = new starling.display.Button(Assets.getAtlas().getTexture("welcome_playButton"));
			playBtn.x = 640;
			playBtn.y = 340;
			playBtn.addEventListener(Event.TRIGGERED, onPlayClick);
			this.addChild(playBtn);
			
			aboutBtn = new starling.display.Button(Assets.getAtlas().getTexture("welcome_aboutButton"));
			aboutBtn.x = 460;
			aboutBtn.y = 460;
			aboutBtn.addEventListener(Event.TRIGGERED, onAboutClick);
			this.addChild(aboutBtn);
			*/
			
			playBtn1 = new starling.display.Button(Assets.getAtlas().getTexture("welcome_playButton"));
			playBtn1.x = 690;
			playBtn1.y = 340;
			playBtn1.addEventListener(Event.TRIGGERED, onPlay1Click);
			this.addChild(playBtn1);
			
			
			// ABOUT ELEMENTS
			fontRegular = Fonts.getFont("Regular");
			
			aboutText = new TextField(480, 600, "", fontRegular.fontName, fontRegular.fontSize, 0xffffff);
			aboutText.text = "Hungry Hero is a free and open source game built on Adobe Flash using Starling Framework.\n\nhttp://www.hungryherogame.com\n\n" +
				" The concept is very simple. The hero is pretty much always hungry and you need to feed him with food." +
				" You score when your Hero eats food.\n\nThere are different obstacles that fly in with a \"Look out!\"" +
				" caution before they appear. Avoid them at all costs. You only have 5 lives. Try to score as much as possible and also" +
				" try to travel the longest distance.";
			aboutText.x = 60;
			aboutText.y = 230;
			aboutText.hAlign = HAlign.CENTER;
			aboutText.vAlign = VAlign.TOP;
			aboutText.height = aboutText.textBounds.height + 30;
			this.addChild(aboutText);
			
			hsharmaBtn = new starling.display.Button(Assets.getAtlas().getTexture("about_hsharmaLogo"));
			hsharmaBtn.x = aboutText.x;
			hsharmaBtn.y = aboutText.bounds.bottom;
			hsharmaBtn.addEventListener(Event.TRIGGERED, onHsharmaBtnClick);
			this.addChild(hsharmaBtn);
			
			starlingBtn = new starling.display.Button(Assets.getAtlas().getTexture("about_starlingLogo"));
			starlingBtn.x = aboutText.bounds.right - starlingBtn.width;
			starlingBtn.y = aboutText.bounds.bottom;
			starlingBtn.addEventListener(Event.TRIGGERED, onStarlingBtnClick);
			this.addChild(starlingBtn);
			
			backBtn = new starling.display.Button(Assets.getAtlas().getTexture("about_backButton"));
			backBtn.x = 660;
			backBtn.y = 350;
			backBtn.addEventListener(Event.TRIGGERED, onAboutBackClick);
			this.addChild(backBtn);
			/*
			// FEATHERS BUTTON
			//create a button and give it some text to display.
			fButton = new feathers.controls.Button();
			//fButton.label = "Click Me";			
			fButton.stateToSkinFunction = null;
			fButton.defaultSkin = new Image(Assets.getAtlas().getTexture(("welcome_playButton")));
			fButton.downSkin = new Image(Assets.getAtlas().getTexture(("welcome_aboutButton")));
			mcHoverState = new MovieClip(Assets.getAtlas().getTextures("soundOn"), 3);
			Starling.juggler.add(mcHoverState);
			this.addChild(mcHoverState);			
			fButton.hoverSkin = mcHoverState;
			fButton.validate();
			fButton.x = 500;// (this.stage.stageWidth - this.fButton.width) / 2;
			fButton.y = 500;// (this.stage.stageHeight - this.fButton.height) / 2;			
			this.addChild( this.fButton );	*/		
		}
		
		private function onPlay1Click(e:Event):void 
		{
			this.dispatchEvent(new NavigationEvent(NavigationEvent.CHANGE_SCREEN, {id: "quiz"}, true));
			
			if (!Sounds.muted) Sounds.sndCoffee.play();
		}
		
		/**
		 * On back button click from about screen. 
		 * @param event
		 * 
		 */
		private function onAboutBackClick(event:Event):void
		{
			if (!Sounds.muted) Sounds.sndCoffee.play();
			
			initialize();
		}
		
		/**
		 * On credits click on hsharma.com image. 
		 * @param event
		 * 
		 */
		private function onHsharmaBtnClick(event:Event):void
		{
			navigateToURL(new URLRequest("http://www.hsharma.com/"), "_blank");
		}
		
		/**
		 * On credits click on Starling Framework image. 
		 * @param event
		 * 
		 */
		private function onStarlingBtnClick(event:Event):void
		{
			navigateToURL(new URLRequest("http://www.gamua.com/starling"), "_blank");
		}
		
		/**
		 * On play button click. 
		 * @param event
		 * 
		 */
		private function onPlayClick(event:Event):void
		{
			this.dispatchEvent(new NavigationEvent(NavigationEvent.CHANGE_SCREEN, {id: "play"}, true));
			
			if (!Sounds.muted) Sounds.sndCoffee.play();
		}
		
		
		/**
		 * On about button click. 
		 * @param event
		 * 
		 */
		private function onAboutClick(event:Event):void
		{
			if (!Sounds.muted) Sounds.sndMushroom.play();
			showAbout();
		}
		
		/**

		 * Show about screen. 

		 * 

		 */

		public function showAbout():void

		{

			screenMode = "about";
			
			hero.visible = false;
			playBtn.visible = false;
			aboutBtn.visible = false;
			
			aboutText.visible = true;
			hsharmaBtn.visible = true;
			starlingBtn.visible = true;
			backBtn.visible = true;

		}
		
		/**
		 * Initialize welcome screen. 
		 * 
		 */
		public function initialize():void
		{
			disposeTemporarily();
			
			this.visible = true;
			
			// If not coming from about, restart playing background music.
			//if (screenMode != "about")
			//{
		//		if (!Sounds.muted) Sounds.sndBgMain.play(0, 999);
		//	}
			
			screenMode = "welcome";
			
			//fButton.visible = false;
			
			hero.visible = true;
			//playBtn.visible = true;
			//aboutBtn.visible = true;
			
			aboutText.visible = false;
			hsharmaBtn.visible = false;
			starlingBtn.visible = false;
			backBtn.visible = false;
			
			hero.x = -hero.width;
			hero.y = 200;
			
			tween_hero = new Tween(hero, 4, Transitions.EASE_OUT);
			tween_hero.animate("x", 80);
			Starling.juggler.add(tween_hero);
			
			//this.addEventListener(Event.ENTER_FRAME, floatingAnimation);
		}
		
		/**

		 * Animate floating objects. 

		 * @param event

		 * 

		 */

		private function floatingAnimation(event:Event):void

		{
			_currentDate = new Date();

			hero.y = 130 + (Math.cos(_currentDate.getTime() * 0.002)) * 25;
		//	playBtn.y = 340 + (Math.cos(_currentDate.getTime() * 0.002)) * 10;
		//	aboutBtn.y = 460 + (Math.cos(_currentDate.getTime() * 0.002)) * 10;
		}
		
		/**
		 * Dispose objects temporarily. 
		 * 
		 */
		public function disposeTemporarily():void
		{
			this.visible = false;
			
			if (this.hasEventListener(Event.ENTER_FRAME)) this.removeEventListener(Event.ENTER_FRAME, floatingAnimation);
			
			if (screenMode != "about") SoundMixer.stopAll();
		}
	}
}