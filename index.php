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
$sc = $_GET["sc"];
if ($sc == "") {
  $sc = 5;
}
if ($sc > 49) {
  $sc = 50;
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
      .search-result-text > .link {
        color: #282828;
        font-weight: bold;
      }
      .search-result-text > .link-big {
        color: #282828;
        font-weight: bold;
      }
      .link > img {
        transition: transform 1s ease;
        width: 280px;
      }
      .link:hover > img {
        transform: scale(1.1, 1.1);
      }
      .link-big > img {
        transition: transform 1s ease;
        width: 320px;
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
      #sign-in-or-out-button {
        color: #dc3545;
        margin-left: 1em;
      }
      #sign-in-or-out-button:hover {
        color: white;
        background: #dc3545;
      }
    </style>
  </head>
  <body>
    <script>function getsc() {return <?php echo $sc; ?>}</script>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#"><img src="logo.svg" height="30" alt=""></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <form class="form-inline my-4 my-lg-0" onsubmit="return search();">
          <input class="form-control mr-sm-4" style="width: 400px;" type="search" id="q" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-danger my-4 my-sm-0" type="submit">Search</button>
          <a class="btn btn-outline-danger my-4 my-sm-0" id="sign-in-or-out-button">Sign in to get your subscription feed</a>
        </form>
      </div>
    </nav>
    <div class="container-fluid" id="body-table">
      <h6>Are you having issues or want to leave a suggestion? Email me: <a href="mailto:filip@kinmails.com?subject=yt.filipkin.com bug/suggestions">filip@kinmails.com</a></h6>
      <div class="row">
        <div id="subdiv" class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" style="text-align:center;display:none;">
          <h2>Subscriptions</h2>
          <hr>
        <div id="subscriptions">
        </div>
        <?php
        $msc = $sc + 10;
        if ($msc > 49) {
          $msc = 50;
        }
        echo '<a href="index.php?sc='.$msc.'&rc='.$rc.'" class="form-control" style="background:#0051BC;color:white;text-align:center;">More Subscription Feed</a>';
        ?>
        </div>
        <script src="https://apis.google.com/js/client.js?onload=onLoadCallback" async defer></script>
        <script>
          var GoogleAuth;
          var SCOPE = 'https://www.googleapis.com/auth/youtube.force-ssl';
          function handleClientLoad() {
            // Load the API's client and auth2 modules.
            // Call the initClient function after the modules load.
            gapi.load('client:auth2', initClient);
          }

          function initClient() {
            // Retrieve the discovery document for version 3 of YouTube Data API.
            // In practice, your app can retrieve one or more discovery documents.
            var discoveryUrl = 'https://www.googleapis.com/discovery/v1/apis/youtube/v3/rest';

            // Initialize the gapi.client object, which app uses to make API requests.
            // Get API key and client ID from API Console.
            // 'scope' field specifies space-delimited list of access scopes.
            gapi.client.init({
                'discoveryDocs': [discoveryUrl],
                'clientId': '1088047505782-gnfl6110hvq4o942hpl1hirginuoj2d2.apps.googleusercontent.com',
                'scope': SCOPE
            }).then(function () {
              GoogleAuth = gapi.auth2.getAuthInstance();

              // Listen for sign-in state changes.
              GoogleAuth.isSignedIn.listen(updateSigninStatus);

              // Handle initial sign-in state. (Determine if user is already signed in.)
              var user = GoogleAuth.currentUser.get();
              setSigninStatus();

              // Call handleAuthClick function when user clicks on
              //      "Sign In/Authorize" button.
              $('#sign-in-or-out-button').click(function() {
                handleAuthClick();
              });
            });
          }

          function handleAuthClick() {
            if (GoogleAuth.isSignedIn.get()) {
              // User is authorized and has clicked 'Sign out' button.
              GoogleAuth.signOut();
            } else {
              // User is not signed in. Start Google auth flow.
              GoogleAuth.signIn();
            }
          }

          function revokeAccess() {
            GoogleAuth.disconnect();
          }

          function setSigninStatus(isSignedIn) {
            var user = GoogleAuth.currentUser.get();
            var isAuthorized = user.hasGrantedScopes(SCOPE);
            if (isAuthorized) {
              $('#sign-in-or-out-button').html('Sign out');
              var element = document.getElementById('subdiv');
              element.style.display = "block";
              $('#trending').addClass("col-xs-0");
              $('#trending').addClass("col-sm-0");
              $('#trending').addClass("col-md-0");
              $('#trending').addClass("col-lg-6");
              $('#trending').addClass("col-xl-6");
              $('#trending').removeClass("col-xs-12");
              $('#trending').removeClass("col-sm-12");
              $('#trending').removeClass("col-md-12");
              $('#trending').removeClass("col-lg-12");
              $('#trending').removeClass("col-xl-12");
              $('.search-result-image').find('.link-big').addClass('link');
              $('.search-result-image').find('.link-big').removeClass('link-big');
              var request = gapi.client.request({
                'method': 'GET',
                'path': '/youtube/v3/subscriptions',
                'params': {'part': 'snippet', 'mine': 'true', 'maxResults': '50'}
              });
              // Execute the API request.
              var numberOfSubsProcessed=0;
              var subs = [];
              request.execute(function(response) {
                var videos = [];
                var items = response.items;
                items.forEach(function(obj){
                  var id = obj.snippet.resourceId.channelId;
                  subs.push(id);
                });
                var sc = getsc();
                var multiple=0;
                for (i=0; i*subs.length<sc; i++) {
                  multiple++;
                }
                subs.forEach(function(sub){
                  var request = gapi.client.request({
                    'method': 'GET',
                    'path': '/youtube/v3/search',
                    'params': {'part': 'snippet', 'channelId': sub, 'maxResults': multiple+1, 'order':'date'}
                  });
                  request.execute(function(response) {
                    numberOfSubsProcessed++;
                    var items = response.items;
                    items.forEach(function(obj) {
                      videos.push(obj)
                    });
                    if (numberOfSubsProcessed == subs.length) {
                      var releaseTimestamps = [];
                      var newvideos = [];
                      videos.forEach(function(obj) {
                        var newobj = {"timestamp": obj.snippet.publishedAt, "id": obj.id.videoId, "channel": obj.snippet.channelTitle, "title": obj.snippet.title, "thumbnail": obj.snippet.thumbnails.medium.url};
                        newvideos.push(newobj);
                      });
                      newvideos.sort(function(a, b) {
                        var at = Date.parse(a.timestamp);
                        var bt = Date.parse(b.timestamp);
                        return bt-at;
                      })
                      newvideos = newvideos.slice(0, sc)
                      console.log(newvideos);
                      var out = [];
                      var x = 0;
                      var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                      newvideos.forEach(function(obj, i) {
                        httpGet("data.php?id="+obj.id, function(data) {
                          var data = JSON.parse(data)
                          var views = data.views;
                          var datetime = new Date(Date.parse(obj.timestamp));
                          var date = months[datetime.getMonth()]+" "+datetime.getDate()+" "+datetime.getFullYear();
                          var length = data.length;
                          var link = '<a href="watch.php?id='+obj.id+'" class="link">';
                          out[i] = '<div class="row">';
                          out[i] += '<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 search-result-image">'+link+'<img src="'+obj.thumbnail+'"></a></div>';
                          out[i] += '<div class="col-xs-0 col-sm-0 col-md-5 col-md-offset-1 col-lg-6 col-xl-6 search-result-text">'+link+obj.title+'</a>';
                          out[i] += '<br>By: '+obj.channel;
                          out[i] += '<br>Views: '+views;
                          out[i] += '<br>Length: '+length;
                          out[i] += '<br>Published: '+date+'</div></div>';
                          document.getElementById("subscriptions").innerHTML=out.join("<hr>")+"<hr>";
                        });
                      })
                    }
                  });
                });
              });
            } else {
              $('#sign-in-or-out-button').html('Sign in to get your subscription feed');
              var element = document.getElementById('subdiv');
              element.style.display = "none";
              $('#trending').removeClass("col-xs-0");
              $('#trending').removeClass("col-sm-0");
              $('#trending').removeClass("col-md-0");
              $('#trending').removeClass("col-lg-6");
              $('#trending').removeClass("col-xl-6");
              $('#trending').addClass("col-xs-12");
              $('#trending').addClass("col-sm-12");
              $('#trending').addClass("col-md-12");
              $('#trending').addClass("col-lg-12");
              $('#trending').addClass("col-xl-12");
              $('.search-result-image').find('.link').addClass('link-big');
              $('.search-result-image').find('.link').removeClass('link');
            }
          }

          function updateSigninStatus(isSignedIn) {
            setSigninStatus();
          }

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
        </script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <script async defer src="https://apis.google.com/js/api.js"
              onload="this.onload=function(){};handleClientLoad()"
              onreadystatechange="if (this.readyState === 'complete') this.onload()">
      </script>
        <div id="trending" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" style="text-align:center;">
          <h2>Trending</h2>
          <hr>
          <?php
            $result = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet&chart=mostPopular&maxResults=".$rc."&key=AIzaSyAiH7HiJnrV6AERNET7ISmcONbisaJvkOA"));
            foreach($result->items as &$obj) {
              $url = "watch.php?id=".$obj->id;
              $link = '<a class="link-big" href="'.$url.'">';
              $content = file_get_contents("https://youtube.com/get_video_info?video_id=".$obj->id);
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
            echo '<a href="index.php?sc='.$sc.'&rc='.$mrc.'" class="form-control" style="background:#0051BC;color:white;text-align:center;">More Trending</a>';
          ?>
        </div>
      </div>
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
