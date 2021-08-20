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
                        <div id="col_id" class="text-center ti- idCol">ID</div>
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
</div>

