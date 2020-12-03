<!DOCTYPE html>
<html>
<head>
<style>
</style>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Astra">

    <link rel="shortcut icon" href="/assets/ntt_images/favicon_1.png" sizes="32x32">


    <title><?php echo $title; ?></title>

    <!--page css-->
        <?php
        $ver = uniqid(); 
        if (isset($layout['css_files'])) {
			if (is_array($layout['css_files'])) {
				$css_array = [];
				foreach ($layout['css_files'] as $file) {
                    if (strpos($file, "assets/app")) {
                        $css_array[] = '<link rel="stylesheet" type="text/css" href="' . $file . '?=' . $ver . '"/>';
                    } else {
                        $css_array[] = '<link rel="stylesheet" type="text/css" href="' . $file . '"/>';
                    }
				}
				echo join("\n", $css_array);
			} else {
				echo $layout['css_files'];
			}
        }
        ?>

    <!--/page css-->
        <link href="/assets/plugins/bootstrap-sweetalert/sweet-alert.css" rel="stylesheet" type="text/css">
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/responsive.css" rel="stylesheet" type="text/css" />


    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <style>
    #translateList input[name="id"] {
        border: none;
    }
    #translateList input[name="ja"][readonly], #translateList input[name="en"][readonly] {
        border: none;
        background: none;
    }
    </style>
    <script src="/assets/js/modernizr.min.js"></script>
</head>

<body class="fixed-left">

    <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <div class="topbar">
                <!-- LOGO -->
                <div class="topbar-left">
                    <div class="text-center">
                        <a href="/" class="logo">
                            <i class="icon-c-logo"> <img src="/assets/ntt_images/logo_ntt_sm.png" height="42"/> </i>
                            <span><img src="/assets/ntt_images/logo_ntt_lg.png" height="40"/></span>
                        </a>
                    </div>
                </div>

                <!-- Button mobile view to collapse sidebar menu -->
                <div class="navbar navbar-default" role="navigation">
                    <div class="container">
                        <div class="">
                            <div class="pull-left">
                                <button class="button-menu-mobile open-left waves-effect waves-light">
                                    <i class="md md-menu"></i>
                                </button>
                                <span class="clearfix"></span>
                            </div>


                            <ul class="nav navbar-nav navbar-right pull-right">
                                <li class="dropdown top-menu-item-xs">
                                    <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="md-person-outline my-ti-user"><?php echo $_SESSION["l10n_email"]; ?></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a id="logoutBtn" href="javascript:void(0)"><i class="ti-power-off m-r-10 text-danger"></i> Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!--/.nav-collapse -->
                    </div>
                </div>
            </div>
            <!-- Top Bar End -->

            <!-- ========== Left Sidebar Start ========== -->
<?php
$this->load->view("layout/l10n_menu");
?>
            <!-- ========== Left Sidebar End ========== -->

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
            <!-- Start content -->
            <div class="content">

    <?php echo $layout["content"]; ?>

            </div><!-- content -->
            </div>


    <!-- END wrapper -->

    <script>
        var resizefunc = [];
    </script>

    <!-- jQuery  -->
        <!-- jQuery  -->
        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
        <script src="/assets/js/detect.js"></script>
        <script src="/assets/js/fastclick.js"></script>

        <script src="/assets/js/jquery.slimscroll.js"></script>
        <script src="/assets/js/jquery.blockUI.js"></script>
        <script src="/assets/js/waves.js"></script>
        <script src="/assets/js/wow.min.js"></script>
        <script src="/assets/js/jquery.nicescroll.js"></script>
        <script src="/assets/js/jquery.scrollTo.min.js"></script>
        <script src="/assets/plugins/pagination/jquery.twbsPagination.min.js"></script>
        <script src="/assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="/assets/plugins/bootstrap-sweetalert/sweet-alert.min.js"></script>

    <!-- jQuery  -->
    <script src="/assets/js/jquery.core.js"></script>
    <script src="/assets/js/jquery.app.js"></script>
    <script src="/assets/app/l10n.js?v=<?php echo time(); ?>"></script>

<?php
$ver = uniqid(); 
if (isset($layout['js_files'])) {
    if (is_array($layout['js_files'])) {
        $js_array = [];
        foreach ($layout['js_files'] as $file) {
            if (strpos($file, "assets/app")) {
                $js_array[] = '<script src="' . $file . '?=' . $ver . '"></script>';
            } else {
                $js_array[] = '<script src="' . $file . '"></script>';
            }
        }
        echo join("\n", $js_array);
    } else {
        echo $layout['js_files'];
    }
}
?>

</body>

</html>
