
            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">
                    <!--- Divider -->
                    <div id="sidebar-menu">
                        <ul>

                        	<li class="text-muted menu-title">Platform Edit</li>

<?php foreach ($platform_arr as $platform) : ?>
                            <li class="">
                                <a href="/?platform=<?php echo $platform; ?>" class="waves-effect"><i class="ti-pencil-alt"></i><span><?php echo $platform; ?></span></a>
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
                                <a href="/tool/compare/warehouse/en-US" class="waves-effect"><i class="ti-pencil-alt"></i><span>Warehouse en-US</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/compare/warehouse/ja-JP" class="waves-effect"><i class="ti-pencil-alt"></i><span>Warehouse ja-JP</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/compare/goface/en-US" class="waves-effect"><i class="ti-pencil-alt"></i><span>GoFace en-US</span></a>
                            </li>
                        	<li class="text-muted menu-title">Export</li>
                            <li class="">
                                <a href="/tool/export?platform=api_error_code" class="waves-effect"><i class="ti-pencil-alt"></i><span>api_error_code</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/export?platform=genesis_msp_js" class="waves-effect"><i class="ti-pencil-alt"></i><span>genesis_msp_js</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/export?platform=genesis_msp_php" class="waves-effect"><i class="ti-pencil-alt"></i><span>genesis_msp_php</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/export?platform=genesis_portal" class="waves-effect"><i class="ti-pencil-alt"></i><span>genesis_portal</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/export?platform=wh_msp" class="waves-effect"><i class="ti-pencil-alt"></i><span>genesis_warehouse</span></a>
                            </li>
                            <li class="">
                                <a href="/tool/export?platform=goface" class="waves-effect"><i class="ti-pencil-alt"></i><span>goface</span></a>
                            </li>

                        	<li class="text-muted menu-title">Import</li>
                            <li>
                                <a href="/tool/import_from_db/error_map">Error code import</a>
                            </li>
                            <li>
                                <a href="/tool/import_from_db/template">Template import</a>
                            </li>
                            <li>
                                <a href="/tool/parsing/php/en-US">Genesis msp import</a>
                            </li>
                            <li>
                                <a href="/tool/import_from_web/portal/en-US">Genesis portal import</a>
                            </li>
                            <li>
                                <a href="/tool/import_from_web/warehouse/en-US">Genesis warehouse import</a>
                            </li>
                            <li>
                                <a href="/tool/import_from_web/goface/en-US">GoFace import</a>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

