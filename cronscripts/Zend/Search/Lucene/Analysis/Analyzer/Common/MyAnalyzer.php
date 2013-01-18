<?php
namespace Zend\Search\Lucene\Analysis\Analyzer\Common;
use Zend\Search\Lucene\Analysis;
/**
 * Description of MyAnalyzer
 *
 * @author mehul
 */
class MyAnalyzer extends AbstractCommon
{
    private  $_position;
    private  $_positions;
    private  $_positionsi;
    private  $_tokenize;
    private  $_NoSpaceTokenFlag;
    private  $_SpaceToken;
    private  $_FullTokenFlag;
    private  $_count;
    private  $_IndividualToken;
    private  $_specialString;
    private  $i;



    public function __construct(){

        $this->_tokenize = 0;
        $this->_FullTokenFlag = 0;
        $this->_NoSpaceTokenFlag = 0;
        $this->_count = 0;
        $this->_specialString = array();
        $this->_IndividualToken = array();
        $this->i = 0;
    }

    /**
     * Reset token stream
     */
    public function reset()
    {
//        echo 'The values are being reset here';
//        echo "\n";
        $this->_position = 0;
        $this->_positions = 0;
        $this->_positionsi = 0;
        $this->_FullTokenFlag = 0;
        $this->_NoSpaceTokenFlag = 0;
        $this->_count = 0;
        
        
          if ($this->_input === null) {
            return;
        }

    }

     /**
     * Tokenization stream API
     * Get next token
     * Returns null at the end of stream
     *
     * @return Zend_Search_Lucene_Analysis_Token|null
     */
   public function nextToken()
   {
        if ($this->_input === null) {
            return null;
        }
        else {
            $this->_filterInput($this->_input);
        }

        while($this->_tokenize == 0 && 
              preg_match('/\b([^\s])+/',
              $this->_input,
              $match,
              PREG_OFFSET_CAPTURE,
              $this->_position) 
             ) {
                $str = $match[0][0];
                $pos = $match[0][1];                
                $endpos = $pos + strlen($str);
                $this->_position = $endpos;
                

      $this->_specialString = array();
      $this->_positionsi = 0;
      while(preg_match('/[a-zA-Z0-9]+/', $str, $matchesi, PREG_OFFSET_CAPTURE, $this->_positionsi))
         {
               $posix = 0;
               $this->_specialString[] = $matchesi[0][0];
               $posix =   $matchesi[0][1];
               $endposix = $posix + strlen($matchesi[0][0]);
               $this->_positionsi = $endposix;
         }
         
      if(count($this->_specialString) > 1){
                               
              $this->_setNoSpaceToken(implode($this->_specialString));
              $this->_setSpaceToken(implode(' ',  $this->_specialString));
              $this->_setIndividualToken($this->_specialString);
       }
                /*Index Rule 1 and Index Rule 2*/
                $token = $this->normalize(new Analysis\Token($str, $pos, $endpos));

//                echo "The string indexed and stored for Rule 1 or Rule 2 is :".$str;
//                echo "\n";
                return $token;
    }

    
    if($this->_tokenize == 1){

              $this->_tokenize = 2;
              $strNospace = $this->_getNoSpaceToken();
              $token = $this->normalize(new Analysis\Token($strNospace, 0, strlen($strNospace)));
//              echo 'The string indexed and stored for Rule 5 is:'.$strNospace;
//              echo "\n";
              return $token;
    }

    if($this->_tokenize == 2){

              $strSpace = $this->_getSpaceToken();
              $this->_tokenize = 3;
              $token = $this->normalize(new Analysis\Token($strSpace, 0, strlen($strSpace)));
//              echo 'The string indexed and stored for Rule 6 is:'.$strSpace;
//              echo "\n";
              return $token;
     }

     if($this->_tokenize == 3){

        $individualString = $this->_getIndividualToken();
        while($this->i < count($individualString)){
            $stri = $individualString[$this->i++];
            $token = $this->normalize(new Analysis\Token($stri, 0, strlen($stri)));
//            echo 'The string indexed and stored for Rule 4 is:'.$stri;
//            echo "\n";      
            return $token;
        }
   
            $this->_tokenize = 0;
     }
     
     $this->_count = substr_count($this->_input,' ');

     if($this->_count && !($this->_FullTokenFlag)){
     
         $posf = 0;
         $this->_FullTokenFlag = 1;
         $endposf = $posf + strlen($this->_input);
         $token = $this->normalize(new Analysis\Token($this->_input, $posf, $endposf));
//         echo 'The string indexed and stored for Rule 1 is:'.$this->_input;
//         echo "\n";
         return $token;
     }

     if($this->_count && !($this->_NoSpaceTokenFlag)){

         $posn = 0;
         $this->_NoSpaceTokenFlag = 1;
         $strn = str_replace(' ', '', $this->_input);
         $endposn = $posn + strlen($strn);
         $token = $this->normalize(new Analysis\Token($strn, $posn, $endposn));
//         echo 'The string indexed and stored for Rule 3 is:'.$strn;
//         echo "\n";
         return $token;
     }    
     
//     echo 'Calling next token...';
//     echo "\n";
       return null;
    }

    public function _setNoSpaceToken($str){

        $this->_tokenize = 1;
        $this->_NoSpaceToken = $str;
    }

    public function _getNoSpaceToken(){
        
        return $this->_NoSpaceToken;
    }

    public function _setSpaceToken($str){

        $this->_SpaceToken = $str;
    }

    public function _getSpaceToken(){

        return $this->_SpaceToken;
    }
    
    public function _setIndividualToken($str){
         $this->i = 0;
         $this->_IndividualToken = $str;
    }
    
    public function _getIndividualToken(){

        return $this->_IndividualToken;
    }

    /*This function filters the input string by elminating the accent symbols and replacing them with their
     * ASCII counterpart. 
     */
    public function _filterInput($str){        

         $search = array('á','à','å','æ','ä','â','ą','ã',
           'č','ç',
           'ď',
           'é','è','ê','ë','ě',
           'ğ',
           'í','ï','î','ì',
           'ł','ĺ','ľ',
           'ň','ń','ñ',
           'ó','ö','œ','ő','ò','ô','õ','ø',
           'ř','ŕ',
           'š','ś','ş','ș',
           'ť','ţ','ț',
           'ú','ů','ü','ù','û','ű',
           'ý','ÿ',
           'ž','ź','ż',
           'ß');

         $replace = array('a','a','a','ae','a','a','a','a',
           'c','c',
           'd',
           'e','e','e','e','e',
           'g',
           'i','i','i','i',
           'l','l','l',
           'n','n','n',
           'o','o','oe','o','o','o','o','o',
           'r','r',
           's','s','s','s',
           't','t','t',
           'u','u','u','u','u','u',
           'y','y',
           'z','z','z',
           'ss');


        $this->_input = str_replace($search, $replace, $str);

    }

}
?>
