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
	import dragonBones.animation.WorldClock;
	import dragonBones.Armature;
	import dragonBones.Bone;
	import dragonBones.factorys.StarlingFactory;
	import dragonBones.objects.SkeletonData;
	import dragonBones.objects.XMLDataParser;
	import dragonBones.textures.StarlingTextureAtlas;
	import starling.events.EnterFrameEvent;
	import starling.events.KeyboardEvent;
	
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
	import starling.textures.Texture;
	
	/**
	 * This is the welcome or main menu class for the game.
	 *  
	 * @author hsharma
	 * 
	 */


	public class Quizdom extends Sprite
	{
		
		[Embed(source = "../../../../../media/ship_rod/skeleton.xml", mimeType = "application/octet-stream")]
		public static const SkeletonXMLData:Class;
	
		[Embed(source = "../../../../../media/ship_rod/texture.xml", mimeType = "application/octet-stream")]
		public static const TextureXMLData:Class;
	
		[Embed(source = "../../../../../media/ship_rod/texture.png")]
		public static const TextureData:Class;
	
		/** Background image. */
		private var bg:Image;
		
		/** Game title. */
		private var title:Image;
		
		/** Play button. */
		private var playBtn:Button;
		
		/** About button. */
		private var aboutBtn:Button;
		
		/** Hero artwork. */
		private var hero:Image;
		
		/** Quizdom logo artwork. */
		private var quizzLogo:Image;

		/** About text field. */
		private var aboutText:TextField;
		
		/** hsharma.com button. */
		private var hsharmaBtn:Button;
		
		/** Starling Framework button. */
		private var starlingBtn:Button;
		
		/** Back button. */
		private var backBtn:Button;
		
		/** Screen mode - "welcome" or "about". */
		private var screenMode:String;

		/** Current date. */
		private var _currentDate:Date;
		
		/** Font - Regular text. */
		private var fontRegular:Font;
		
		/** Hero art tween object. */
		private var tween_hero:Tween;
		
		public static var instance:Quizdom;

		private var factory:StarlingFactory;
		private var armature:Armature;
		private var armatureClip:Sprite;
		private var textField:TextField;
		
		public function Quizdom()
		{
			super();
			this.visible = true;
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
			stage.addEventListener(KeyboardEvent.KEY_DOWN, onKeyEventHandler);
			stage.addEventListener(KeyboardEvent.KEY_UP, onKeyEventHandler);
			drawScreen();
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
					trace("101");
					armature.animation.gotoAndPlay("cannon5");
					break;	
				case 90 :	
					var _armR:Bone = armature.getBone("ship_3");
					//var _armL:Bone = armature.getBone("armInside");
					//var _movementName:String = "weapon" + (weaponID + 1);
					trace("90!!!!!!");
					armature.removeBoneByName("ship_2");
					armature.removeBoneByName("ship_3");
					armature.removeBoneByName("ship_4");
					armature.removeBoneByName("ship_5");
					armature.removeBoneByName("ship_6");
					armature.removeBoneByName("ship_7");
					armature.removeBoneByName("ship_8");
					
					//_armL.childArmature.animation.gotoAndPlay(_movementName);
					break;
			}
		}
		
		/**
		 * Draw all the screen elements. 
		 * 
		 */

		private function drawScreen():void

		{
			// GENERAL ELEMENTS
			
			bg = new Image(Assets.getTexture("quizdomBack"));
			bg.blendMode = BlendMode.NONE;
			bg.width = 1024;
			this.addChild(bg);
		
			textField = new TextField(700, 30, "Use keys 1 to 5 (numeric keyboard)", "Arial", 18, 0, true)
			textField.x = 60;
			textField.y = 5;
			this.addChild(textField);
			
			quizzLogo = new Image(Assets.getTexture("logo"));
			quizzLogo.x = 1024 - (quizzLogo.width + 20);
			quizzLogo.y = 20
			this.addChild(quizzLogo);
			
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
			armatureClip.x = 20;
			armatureClip.y = 900;
			addChild(armatureClip);
			
			WorldClock.clock.add(armature);
			addEventListener(EnterFrameEvent.ENTER_FRAME, onEnterFrameHandler);
			
			/*tween_hero = new Tween(armatureClip, 4, Transitions.EASE_OUT);
			tween_hero.animate("x", 80);
			Starling.juggler.add(tween_hero);
			*/
			this.addEventListener(Event.ENTER_FRAME, floatingAnimation);
			
			
			title = new Image(Assets.getTexture(("water")));
			title.x =0;
			title.y = 600;
			this.addChild(title);
			
			// WELCOME ELEMENTS
			
			/*hero = new Image(Assets.getAtlas().getTexture("welcome_hero"));
			hero.x = -hero.width;
			hero.y = 130;
			this.addChild(hero);
			
			playBtn = new Button(Assets.getAtlas().getTexture("welcome_playButton"));
			playBtn.x = 640;
			playBtn.y = 340;
			playBtn.addEventListener(Event.TRIGGERED, onPlayClick);
			this.addChild(playBtn);
			
			aboutBtn = new Button(Assets.getAtlas().getTexture("welcome_aboutButton"));
			aboutBtn.x = 460;
			aboutBtn.y = 460;
			aboutBtn.addEventListener(Event.TRIGGERED, onAboutClick);
			this.addChild(aboutBtn);
			*/
			// ABOUT ELEMENTS
			/*fontRegular = Fonts.getFont("Regular");
			
			aboutText = new TextField(480, 600, "", fontRegular.fontName, fontRegular.fontSize, 0xffffff);
			aboutText.text = "keys 1 to 5 to destroy";
			aboutText.x = 60;
			aboutText.y = 50;
			aboutText.hAlign = HAlign.CENTER;
			aboutText.vAlign = VAlign.TOP;
			aboutText.height = aboutText.textBounds.height + 30;
			this.addChild(aboutText);
			/*
			hsharmaBtn = new Button(Assets.getAtlas().getTexture("about_hsharmaLogo"));
			hsharmaBtn.x = aboutText.x;
			hsharmaBtn.y = aboutText.bounds.bottom;
			hsharmaBtn.addEventListener(Event.TRIGGERED, onHsharmaBtnClick);
			this.addChild(hsharmaBtn);
			
			starlingBtn = new Button(Assets.getAtlas().getTexture("about_starlingLogo"));
			starlingBtn.x = aboutText.bounds.right - starlingBtn.width;
			starlingBtn.y = aboutText.bounds.bottom;
			starlingBtn.addEventListener(Event.TRIGGERED, onStarlingBtnClick);
			this.addChild(starlingBtn);
			*/
			backBtn = new Button(Assets.getAtlas().getTexture("about_backButton"));
			backBtn.x = 1024 - (backBtn.width+ 20);
			backBtn.y = 768 - (backBtn.height+ 20);
			backBtn.addEventListener(Event.TRIGGERED, onAboutBackClick);
			this.addChild(backBtn);
		}
		

		
		private function onEnterFrameHandler(_e:EnterFrameEvent):void {
			/*if (stage && !stage.hasEventListener(TouchEvent.TOUCH)) {
				stage.addEventListener(TouchEvent.TOUCH, onMouseMoveHandler);
			}*/
			//updateSpeed();
			//updateWeapon();
			WorldClock.clock.advanceTime(-1);
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
			
			this.visible = false;
			
			// If not coming from about, restart playing background music.
		//	if (screenMode != "about")
			//{
			//	if (!Sounds.muted) Sounds.sndBgMain.play(0, 999);
		//	}
			
			screenMode = "quizz";
			
			//hero.visible = true;
		//	playBtn.visible = true;
		//	aboutBtn.visible = true;
			
			//aboutText.visible = false;
		/*	hsharmaBtn.visible = false;
			starlingBtn.visible = false;
			backBtn.visible = false;
			
			hero.x = -hero.width;
			hero.y = 100;
			
			tween_hero = new Tween(hero, 4, Transitions.EASE_OUT);
			tween_hero.animate("x", 80);
			Starling.juggler.add(tween_hero);
			
			this.addEventListener(Event.ENTER_FRAME, floatingAnimation);*/
		}
		
		/**

		 * Animate floating objects. 

		 * @param event

		 * 

		 */

		private function floatingAnimation(event:Event):void

		{
			_currentDate = new Date();
			armatureClip.y = 200 + (Math.cos(_currentDate.getTime() * 0.002)) * 15;
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