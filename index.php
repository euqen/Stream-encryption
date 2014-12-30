<?php

 #message - text for encoding;
 #cipher - encoded text;
 #messageInBytes - byte-code of each character of $message; 

function swapBytes(&$rest) {
 for ($i = 0; $i <= floor((strlen($rest) - 1) / 2); $i++) {
  $dump = $rest{$i};
  $rest{$i} = $rest{strlen($rest) - 1 - $i};
  $rest{strlen($rest) - 1 - $i} = $dump;
 }
}

function convert_into_2_system($symbolsCode) {
 define("BASE", "2");

 while($symbolsCode >= BASE){
  $rest .= $symbolsCode % BASE;
  $symbolsCode = floor($symbolsCode / BASE); 
 }  
 
 $rest .= $symbolsCode % BASE;
 swapBytes($rest); 
 
 return $rest;
}

function convert_into_10_system($symbolsCode) {
 $rank = 1;
 for ($i = strlen($symbolsCode) - 1; $i >= 0; $i--) {
  $ANSICode = $ANSICode + ($symbolsCode{$i} * $rank);
  $rank *= 2;
 }
 
 return $ANSICode;
}

function getMessageBytes(&$message) {
 define("BYTE", "8");
 
 $i = 0;
 while ($i <= strlen($message) - 1) {   
  $symbolsCode = ord($message{$i}); 
  $symbolsCode = convert_into_2_system($symbolsCode);
   
  while (strlen($symbolsCode) < BYTE) $symbolsCode = '0'.$symbolsCode;
   
  $bytes .= $symbolsCode;
  $i++; 
 }
    
 return $bytes;
}

function LFSR($init, $length, $size, $polynom) {
 global $key;
     
 $key = $init{0};
 for ($i = 0; $i <= $length - 1; $i++) {
   
  $newBit = (int)$init{$size - $polynom[0]} ^ (int)$init{$size - $polynom[1]}; 
  //for ($j = 2; $j <= count($polynom); $j++) $newBit ^= (int)$init{$size - $polynom[$j]};
  if ($i != 0) {
   $key .= $init{0}; 
  }
  
  $init = substr($init, 1); //shift left
  $init .= $newBit; // paste new bit
  
 
 }
 
 return $key;  
}

function encode(&$message, $encryptType) {
 global $initial;
 switch($encryptType) {
  case 0:  
   $key = LFSR($initial, strlen($message) - 1, 23, array(23, 5));  
   break;
  case 1:
   $key = geffe($_POST['first1'], $_POST['second1'], $_POST['third1'], $message); 
   break;
  case 2:
   $key = RC4($_POST['RC4KEY'], strlen($message) - 1); 
   break;
 }
 
 for($i = 0; $i <= strlen($message) - 1; $i++) {   
  if ($encryptType == 2) {
   $cell = (int)ord($message{$i}) ^ (int)$key[$i]; 
   $cipher .= chr($cell);
  }
  else  {
   $cell = (int)$message{$i} ^ (int)$key[$i];
   $cipher .= (string)$cell;
  }
 }
 
 return $cipher;
}

function decode(&$cipher, $decryptType) {
 global $initial;
 
  switch($decryptType) {
  case 0:  
   $key = LFSR($initial, strlen($cipher) - 1, 23, array(23, 5));
   break;
  case 1:
   $key = geffe($_POST['first1'], $_POST['second1'], $_POST['third1'], $cipher); 
   break;
  case 2:
   $key = RC4($_POST['RC4KEY'], strlen($cipher) - 1); 
   break;
 }
 
 for($i = 0; $i <= strlen($cipher) - 1; $i++) {   
  if ($decryptType == 2) {
   $cell = (int)ord($cipher{$i}) ^ (int)$key[$i];
   $message .= chr($cell);
  }
  else {
   $cell = (int)$cipher{$i} ^ (int)$key[$i];
   $message .= (string)$cell;
  }
 }
 
 return $message;
}

function getANSICharacters($cipher) {
 define("BYTE", "8");
  
 for ($i = 0; $i <= strlen($cipher); $i++){
  if ($counter == BYTE) {
   $charCode = convert_into_10_system($code);
   $cipherMessage .= chr($charCode);
   unset($code); unset($counter);
   $i--;
  }
  else {
   $code .= $cipher{$i};
   $counter++;
  }
 }
  
 return $cipherMessage;
}

function getMessageFromFile(&$message) {
    
 $fp = fopen("message.txt", "r");
 while(!feof($fp)) $message .= fgets($fp); 
    
}

function geffe($initial1, $initial2, $initial3, $message) {
 global $key1, $key2, $key3;

 $key1 = LFSR($initial1, strlen($message) - 1, 23, array(23, 5));
 $key2 = LFSR($initial2, strlen($message) - 1, 31, array(31, 3));
 $key3 = LFSR($initial3, strlen($message) - 1, 39, array(39, 4));
  
 for($i = 0; $i <= strlen($key1) - 1; $i++) {
  $key .= ((int)$key1{$i} & (int)$key2{$i}) | ((int)!$key1{$i} & (int)$key3{$i});
 }
  
 return $key;
}

function swap(&$elem1, &$elem2) {
    
 $dump = $elem1;
 $elem1 = $elem2;
 $elem2 = $dump;
 unset($dump);
 
}

function readFileBytes($fileName) {
 
 $handle = fopen($fileName, "rb");
 $content = fread($handle, filesize($fileName));
 fclose($handle);

 return $content;  
}

function writeFileBytes($message, $fileName) {
    
 $handle = fopen($fileName.".bin", "wb");
 $content = fwrite($handle, $message);
 fclose($handle);   

}

function RC4($rawKey, $length) {   
 define("MAX_VALUE", "255");
 global $key;
 
 preg_match_all('/([0-9]+)/', $rawKey, $matches);
 $key = array(); for($i = 0; $i <= MAX_VALUE; $i++) $S[$i] = $i;
 
 $j = 0;
 for ($i = 0; $i <= MAX_VALUE; $i++) { 
  $j = ($j + $S[$i] + $matches[0][$i % count($matches[0])]) % MAX_VALUE;
  swap($S[$i], $S[$j]);
 }
 
 $i = 0; $j = 0;  
 for ($k = 0; $k <= $length; $k++) {
  $i = ($i + 1) % MAX_VALUE;
  $j = ($j + $S[$i]) % MAX_VALUE;
  swap($S[$i], $S[$j]);
  array_push($key, $S[($S[$i] + $S[$j]) % MAX_VALUE]);
 }

 return $key;
}

if (isset($_POST['submit'])) {
 if (!empty($_POST['first1']) && isset($_POST['LSFR1'])) {
  $initial = $_POST['first1'];
  getMessageFromFile($message);
  $messageInBytes = getMessageBytes($message);
  $cipher = encode($messageInBytes, 0);
  $decoded = decode($cipher, 0);
 }   
 else if(!empty($_POST['first1']) && !empty($_POST['second1']) && !empty($_POST['third1']) && isset($_POST['geffe'])) {
  getMessageFromFile($message);
  $messageInBytes = getMessageBytes($message);
  $cipher = encode($messageInBytes, 1);  
  $decoded = decode($cipher, 1);
 }
 else if (isset($_POST['RC4']) && !empty($_POST["RC4KEY"]) && !empty($_POST['fileName'])) {
   $message = readFileBytes($_POST['fileName']);
   $cipher = encode($message, 2);
   $decoded = decode($cipher, 2);
   writeFileBytes($cipher, "cipheredMessage");
   writeFileBytes($decoded, "decodedMessage");
 }
}

?>
<html>
<body style="margin:  0 auto; width: 100%; height: 100%;margin-left:50px;margin-top:50px;">
<form method="POST">
<div style="float:left; margin-right: 25px;">
<legend><input type="radio" name="LSFR1" checked="on" />LSFR1</legend>
<legend><input type="radio" name="geffe" />GEFFE</legend>
<legend><input type="radio" name="RC4" />RC4</legend>
</div>
<div style="float:left; margin-right: 25px;">
<input style="display: block; width:500px;" maxlength="23" name="first1" placeholder="LSFR1" />

<input style="display: block; width:500px;" maxlength="31" name="second1" placeholder="LSFR2" disabled="disabled" />

<input style="display: block; width:500px;" maxlength="39" name="third1" placeholder="LSFR3" disabled="disabled" />
</div>
<input style="display: block; width:500px;" maxlength="23" name="output1" value="<?php if(isset($_POST['geffe'])) echo $key1; else echo $key; ?>" placeholder="OUTPUT LSFR1" disabled="disabled" />
<input style="display: block; width:500px;" maxlength="31" name="output2" value="<?php echo $key2; ?>"placeholder="OUTPUT LSFR2" disabled="disabled" />
<input style="display: block; width:500px;" maxlength="39" name="output3" value="<?php echo $key3; ?>" placeholder="OUTPUT LSFR3" disabled="disabled" />

<br />
<input  style="display: block; width:1205px;" maxlength="23" name="gkey" placeholder="GENERATED KEY" value="<?php if(is_array($key)) { for($i = 0; $i <= count($key) - 1; $i++) $out .= $key[$i].' '; echo $out; } else echo $key; ?>" />
<br />
<textarea disabled="disabled" style="width: 290px;height:250px;">Cipher in binare form:<?php echo "\n\r"; echo $cipher; ?></textarea>
<textarea disabled="disabled" style="width: 290px;height:250px;">Cipher in ASCII form: <?php echo "\n\r"; echo getANSICharacters($cipher); ?></textarea>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<textarea disabled="disabled" style="width: 290px;height:250px;">decoded message in binare form: <?php echo "\n\r"; echo $decoded; ?></textarea>
<textarea disabled="disabled" style="width: 290px;height:250px;">decoded original message: <?php echo "\n\r"; echo getANSICharacters($decoded); ?></textarea>
<br /><br />
<input  style="width:905px;" name="RC4KEY" placeholder="RC4 KEY" disabled="disabled" />
<input  style="width:300px;border:  1px solid #A9A9A9;" name="fileName" type="file" disabled="disabled" />
<br /><br />
<input type="submit" name="submit" style="display:block;" />

</form>

</body>
<script src="jQuery.js"></script>
<script src="form.js"></script>
</html>