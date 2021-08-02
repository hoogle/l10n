
            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">
                    <!--- Divider -->
                    <div id="sidebar-menu">
                        <ul>

                        	<li class="text-muted menu-title">Platform Edit</li>

<?php if ( ! $platform_arr) : ?>
                            <li class="">
                                <a class="waves-effect"><i class="fa fa-info-circle"></i><span>No data yet</span></a>
                            </li>
<?php else : ?>
<?php foreach ($platform_arr as $pf) : ?>
<?php
        $pf_str = $pf["production"] . "_" . $pf["platform"];
        $pen = '<i style="color:#ffa857" class="fa fa-pencil-square-o"></i>';
        $mod_icon = $pf_stat[$pf_str]["modified"] ? $pen : "";
        switch ($pf["platform"]) {
            case "Android":
                $tifa_icon = "ti-android";
                break;
            case "iOS":
                $tifa_icon = "fa fa-apple";
                break;
            case "gf":
                $tifa_icon = "ti-mobile";
                break;
            case "portal":
                $tifa_icon = "fa fa-desktop";
                break;
            default:
                $tifa_icon = "fa fa-globe";
                break;
        }
?>
                            <li class="">
                                <a href="/?p=<?php echo $pf_str; ?>" class="waves-effect"><i class="<?php echo $tifa_icon; ?>"></i><span><?php echo $pf["production"] . " -> " . $pf["platform"]; ?></span><?php echo $mod_icon; ?></a>
                            </li>
<?php endforeach ?>
<?php endif ?>


                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

