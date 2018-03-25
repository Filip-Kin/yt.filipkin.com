<?php
  function nicenumber($n) {
      // first strip any formatting;
      $n = (0+str_replace(",","",$n));

      // is this a number?
      if(!is_numeric($n)) return false;

      // now filter it;
      if($n>1000000000) return round(($n/1000000000),1).'B';
      else if($n>1000000) return round(($n/1000000),1).'M';
      else if($n>1000) return round(($n/1000),1).'K';

      return number_format($n);
  }
  $id = $_GET["id"];
  $content = file_get_contents("https://youtube.com/get_video_info?video_id=".$id);
  parse_str($content, $ytarr);
  $views = $ytarr['view_count'];
  $length = $ytarr['length_seconds'];
  $hours = floor($length / 3600);
  $mins = floor($length / 60 % 60);
  $secs = floor($length % 60);
  echo '{"views": "'.nicenumber($views).'", "length": "'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs).'", "debug": '.json_encode($ytarr).'}';
?>
