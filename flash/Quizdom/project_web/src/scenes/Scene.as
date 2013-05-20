package scenes
{
    import starling.display.Button;
	import starling.display.Image;
    import starling.display.Sprite;
	import starling.textures.Texture;
    
    public class Scene extends Sprite
    {
        private var mBackButton:Button;
        
        public function Scene(backgroundTexture:String = null)
        {
            // Add background to scene
			if (backgroundTexture != null){
				var bgTexture:Texture = Game.assets.getTexture(backgroundTexture);
				addChild(new Image(bgTexture));
			}
			// the main menu listens for TRIGGERED events, so we just need to add the button.
            // (the event will bubble up when it's dispatched.)
            
            mBackButton = new Button(Game.assets.getTexture("button_back"), "Back");
            mBackButton.x = Constants.CenterX - mBackButton.width / 2;
            mBackButton.y = Constants.GameHeight - mBackButton.height + 1;
            mBackButton.name = "backButton";
            addChild(mBackButton);
        }
    }
}