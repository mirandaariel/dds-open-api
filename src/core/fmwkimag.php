<?php 

class fmwkimag
{
	public static function procImag( $aValo, $oClas )
	{
    //var_dump( $aValo );
    
    $sClav = "";                                                                // string con la clave de la instancia. En claves compuesta, separa los valores con punto
    $sEnti = $oClas->aBase['aEnti'][0];                                         // nombre de la entidad a la que pertence la instancia.
    $aImag = fmwkconf::obteImag();                                              // obtengo la configuracion de los imagenes.
    //var_dump( $aImag );
    
    foreach( $oClas->aBase['aClav'] as $iIden => $sCampClav )                   // construccion del valor de la clave de la instancia.
      $sClav .= ( ( $sClav != "" )? "." : "" ).$aValo[ $sCampClav ];
     
    if ( $sEnti == "" )                                                         // controlo que haya seteado una entidad para la instancia.
      return false;
      
    if ( ! isset( $aImag[ $sEnti ] ) )
      return false;

    foreach( $aImag[ $sEnti ] as $sCamp => $aDime )                             // se recorre el array de la configuración de las imagenes.
    {
      if ( ! isset( $aValo[ $sCamp ] ) )                                        // si se omitia el campo del archivo, se pinchaba por no encontrar B#1
        continue;
      foreach( $aDime as $sNomb => $aSize )                                     // para un campo de la entidad en particular, se recorre la configuracion de las imagenes.
      {
        $iANMX = $aSize[0];                                                     // ancho maximo
        $iALMX = $aSize[1];                                                     // alto maximo
        $sArch = $sNomb."_".$aValo[ $sCamp ];                                   // B#1 - nombre de los nuevos archivos con el prefijo que se define en la conf de imagenes
        $sAgru = substr( $sClav, -1, 1 );                                       // carpeta que agrupara segun el ultimo caracter de la instancia.
        $sRoot = $_SERVER['DOCUMENT_ROOT'];

        // 2016-03-19
        if ( FMWK_CLIE_MODU )
          $sAppR = FMWK_CLIE_ROOT . "../../";
        else
          $sAppR = FMWK_CLIE_ROOT;                                                // ruta de la aplicacion a partir del root del sitio
        
        $sCarp = $sRoot.$sAppR.'project/upld/'.$sEnti.'/'.$sAgru.'/'.$sClav.'/';
        $sDest = $sCarp.$sArch;                                                 // ruta del archivo destino
        $sRefe = $sCarp.$sCamp."_".$sArch;                                      // ruta del archivo de referencia para poder ser manipulado por el fmwk
        $sTemp = $sCarp.$aValo[ $sCamp ];                                       // ruta del archivo original
        //var_dump( $sTemp );

        list($width, $height) = getimagesize($sTemp);
        
        if ( !empty($width) && !empty($height) )                                // 20120910 - Nueva logica para el dimensionamiento de las imagenes
        {
          if ( $iANMX != 0 && $iALMX != 0 )                                     // si estan ambas dimensiones limites definidas.
          {
            $iMenr = ( $width <= $height )? $width : $height;                   // obtener el menor valor de las dimensiones de la imagen.
            $sMenr = ( $width <= $height )? "anch" : "alto";                    // definir a que dimension pertenece.
            $iLimt = ( $sMenr == "anch" )? $iANMX : $iALMX;                     // se define el limite
          }
          else                                                                  // sino, significa que el tamaño de una dimension, no importa
          {
            $iLimt = ( $iANMX != 0 )? $iANMX : $iALMX;                          // se define el limite
            $sMenr = ( $iANMX != 0 )? "anch" : "alto";                          // definir a que dimension pertenece.
            $iMenr = ( $iANMX != 0 )? $width : $height;                         // obtener el menor valor de las dimensiones de la imagen.
          }
          $dProp = $iLimt / $iMenr;                                             // se obtiene la proporcion.
          $iAnch = round( $dProp * $width );                                    // se obtiene el ancho sDest
          $iAlto = round( $dProp * $height );                                   // se obtiene el alto sDest
          $tn_width = $iAnch;                                                   // valores necesarios para la redimension
          $tn_height = $iAlto;                                                  // valores necesarios para la redimension
          if ( $iANMX != 0 && $iALMX != 0 )
          {
            $iDife = ( $sMenr == "anch" )? $iAlto - $iALMX : $iAnch - $iANMX;   // se obtiene la diferencia del lado que sobresale.
            $iSrcx = ( $sMenr == "alto" )? round( $iDife / 2 ) :  0;            // se establece a partir de que posicion y se recortara la imagen origen para la imag dest
            $iSrcy = ( $sMenr == "anch" )? round( $iDife / 2 ) :  0;            // se establece a partir de que posicion x se recortara la imagen origen para la imag dest
            $iAnch = ( $sMenr == "anch" )? $iAnch : $iANMX;                     // se determina cual es ancho que tendra la nueva imag en base a lado mas chico
            $iAlto = ( $sMenr == "alto" )? $iAlto : $iALMX;                     // se determina cual es alto que tendra la nueva imag en base a lado mas chico
          }
          ini_set('memory_limit', '128M');                                      // Se setea la memoria alta por lo que pueda consumir la generacion de la imagen
          // Se crea la nueva imagen proporcionalmente redimensionada
          
          if ( strpos( $sTemp, ".png" ) !== false )
            $src = imagecreatefrompng( $sTemp );
          else if ( strpos( $sTemp, ".gif" ) !== false )
            $src = imagecreatefromgif ( $sTemp );
          else if ( strpos( $sTemp, ".bmp" ) !== false )
            $src = imagecreatefromwbmp ( $sTemp );
          else
            $src = ImageCreateFromJpeg($sTemp);
          
          $dst = ImageCreateTrueColor($tn_width, $tn_height);
          ImageCopyResized($dst, $src, 0, 0, 0, 0, $tn_width, $tn_height, 
            $width, $height);
          // se crea la imagen final teniendo en cuenta el recorte.
          if ( $iANMX != 0 && $iALMX != 0 )
          {
            $ds2 = ImageCreateTrueColor( $iAnch, $iAlto );
            ImageCopyResized($ds2, $dst, 0, 0, $iSrcx, $iSrcy, $iAnch, $iAlto, 
              $iAnch, $iAlto);
          }
          else
          {
            $ds2 = $dst;
          }
  
          if ( strpos( $sTemp, ".png" ) !== false )
            $resultado = imagepng($ds2, $sDest);
          else if ( strpos( $sTemp, ".gif" ) !== false )
            $resultado = imagegif($ds2, $sDest);
          else if ( strpos( $sTemp, ".bmp" ) !== false )
            $resultado = imagewbmp($ds2, $sDest);
          else
            $resultado = ImageJpeg($ds2, $sDest);
          
          fclose( fopen( $sRefe, "w" ) );                                       // se crea el archivo de sReferenci
          // Se destruyen los temporarios
          ImageDestroy($src);
          ImageDestroy($dst);
          if ( $iANMX != 0 && $iALMX != 0 )
            ImageDestroy($ds2);
        }
      }
    }
	}
}