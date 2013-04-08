package scenes
{
    import starling.display.Image;
    import starling.text.TextField;
    import starling.textures.Texture;

    public class TextureScene extends Scene
    {
        public function TextureScene()
        {
            // the flight textures are actually loaded from an atlas texture.
            // the "AssetManager" class wraps it away for us.
            
           

			
			var image1:Image = new Image(Game.assets.getTexture("ship_1"));
            image1.x = 173;
            image1.y = 354;
            addChild(image1);
            
            var image2:Image = new Image(Game.assets.getTexture("ship_2"));
            image2.x = 5;
            image2.y = 45;
            addChild(image2);
            
            var image3:Image = new Image(Game.assets.getTexture("ship_3"));
            image3.x = 150;
            image3.y = 311;
            addChild(image3);
			
			var image4:Image = new Image(Game.assets.getTexture("ship_4"));
            image4.x = 200;
            image4.y = 300;
            addChild(image4);


			
			var image6:Image = new Image(Game.assets.getTexture("ship_6"));
            image6.x = 25;
            image6.y = 336;
            addChild(image6);
			
			var image7:Image = new Image(Game.assets.getTexture("ship_7"));
            image7.x = 109;
            image7.y = 323;
            addChild(image7);			

			var image8:Image = new Image(Game.assets.getTexture("ship_8"));
            image8.x = 67;
            image8.y = 24;
            addChild(image8);   
			
			var image9:Image = new Image(Game.assets.getTexture("ship_9"));
            image9.x = 50;
            image9.y = 91;
            addChild(image9);	
			
			var image10:Image = new Image(Game.assets.getTexture("ship_10"));
            image10.x = 0;
            image10.y = 0;
            addChild(image10);			

			var image11:Image = new Image(Game.assets.getTexture("ship_11"));
            image11.x = 86;
            image11.y = 227;
            addChild(image11);	
			
			var image12:Image = new Image(Game.assets.getTexture("ship_12"));
            image12.x = 157;
            image12.y = 247;
            addChild(image12);
			
			var image13:Image = new Image(Game.assets.getTexture("ship_13"));
            image13.x = 51;
            image13.y = 293;
            addChild(image13);
			
			var image14:Image = new Image(Game.assets.getTexture("ship_14"));
            image14.x = 54;
            image14.y = 341;
            addChild(image14);
			
			var image15:Image = new Image(Game.assets.getTexture("ship_15"));
            image15.x = 96;
            image15.y = 361;
            addChild(image15);
			
			var image5:Image = new Image(Game.assets.getTexture("ship_5"));
            image5.x = 20;
            image5.y = 267;
            addChild(image5);
			
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
    }
}