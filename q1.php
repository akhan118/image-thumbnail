
<?php



$f = "text.txt";

// read into string
 $str = file_get_contents($f,true);
// count words
echo "Total word count: ". $numWords = str_word_count($str);
echo "</br>";
echo "Unique words:".$count= count(array_unique(str_word_count($str, 1)));
echo "</br>";

echo "Sentences:".$uwords=preg_match_all('/[^\s](\.|\!|\?)(?!\w)/',$str,$match);

?>