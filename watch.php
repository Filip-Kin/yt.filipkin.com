<!doctype html>
<?php include("/var/www/analytics.php"); ?>
<?php ini_set('display_errors', 'Off'); ?>
<?php
$id = $_GET["id"];
$content = file_get_contents("https://youtube.com/get_video_info?video_id=".$id);
parse_str($content, $ytarr);
$title = $ytarr['title'];
$views = $ytarr['view_count'];
$channel = $ytarr['author'];
$length = $ytarr['length_seconds'];
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
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <style>
    .related-link {
      font-weight: bold;
      color: #282828;
      transition: color 1s ease;
      font-size:18px;
    }
    .related-channel {
      color: #333333;
      transition: color 1s ease;
    }
    .related-link:hover {
      font-weight: bold;
      color: #444444;
    }
    .related-channel:hover {
      color: #666666;
    }
    .related-link > img {
      transition: transform 1s ease;
    }
    .related-link:hover > img {
      transform: scale(1.1, 1.1);
    }
  </style>
  <title><?php echo $title; ?></title>
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
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-zero embed-responsive embed-responsive-16by9">
          <iframe class="embed-responsive-item" src="http://www.youtube.com/embed/<?php echo $id; ?>?autoplay=1" frameborder=0 allowfullscreen="allowfullscreen"></iframe>
        </div>
        <h1><?php echo $title; ?></h1>
        Channel: <?php echo $channel; ?>
        <br>Views: <?php echo nicenumber($views); ?>
      </div>
      <div class="col-xs-0 col-sm-0 col-md-0 col-lg-5">
        <h4>Related Videos</h4><table id="related"></table>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script>
    function httpGet(theUrl, callback) {
      var xmlHttp = new XMLHttpRequest();
      xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            callback(xmlHttp.responseText);
        }
      }
      xmlHttp.open("GET", theUrl, true); // true for asynchronous
      xmlHttp.send(null);
    }

    httpGet("https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=10&relatedToVideoId=<?php echo $id; ?>&type=video&key=AIzaSyAiH7HiJnrV6AERNET7ISmcONbisaJvkOA", function(data) {
      var rt = document.getElementById("related");
      var videos = JSON.parse(data).items;
      videos.forEach(function(obj) {
        var row = rt.insertRow(rt.rows.length);
        httpGet("data.php?id="+obj.id.videoId, function(data) {
          var data = JSON.parse(data)
          var views = data.views;
          var length = data.length;
          row.insertCell(0).innerHTML = '<a class="related-link" href="watch.php?id='+obj.id.videoId+'"><img src="'+obj.snippet.thumbnails.medium.url+'"></a>';
          var data = '<a class="related-link" href="watch.php?id='+obj.id.videoId+'">'+obj.snippet.title+'</a>';
          data += '<br><a class="related-channel" href="#">'+obj.snippet.channelTitle+'</a>';
          data += '<br>Views: '+views+'<br>Length: '+length;
          row.insertCell(1).innerHTML = data
        });
      });
    });
  </script>
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
