<?php
/**
 * @desc Classe for decompress ZIP File
 * @author Gerald LONLAS
 * @version 1.0
 */
class ZipExtract
{

    /* ATTRIBUTS
    ---------------------------------*/
    var $ressource;     // Zip Ressource; 
    var $pathZipFile;   // Stock path to the Zip file.
    var $listFiles = array();     // List of files in Zip file.

    /**
    * @desc Use to force change output name file
    */
    var $isChangeNameFile;

    /**
    * @desc  Use to know where the class will decompress the zip file.
    */
    var $outputDestination;    

    
    
    /* METHODS
    ---------------------------------*/ 
    /**
    * @desc Constructor, initialize $isDelete and $outputDestination
    */
    function ZipExtract()
    {
        $this->isChangeNameFile = false; 
        $this->outputDestination = './';  
    }
    
    /**
    * @desc     This method try to open Zip file.
    * @param    String : Path to ZIP File
    * @return   Bool : True : File found and Open, False : Problem with the file (Not found ou not Zip file)
    * @since    1.0
    * @version  1.0 
    */
    public function OpenZipFile( $filename )
    {
    	// Check that the zlib is available
		if(extension_loaded('zlib')) {
			
		
        $this->ressource = zip_open( $filename );   // Return 11 if error on Open file
		
	        if ( $this->ressource != 11 )
	        {
	            $this->pathZipFile = $filename;
	            
	            return true;
	        }
	        else
	        {
	            return false;
	        }
        }
        else 
        	return false;
    }
    
    
    /**
    * @desc     This method descompress Zip file.
    * @since    1.0
    * @version  1.0 
    */
    public function Extract()
    {	
		// Loop for every files in ZIP
        while( $entry = zip_read( $this->ressource ) )
        {

            // Check if the size of Zip is up to 0 Ko
            if( zip_entry_filesize($entry) > 0 )
            {

                // Full name (Path + name) of the current file
                if( $this->isChangeNameFile )
                { $fileName = $this->ChangeFileName( zip_entry_name( $entry ) ); }
                else
                { $fileName =  zip_entry_name( $entry ); }
                
                
                $fullFileName = $this->outputDestination . $fileName; //Nom et chemin de destination
                
                // Add the current file to the array
                array_push( $this->listFiles, $fileName );
                

                // Full path to the file
                $fullPath = $this->outputDestination . dirname( zip_entry_name( $entry ) );

                
                
                // Make folder if not exist
                if( !file_exists( $fullPath ) )
                {
                    $path = '';
                    foreach( explode( '/',$fullPath ) as $subFolder )
                    {
                        $path .= $subFolder.'/';

                        if( !file_exists( $path ) )
                        { mkdir( $path, 0755 ); }
                    }
                }

                
                // Extract files on new directory
                if ( zip_entry_open( $this->ressource, $entry, "r" ) )
                {	
					$fd = fopen( $fullFileName, 'w' );

                    fwrite( $fd, zip_entry_read( $entry, zip_entry_filesize( $entry ) ) );

                    fclose( $fd );
                    zip_entry_close( $entry );
                }
            }
        }

        // Close Zip file
        zip_close( $this->ressource );  
    }
    
    /**
    * @desc     Return array list of files extract on your server
    * @return   String Array : files extracted
    * @since    1.0
    * @version  1.0 
    */
    public function ListFiles()
    {
        return $this->listFiles;
    }
    
    /**
    * @desc     Use delete the Zip file
    * @return   Bool : True, Zip file is deleted; False : Problem when we try to delete the Zip file
    * @since    1.0
    * @version  1.0 
    */
    public function DeleteZipFile()
    {
        return @unlink( $this->pathZipFile ); 
    }
    
    
    /**
    * @desc     Use to change name of file will extract
    * @return   String : New name of file
    * @since    1.0
    * @version  1.0 
    */
    function ChangeFileName( $str )
    {	
		$str = strtr( $str,"�����������������������������������������������������","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn" );
        $str = strtolower( $str );
        $str = ereg_replace( '[^a-zA-Z0-9./]', '-', $str );
		
        return $str;
    }
    
    

    
     /* GETTERS AND SETTERS
    ---------------------------------*/ 
    
    /**
    * @desc     Getter for $isChangeNameFile
    * @return   Bool : True, name of files will be change; False, don't change name
    * @since    1.0
    * @version  1.0 
    */
    public function GetIsChangeNameFile()
    {
        return $this->isChangeNameFile;
    }
    
    /**
    * @desc     Setter for $isChangeNameFile 
    * @param    Bool : True, name of files will be change; False, don't change name  
    * @since    1.0
    * @version  1.0 
    */
    public function SetIsChangeNameFile($var)
    {
        $this->isChangeNameFile = $var;
    }
    
    /**
    * @desc     Getter for $outputDestination
    * @param    String : path where files will be extract   
    * @since    1.0
    * @version  1.0 
    */
    public function GetOutputDestination()
    {
        return $this->outputDestination;
    }
    
    /**
    * @desc     Setter for $outputDestination 
    * @param    String : path where files will be extract  
    * @since    1.0
    * @version  1.0 
    */
    public function SetOutputDestination($var)
    {
        $this->outputDestination = $var;
    }
}

?>
