
            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">
                    <!--- Divider -->
                    <div id="sidebar-menu">
                        <ul>
                        	<li class="text-muted menu-title">Platforms</li>
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
        $link = "/trans?p=" . $pf_str;
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
                $tifa_icon = "fa fa-tv";
                break;
            case "email":
                $tifa_icon = "ti-email";
                break;
            default:
                $tifa_icon = "fa fa-globe";
                break;
        }
?>
                            <li class="">
                                <a href="<?php echo $link; ?>" class="waves-effect"><i class="<?php echo $tifa_icon; ?>"></i><span><?php echo $pf["production"] . " -> " . $pf["platform"]; ?></span><?php echo $mod_icon; ?></a>
                            </li>
<?php endforeach ?>
<?php endif ?>
                        </ul>
                        <ul>
                            <li class="text-muted menu-title">Relationship</li>
                            <li class="">
                                <a href="/relation/?production=goface" class="waves-effect"><i class="fa fa-link"></i><span>Relationship</span><?php echo $mod_icon; ?></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

