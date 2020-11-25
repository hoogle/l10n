
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

                        	<li class="text-muted menu-title">Compare</li>
                            <li class="">
                                <a href="/tool/compare/portal/en-US" class="waves-effect"><i class="ti-pencil-alt"></i><span>Portal en-US</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/compare/portal/ja-JP" class="waves-effect"><i class="ti-pencil-alt"></i><span>Portal ja-JP</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/compare/goface/en-US" class="waves-effect"><i class="ti-pencil-alt"></i><span>GoFace en-US</span></a>
                            </li>
                        	<li class="text-muted menu-title">Export</li>
                            <li class="">
                                <a href="/tool/export?p=api_error_code" class="waves-effect"><i class="ti-pencil-alt"></i><span>api_error_code</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/export?p=goface_portal" class="waves-effect"><i class="ti-pencil-alt"></i><span>Goface Portal</span></a>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

