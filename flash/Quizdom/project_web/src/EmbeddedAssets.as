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
        
       [Embed(source="../../assets/textures/1x/atlas.xml", mimeType="application/octet-stream")]
        public static const atlas_xml:Class;
        
        [Embed(source="../../assets/textures/1x/atlas.png")]
        public static const atlas:Class;

		[Embed(source="../../assets/textures/3x/shipbitmap.xml", mimeType="application/octet-stream")]
        public static const shipbitmap_xml:Class;
        
        [Embed(source="../../assets/textures/3x/shipbitmap.png")]
        public static const shipbitmap:Class;
       
		[Embed(source="../../assets/textures/5x/ship.xml", mimeType="application/octet-stream")]
        public static const ship_xml:Class;
        
        [Embed(source="../../assets/textures/5x/ship.png")]
        public static const ship:Class;

	/*	[Embed(source="../../assets/textures/6x/bandera.xml", mimeType="application/octet-stream")]
        public static const bandera_xml:Class;
        
        [Embed(source="../../assets/textures/6x/bandera.png")]
        public static const bandera:Class;
		

*/
	
	//	[Embed(source = "../../assets/textures/ship_rod/texture.png")]
	//	public static const TextureData:Class;

		
		// Compressed textures
        
     //  [Embed(source = "../../assets/textures/1x/compressed_texture.atf", mimeType="application/octet-stream")]
      // public static const compressed_texture:Class;
      

        // Bitmap Fonts
        
        [Embed(source="../../assets/fonts/1x/desyrel.fnt", mimeType="application/octet-stream")]
        public static const desyrel_fnt:Class;
        
        [Embed(source = "../../assets/fonts/1x/desyrel.png")]
        public static const desyrel:Class;
        
        // Sounds
        
        [Embed(source="../../assets/audio/wing_flap.mp3")]
        public static const wing_flap:Class;
    }
}