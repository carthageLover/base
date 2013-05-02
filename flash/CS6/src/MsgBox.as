package
{


     import starling.display.Sprite;
     import starling.display.Button;
     import starling.display.Image;
     import starling.text.TextField;
     import starling.textures.Texture;
	 
	 

public class MsgBox extends Sprite
{
    [Embed(source = "DuelScreenTest.jpg")]
    private static const Background:Class;
 
    // [Embed(source = "button_bg.png")]
    [Embed(source = "DuelScreenTest_btn.jpg")]
    private static const ButtonBG:Class;
 
    public function MsgBox(text:String)
    {
        var background:Image = Image.fromBitmap(new Background());
        var textField:TextField = new TextField(100, 20, text);
 
        var buttonTexture:Texture = Texture.fromBitmap(new ButtonBG());
        var yesButton:Button = new Button(buttonTexture, "yes");
        var noButton:Button  = new Button(buttonTexture, "no");
 
        yesButton.x = 10;
        yesButton.y = 20;
 
        noButton.x = 60;
        noButton.y = 20;
 
        addChild(background);
        addChild(textField);
        addChild(yesButton);
        addChild(noButton);
    }
}
}