<?php
/* func_gallery.php
Gallerian funktiot
*/
function image_information($originalFile)
{
    /* Ottaa tarkistettavan tiedoston nimen ja tarkistaa sen tiedostopäätteen
    välittämättä tiedostonimestä, toimii kuville*/
    $type = getimagesize($originalFile);
    $filesize = filesize($originalFile);
   
    // Tarkistetaan tiedoston tyyppi
    if($type[2] == 1) { // GIF 
        $fileExtension = "gif";
    } elseif($type[2] == 2) { // JPEG 
        $fileExtension = "jpg";
    } elseif($type[2] == 3) { // PNG
        $fileExtension = "png";
        } else { // Tiedostomuoto ei ole tuettu, palauttaa FALSE
        $fileExtension = FALSE;
    // Funktio palauttaa arvot, jos ok
    } if($fileExtension) {
        // palauttaa type,tiedostopääte,leveys,korkeus,tiedostokoko
        return array($type[2],$fileExtension,$type[0],$type[1],$filesize);
        } else {
        // Tiedostotyyppi ei ole tuettu tai jotain häiriöö
        return array(FALSE,FALSE,FALSE,FALSE);
        }
}

function createResizedImage($originalFile,$destinationFile,$resizedWidth,$resizedHeight)
{
    /* Ottaa syötteenä vastaan (alkuperäinen tiedosto),
    (uuden kuvan hakemisto/tiedosto ilman päätettä), (uusi leveys), (uusi korkeus) */
   
    // Selvitetään kuvan koko ja tyyppi
    list($originalWidth, $originalHeight, $type) 
	= getimagesize($originalFile);
   
    // Tarkistetaan tiedoston tyyppi
    if($type == 1) { // GIF
        $originalImage = imagecreatefromgif($originalFile);
        // Läpinäkyvyys -> valkoinen
        $white = imagecolorallocate($originalImage, 255, 255, 255);
        $transparent = imagecolortransparent($originalImage, $white);
    } elseif($type == 2) { // JPEG
        $originalImage = imagecreatefromjpeg($originalFile);
    } elseif($type == 3) { // PNG
        $originalImage = imagecreatefrompng($originalFile);
    } else { // Tiedostomuoto ei ole tuettu, palauttaa FALSE
        $type = FALSE;
    }
    if($type) {
        // Lasketaan kuvalle uusi koko siten, että kuvasuhde säilyy
        $newW = $originalWidth/$resizedWidth; // Kuvasuhde: leveys
        $newH = $originalHeight/$resizedHeight; // Kuvasuhde: korkeus
        if($newW > $newH || $newW == $newH) {
            if($newW < 1) {
                /* Jos alkuperäinen kuva on pienempi kuin luotava, luodaan
                alkuperäisen kokoinen kuva */
                $newW = 1;
                }
            /* Käytetään sitä suhdetta, jolla tulee max. asetettu leveys,
            korkeus on alle max. */
            $newWidth = $originalWidth / $newW;
            $newHeight = $originalHeight / $newW;
            } elseif($newW < $newH) {
            if($newH < 1) {
                /* Jos alkuperäinen kuva on pienempi kuin luotava, luodaan
                alkuperäisen kokoinen kuva */
                $newH = 1;
                }
            /* Käytetään sitä suhdetta, jolla tulee max. asetettu korkeus,
            leveys on alle max. */
            $newWidth = $originalWidth / $newH;
            $newHeight = $originalHeight / $newH;
            }
        // Luodaan kuva, joka on määrätyn kokoinen
        $image = imagecreatetruecolor($newWidth, $newHeight);
        // Resample, luo uuden kuvan tiedostoon
        imagecopyresampled($image, $originalImage, 0, 0, 0, 0,
        $newWidth, $newHeight, $originalWidth, $originalHeight);
       
        /* Tallennetaan uusi kuva määriteltyyn tiedostoon ja annetaan
        sopiva tiedostopääte*/
        if($type == 1) { // GIF
            imagegif($image, $destinationFile);
        } elseif($type == 2) {// JPEG
            imagejpeg($image, $destinationFile);
        } elseif($type == 3) { // PNG
            imagepng($image, $destinationFile);
        }
    }
    // Poistetaan kuva muistista, ei tuhoa alkuperäistä tiedostoa!
    imagedestroy($image);
    // Palauttaa tiedostotyypin onnistuessaan, FALSE jos ei onnistu
    return $type;
}
    
    
function luo_kansio($maakansio,$galleriakansio) 
{  
   $ok=FALSE; 
   
    /* luo galleriakansio ja pura suojaukset*/
    $polku="./sisaltokuvat/".$maakansio."/".$galleriakansio;
    if(mkdir($polku, 0777))
    $ok=TRUE;
  
    /*luo alikansiot thumbs, upload ja kuvat*/
    $thumbpolku=$polku."/thumbs";
    $kuvatpolku= $polku."/kuvat";
    $uploadpolku= $polku."/upload";
    if($ok) {
        if(mkdir($thumbpolku, 0755) 
        && mkdir($kuvatpolku, 0755) 
        && mkdir($uploadpolku, 0755))
        $ok=TRUE;
    }
    suojaa_kansio($polku);
    return $ok;
}

function pura_suojaus($kansio)
{
    chmod($kansio, 0777);
}

function suojaa_kansio($kansio)
{
    chmod($kansio, 0755);
}
?>
