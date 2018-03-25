<!doctype html>
<?php include("/var/www/analytics.php"); ?>
<?php ini_set('display_errors', 'Off'); ?>
<?php
function decode($str) {
  $dict = "FEDCBAPONMLKJIHGZYXWVUTSRQjihgfedcbatsrqponmlk4321zyxwvu~_-.098765";
  $newdict = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_~");
  $ar = str_split($str);
  $out = "";
  foreach($ar as &$letter) {
    $index = strpos($dict, $letter);
    if ($index == false) {
      $out.=$letter;
    } else {
      $out.=$newdict[$index];
    }
  }
  return $out;
}
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
$rc = $_GET["rc"];
if ($rc == "") {
  $rc = 5;
}
if ($rc > 49) {
  $rc = 50;
}
?>
<html>
  <head>
    <title>No VPN Youtube</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
      .search-result-image {
        text-align: right !important;
      }
      .search-result-text {
        text-align: left !important;
      }
      .link > img {
        transition: transform 1s ease;
        width: 320px;
      }
      .link:hover > img {
        transform: scale(1.1, 1.1);
      }
      .link  {
        color: #282828;
        transition: color 0.5s ease;
        font-weight: bold;
      }
      .link:hover {
        color: #555555;
      }
      @media screen and (max-width: 48em) {
        .search-result-image {
            text-align: center !important;
        }
        .search-result-text {
            text-align: center !important;
            font-size: 12px;
        }
      }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="index.php"><img src="logo.svg" height="30" alt=""></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <form class="form-inline my-4 my-lg-0" onsubmit="return search();">
          <input class="form-control mr-sm-4" style="width: 400px;" type="search" id="q" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-danger my-4 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>
    <div class="container-fluid" id="body-table">
      <hr>
    <?php
      $q = decode($_GET['q']);
      $result = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/search?type=video&maxResults=".$rc."&key=AIzaSyAiH7HiJnrV6AERNET7ISmcONbisaJvkOA&part=snippet&q=".urlencode($q)));
      foreach($result->items as &$obj) {
        $url = "watch.php?id=".$obj->id->videoId;
        $link = '<a class="link" href="'.$url.'">';
        $content = file_get_contents("https://youtube.com/get_video_info?video_id=".$obj->id->videoId);
        parse_str($content, $ytarr);
        $title = $ytarr['title'];
        $views = $ytarr['view_count'];
        $length = $ytarr['length_seconds'];
        $hours = floor($length / 3600);
        $mins = floor($length / 60 % 60);
        $secs = floor($length % 60);
        echo '<div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 search-result-image">'.$link.'<img src="'.$obj->snippet->thumbnails->medium->url.'"></a></div>
        <div class="col-xs-0 col-sm-0 col-md-5 col-lg-6 col-xl-6 search-result-text">'.$link.$obj->snippet->title.'</a>
        <br>By: '.$obj->snippet->channelTitle.'
        <br>Views: '.nicenumber($views).'
        <br>Length: '.sprintf('%02d:%02d:%02d', $hours, $mins, $secs).'</div></div><hr>';
      }
      $mrc = $rc + 10;
      if ($mrc > 49) {
        $mrc = 50;
      }
      echo '<a href="search.php?q='.urlencode($_GET["q"]).'&rc='.$mrc.'" class="form-control" style="background:#0051BC;color:white;text-align:center;">More Results</a>';
      echo '<script>document.getElementById("q").value="'.$q.'"</script>';
    ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
      function encode(str) {
        var dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_~".split('')
        var newdict = "FEDCBAPONMLKJIHGZYXWVUTSRQjihgfedcbatsrqponmlk4321zyxwvu~_-.098765".split('')
        var ar = str.split('')
        var out = []
        ar.forEach(function(letter) {
          var index = dict.indexOf(letter);
          if (index == -1) {
            out.push(letter)
          } else {
            out.push(newdict[index])
          }
        })
        return encodeURIComponent(out.join(''))
      }
      function decode(str) {
        var dict = "FEDCBAPONMLKJIHGZYXWVUTSRQjihgfedcbatsrqponmlk4321zyxwvu~_-.098765".split('')
        var newdict = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_~".split('')
        var ar = decodeURIComponent(str).split('')
        var out = []
        ar.forEach(function(letter) {
          var index = dict.indexOf(letter);
          if (index == -1) {
            out.push(letter)
          } else {
            out.push(newdict[index])
          }
        })
        return out.join('')
      }
      function search() {
        var url = "http://yt.filipkin.com/search.php?q="+encode(document.getElementById("q").value)
        window.location = url;
        return false;
      }
    </script>
  </body>
</html>
