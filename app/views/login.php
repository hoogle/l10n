<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Localization - Login</title>
  <!-- Custom fonts for this template-->
  <link href="/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="//fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="/assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="">
  <div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-login-image">
                <!--img src="/assets/images/doggy.jpg"-->
                <img src="https://up.enterdesk.com/edpic/a5/7e/de/a57ede09f9e99cf67b08488bd2e622e2.jpg">
              </div>
              <div class="col-lg-6">
                <div class="p-5">
					<div class="text-center">
					  <h1 class="h4 text-gray-900 mb-4"><br/><br/></h1>
					</div>
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Localization <span style="color:white">Management</span></h1>
                  </div>
                  <form class="text-center user">
                    <hr>
                    <a href="<?=$gl_login_url?>" class="btn btn-google btn-user">
                      &nbsp;&nbsp;&nbsp;<i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                  </form>
                  <hr>
                  <div class="text-center">
                    <br/><br/><br/><br/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
  <?php
  if (isset($_GET['error'])) {
      $error = htmlspecialchars($_GET['error'], ENT_QUOTES);
      if ($error == 'miss_code') {
          $error = 'Miss Code';
      } else if($error == 'error_authenticate') {
          $error = 'Authentication Failed';
      } else if($error == 'not_support_domain') {
          $error = 'Only support astra.cloud domain';
      } else if($error == 'session_error') {
          $error = 'Session Failed';
      }
      ?>Swal.fire({type: 'error', text: '<?=$error?>',});<?php
  }
  ?>
  </script>
</body>
</html>
