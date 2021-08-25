<div class="container">
    <div class="col-sm-12">
        <div class="pull-right d-flex" style="position: relative;">
        </div>
        <h4 class="page-title">Environment : <span id="platform"><?php echo $production; ?> - Relationship for Android / iOS</span></h4>
        <ol class="breadcrumb"></ol>
    </div>
     
    <div class="col-sm-12">
        <div class="card-box table-responsive" id="mainTable">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <input type="hidden" name="production" value="<?php echo $production; ?>">
            <div class="row m-b-20">
                <h4 class="m-t-0 header-title col-md-2 col-sm-12"><b>Key List</b></h4>
                <div class="col-md-6 col-sm-12 pull-right">
                    <form name="searchTranslate" class="form-horizontal" method="post">
                    <div class="form-group m-r-10">
                        <label class="col-md-4 control-label">Search:</label>
                        <div class="col-md-8">
                            <input type="search" name="key" class="form-control input-sm" placeholder="">
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="p-10">
                <div class="row m-b-10" style="padding: 8px; font-weight: 600; vertical-align: bottom; border-bottom: 2px solid #ebeff2;">
                    <div class="d-flex justify-content-around" id="mainColGroup">
                        <div id="col_ui_key" class="text-center ti- uiKeyCol">UI</div>
                        <div id="col_platform" class="text-center ti- ptCol"><i class="ti-mobile p-0"></i></div>
                        <div id="col_keyword" class="text-center ti- keyCol">KEY</div>
<?php foreach ($user_langs as $lang) : ?>
                        <div id="col_<?php echo $lang; ?>" class="text-center ti-"><i class="hideColBtn ti ti-shift-left"></i><?php echo $lang; ?><i class="showColBtn ti ti-shift-right"></i></div>
<?php endforeach ?>
                        <div class="hide text-white translateSubmit">.</div>
                        <div class="" style="flex: 0 0 24px;"></div>
                    </div>
                </div>
                <div id="translateList" class="col-sm-12"></div>
            </div>
            <div id="pagination" class="text-center"></div>
        </div>
    </div>
        <template id="transListRowTemp">
            <div class="row translateRow m-b-10 d-flex align-item-center" id="ui-{ui_key}">
                    <div class="uiKeyCol">
                        {ui_key}
                    </div>
                <form name="l10n_{id}" class="d-flex align-item-center flex-fill">
                    <input type="hidden" name="platform" value="{platform}"/>
                    <div class="ptCol">
                        <i class="ti-{platformIcon} m-l-10"></i>
                    </div>
                    <div class="translateCol keyCol">
                        <input type="hidden" name="id" value="{id}">
                        <textarea class="form-control text-center" name="keyword" title="{keyword}" readonly>{keyword}</textarea>
                    </div>
        <?php foreach ($user_langs as $lang) : ?>
        <?php   $input_name = strtolower(str_replace("-", "", $lang)); ?>
                    <div class="translateCol edit_<?php echo $lang; ?>">
                        <textarea class="form-control" name="<?php echo $input_name; ?>" title="{<?php echo $lang; ?>}" readonly>{<?php echo $lang; ?>}</textarea>
                    </div>
        <?php endforeach ?>
                    <div class="translateSubmit hide">
                        <button type="submit" class="btn btn-default waves-effect waves-light btn-md">Save</button>
                    </div>
                </form>
            </div>
        </template>
</div>

