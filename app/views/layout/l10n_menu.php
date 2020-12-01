
            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">
                    <!--- Divider -->
                    <div id="sidebar-menu">
                        <ul>

                        	<li class="text-muted menu-title">Platform Edit</li>

<?php foreach ($platform_arr as $pf) : ?>
                            <li class="">
                                <a href="/?p=<?php echo $pf["production"] . "_" . $pf["platform"]; ?>" class="waves-effect"><i class="ti-pencil-alt"></i><span><?php echo $pf["production"] . " -> " . $pf["platform"]; ?></span></a>
                            </li>
<?php endforeach ?>


                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

