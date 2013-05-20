package 
{
    public class EmbeddedAssets
    {
        /** ATTENTION: Naming conventions!
         *  
         *  - Classes for embedded IMAGES should have the exact same name as the file,
         *    without extension. This is required so that references from XMLs (atlas, bitmap font)
         *    won't break.
         *    
         *  - Atlas and Font XML files can have an arbitrary name, since they are never
         *    referenced by file name.
         * 
         */
        
        // Texture Atlas
        
       [Embed(source="../assets/textures/1x/atlas.xml", mimeType="application/octet-stream")]
        public static const atlas_xml:Class;
        
        [Embed(source="../assets/textures/1x/atlas.png")]
        public static const atlas:Class;

		[Embed(source="../assets/textures/3x/shipbitmap.xml", mimeType="application/octet-stream")]
        public static const shipbitmap_xml:Class;
        
        [Embed(source="../assets/textures/3x/shipbitmap.png")]
        public static const shipbitmap:Class;
       
		[Embed(source="../assets/textures/5x/ship.xml", mimeType="application/octet-stream")]
        public static const ship_xml:Class;
        
        [Embed(source="../assets/textures/5x/ship.png")]
        public static const ship:Class;

      
		[Embed(source="../assets_system/quizdomBack2.jpg")]
		public static const quizdomBack2:Class;	  
		
		[Embed(source="../assets_system/quizdomBack3.jpg")]
		public static const quizdomBack3:Class;
		
		[Embed(source="../assets_system/boton.png")]
		public static const boton:Class;
		
		[Embed(source="../assets_system/boton.xml", mimeType="application/octet-stream")]
		public static const boton_xml:Class;

        // Bitmap Fonts
        
        [Embed(source="../assets/fonts/1x/desyrel.fnt", mimeType="application/octet-stream")]
        public static const desyrel_fnt:Class;
        
        [Embed(source = "../assets/fonts/1x/desyrel.png")]
        public static const desyrel:Class;
		
		[Embed(source = "../assets/fonts/1x/franklin.fnt", mimeType="application/octet-stream")]
        public static const franklin_fnt:Class;
        
        [Embed(source = "../assets/fonts/1x/franklin.png")]
        public static const franklin:Class;
 		
		[Embed(source = "../assets/fonts/1x/font.fnt", mimeType="application/octet-stream")]
        public static const font_fnt:Class;
        
        [Embed(source = "../assets/fonts/1x/font.png")]
        public static const font:Class;       
        // Sounds
        
        [Embed(source="../assets/audio/wing_flap.mp3")]
        public static const wing_flap:Class;
		
		[Embed(source="../assets/audio/vengo.mp3")]
        public static const vengo:Class;
		
    }
}