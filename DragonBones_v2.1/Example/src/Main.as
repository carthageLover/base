package  {
	import flash.display.Sprite;
	import flash.events.MouseEvent;

	import starling.core.Starling;

    [SWF(width="800", height="600", frameRate="30", backgroundColor="#cccccc")]
	public class Main extends flash.display.Sprite {

		public function Main() {
			stage.addEventListener(MouseEvent.CLICK, mouseHandler);
			starlingInit();
		}

		private function mouseHandler(e:MouseEvent):void 
		{
			switch(e.type) {
				case MouseEvent.CLICK:
					StarlingGame.instance.changeMovement();
					break;
			}
		}

		private function starlingInit():void {
			var _starling:Starling = new Starling(StarlingGame, stage);
			_starling.showStats = true;
			_starling.start();
		}
	}
}

