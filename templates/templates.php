<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Abuse Wordpress Pingback Feature to Disclose Server IP behind CloudFlare">
    <meta name="author" content="Ahmad Ramadhan Amizudin">

    <title>WPCF</title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
  </head>

  <body>
    <div class="site-wrapper">
      <div class="site-wrapper-inner">
        <div class="cover-container">
          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand"><a href="/">WPCF</a></h3>
              <nav>
                <ul class="nav masthead-nav">
                  <!-- <li class="active"><a href="#">Home</a></li> -->
                  <li class="active"><a href="#" data-toggle="modal" data-target="#myModal">WTF IS DIS</a></li>
                  <!-- <li><a href="#">Contact</a></li> -->
                </ul>
              </nav>
            </div>
          </div>
          <div class="inner cover" id="content">
            <!-- <h1 class="cover-heading">Abuse Wordpress Pingback Feature to Disclose Server IP behind CloudFlare</h1> -->

            <div class="notice notice-danger notice-sm" id="warning" style="display:none;">
              <strong>Error!</strong> <span id="warning-msg"></span>
            </div>

            <div class="notice notice-sm" id="info" style="display:none;">
              <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate" id="icspin"></span><span class="glyphicon glyphicon-ok" id="icok" style="display:none;"></span>&nbsp;&nbsp;&nbsp;<strong><span id="info-msg"></span></strong>
            </div>

            <p class="lead" style="text-align: center;">
              <input type="text" id="url" aria-describedby="helpBlock" class="form-control input-lg" placeholder="Enter URL"/>
              <span id="helpBlock" class="help-block">Full url to blog. Eg: http://example.com/blog/</span>
            </p>
            <p class="lead" style="text-align: center;">
              <button id="btnResolve" class="btn btn-lg btn-default">Resolve</button>
            </p>
          </div>
          <div class="mastfoot" style="text-align: center;">
            <div class="inner">
              <p>
                Cover template for <a href="http://getbootstrap.com">Bootstrap</a>, by <a href="https://twitter.com/mdo">@mdo</a>.<br /> 
                &lt;3 Rempah
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel" style="color: #000">????</h4>
          </div>
          <div class="modal-body" style="color: #000">
            TLDR: Abuse Wordpress Pingback Feature to Disclose Server IP behind CloudFlare
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      var timer;
      var counter = 1;

      function isURL(url) {
        return true;
        // fakdisregexp
        {literal}
          var urlregex = new RegExp("/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$/i");
          return urlregex.test(url);
        {/literal}
      }

      function showWarning(msg) {
          $('#warning-msg').text(msg);
          $('#warning').fadeIn();
          setTimeout(function() {
            $('#warning').fadeOut();
          }, 5000);
      }

      function showPermanentWarning(msg) {
          $('#warning-msg').text(msg);
          $('#warning').fadeIn();
      }

      function showInfo(msg) {
          $('#info-msg').text(msg);
          $('#info').fadeIn();
      }

      function hideInfo() {
        $('#info').fadeOut();
      }

      function checkStatus(data) {
        if( counter >= 60 ) {
          hideInfo();
          showPermanentWarning('Error occurred! Server seem not sending any request');
          clearInterval(timer);
          $.post('/api.php', {
            'action': 'error',
            'id': data.id,
            'hash': data.hash
          }, 'json');
        }
        console.log(counter);
        counter++;
        $.post('/api.php', {
          'action': 'check',
          'id': data.id,
          'hash': data.hash
        }, function(r) {
          if(r.error == false) {
            if(r.status == 2) {
              $('#icspin').hide();
              $('#icok').show();
              showInfo('Completed! Server IP: ' + r.data.resolved_ip);
              clearInterval(timer);
            }
          }
        }, 'json');
      }

      $(function() {
        $('#btnResolve').on('click', function(e) {
          var objUrl = $('#url');
          if (objUrl.val() === "") {
            showWarning('Please enter URL');
          } else {
            if (!isURL(objUrl.val())) {
              showWarning('Valid URL is required.');
              objUrl.focus();
            } else {
              $('#icok').hide();
              $('#icspin').show();
              showInfo('Processing!');
              $.post('/api.php', {
                'action': 'submit',
                'url': objUrl.val()
              }, function(r) {
                if(r.error == true) {
                  hideInfo();
                  showWarning(r.message);
                } else {
                  timer = setInterval(function() {
                    checkStatus(r.data);
                  }, 1000);
                }
              }, 'json');
            }
          }
        });
      });
    </script>
    <a href="https://github.com/RamadhanAmizudin/WPCF"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://camo.githubusercontent.com/82b228a3648bf44fc1163ef44c62fcc60081495e/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f6c6566745f7265645f6161303030302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_left_red_aa0000.png"></a>
  </body>
</html>
