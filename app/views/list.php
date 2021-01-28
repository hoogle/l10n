<div class="container">
    <div class="col-sm-12">
        <div class="btn-group pull-right">
            <!-- Button trigger modal -->
            <button class="btn btn-primary waves-effect waves-light" id="NewKeyBtn" data-toggle="modal" data-target="#myAdd"><i class="fa fa-plus"></i> New Key</button>
            <button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#myExport"><i class="fa fa-share"></i> Export</button>
        </div>
        <h4 class="page-title">Environment : <span id="platform"><?php echo $production; ?> / <?php echo $platform; ?></span></h4>
        <ol class="breadcrumb"></ol>
    </div>
     
    <div class="col-sm-12">
        <div class="card-box table-responsive" id="mainTable">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <h4 class="m-t-0 header-title"><b>Key List</b></h4>
            <div class="row">
                <div class="col-sm-8 pull-left">
                    <b style="color: #beaf5f">Export to S3 url: </b><br>
                    <i id="copy-url" style="font-size:13px;cursor:pointer;" class="fa" title="Copy S3 url to clipboard">&#xf0c5;</i>
                    <a id="s3-url" target="_blank" href="<?php echo $s3_link . "?t=" . time(); ?>"><?php echo $s3_link; ?></a>
                    <i style="font-size:9px" class="fa">&#xf064;</i>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 m-t-10">
                <button type="button" class="btn btn-white btn-sm ti-eye btn-rounded" id="showKeyBtn"> Key</button>
                </div>
                <div class="col-xs-6 pull-right">
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
                        <div class="text-center keyCol">KEY</div>
                        <div id="col_en-US" class="text-center ti-">en-US</div>
                        <div id="col_ja-JP" class="text-center ti-">ja-JP</div>
                        <div id="col_zh-TW" class="text-center ti-">zh-TW</div>
                        <div id="col_id-ID" class="text-center ti-">id-ID</div>
                        <div id="col_ms-MY" class="text-center ti-">ms-MY</div>
                        <div class="text-white">.</div>
                    </div>
                </div>
                <div id="translateList" class="col-sm-12"></div>
            </div>
            <div id="pagination" class="text-center"></div>
        </div>
    </div>
</div>

<template id="transListRowTemp">
    <div class="row translateRow m-b-10">
        <form name="l10n_{id}" class="d-flex">
        <input type="hidden" name="platform" value="<?php echo $platform; ?>"/>
        <div class="translateCol keyCol">
            <input type="hidden" name="id" value="{id}">
            <textarea class="form-control text-center" name="keyword" title="{keyword}" readonly>{keyword}</textarea>

        </div>
        <div class="translateCol">
            <textarea class="form-control" name="enus" title="{en-US}" readonly>{en-US}</textarea>
        </div>
        <div class="translateCol">
            <textarea class="form-control" name="jajp" title="{ja-JP}" readonly>{ja-JP}</textarea>
        </div>
        <div class="translateCol">
            <textarea  class="form-control" name="zhtw" title="{zh-TW}" readonly>{zh-TW}</textarea>
        </div>
        <div class="translateCol">
            <textarea class="form-control" name="idid" title="{id-ID}" readonly>{id-ID}</textarea>
        </div>
        <div class="translateCol">
            <textarea class="form-control" name="msmy" title="{ms-MY}" readonly>{ms-MY}</textarea>
        </div>
        <div class="translateCol">
            <button type="submit" class="btn btn-default waves-effect waves-light btn-md">Save</button>
        </div>
        </form>
    </div>
</template>

<div id="myExport" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabelExport" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="exportIt" class="form-horizontal" role="form" action="/tool/export?p=<?php echo $production; ?>_<?php echo $platform; ?>" data-parsley-validate novalidate>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabelExport">Export translation</h4>
                </div>
                <div class="modal-body">
                    <h3>Make sure export translation to S3?</h3>
                    <div>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                        &nbsp;
                        <button type="submit" class="btn btn-danger waves-effect waves-light">Export</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myAdd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabelAdd" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="addKey" class="form-horizontal" role="form" action="/index/add" data-parsley-validate novalidate>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabelAdd">New Key</h4>
            </div>
            <div class="modal-body">
            <input type="hidden" name="production" value="<?php echo $production; ?>">
            <input type="hidden" name="platform" value="<?php echo $platform; ?>">
            <input type="hidden" name="keyword" value="">
                <div class="form-group">
                    <label class="col-md-2 control-label">Default String</label>
                    <div class="col-md-10">
                        <textarea name="d4str" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">en-US</label>
                    <div class="col-md-10">
                        <textarea name="enus" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ja-JP</label>
                    <div class="col-md-10">
                        <textarea name="jajp" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">zh-TW</label>
                    <div class="col-md-10">
                        <textarea name="zhtw" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">id-ID</label>
                    <div class="col-md-10">
                        <textarea name="idid" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ms-MY</label>
                    <div class="col-md-10">
                        <textarea name="msmy" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
