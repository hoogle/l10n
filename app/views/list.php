<div class="container">
        <div class="col-sm-12">
<div class="btn-group pull-right m-t-15">
            <!-- Button trigger modal -->
            <?php if (! preg_match('/^genesis/', $platform, $matches)): ?>
            <button class="btn btn-pink waves-effect waves-light" data-toggle="modal" data-target="#myAdd"><i class="md md-add"></i>New Key</button>
            <?php endif; ?>
            </div>
            <h4 class="page-title">Platform : <span id="platform"><?php echo $platform; ?></span></h4>
            <ol class="breadcrumb"></ol>

        </div>
     
    <div class="col-sm-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title"><b>Key List</b>
            </h4>
        <div class="row">
        <div class="col-sm-4 pull-right">
        <form name="searchTranslate" class="form-horizontal" method="post">
        <div class="form-group m-r-10"> <label class="col-md-4 control-label" >Search:</label>
            <div class="col-md-8">
            <input type="search" name="key" class="form-control input-sm" placeholder="">
            </div>
        </div>
        </form>
        </div>
    </div>
            <div class="p-10">
                <div class="row m-b-10" style="padding: 8px; font-weight: 600; vertical-align: bottom; border-bottom: 2px solid #ebeff2;">
                    <div class="col-sm-1 text-center">
                    KEY
                    </div>
                    <div class="col-sm-2 text-center">
                    en-US
                    </div>
                    <div class="col-sm-2 text-center">
                    ja-JP
                    </div>
                    <div class="col-sm-2 text-center">
                    zh-TW
                    </div>
                    <div class="col-sm-2 text-center">
                    id-ID
                    </div>
                    <div class="col-sm-2 text-center">
                    ms-MY
                    </div>
                    <div class="col-sm-1">
                    </div>
                </div>
                <div id="translateList" class="col-sm-12">
                </div>
            </div>
            <div id="pagination" class="text-center"></div>
        </div>
    </div> </div>

<template id="transListRowTemp">
    <div class="row translateRow m-b-10">
    <form name="l10n_{id}">
    <input type="hidden" name="platform" value="<?php echo $platform; ?>"/>
        <div class="col-sm-1">
        <input type="hidden" name="id" value="{id}">
        <input type="text" class="form-control text-center text-muted" name="keyword" value="{keyword}" readonly/>
        </div>
        <div class="col-sm-2">
        <input type="text" class="form-control" name="enus" value="{en-US}" readonly/>
        </div>
        <div class="col-sm-2">
        <input type="text" class="form-control" name="jajp" value="{ja-JP}" readonly/>
        </div>
        <div class="col-sm-2">
        <input type="text" class="form-control" name="zhtw" value="{zh-TW}" readonly/>
        </div>
        <div class="col-sm-2">
        <input type="text" class="form-control" name="idid" value="{id-ID}" readonly/>
        </div>
        <div class="col-sm-2">
        <input type="text" class="form-control" name="msmy" value="{ms-MY}" readonly/>
        </div>
        <div class="col-sm-1">
        <button type="submit" class="btn btn-default waves-effect waves-light btn-md">Save</button>
        </div>
    </form>
    </div>
</template>
    <div id="myAdd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="addKey" class="form-horizontal" role="form" action="/l10n/translate/add" data-parsley-validate novalidate>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">New Key</h4>
            </div>
            <div class="modal-body">
            <input type="hidden" name="platform" value="<?php echo $platform; ?>">
                <div class="form-group">
                    <label class="col-md-1 control-label">Keyword</label>
                    <div class="col-md-10">
                        <input type="text" name="keyword" class="form-control" value="" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">en-US</label>
                    <div class="col-md-10">
                        <input type="text" name="enus" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ja-JP</label>
                    <div class="col-md-10">
                        <input type="text" name="jajp" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">zh-TW</label>
                    <div class="col-md-10">
                        <input type="text" name="zhtw" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">id-ID</label>
                    <div class="col-md-10">
                        <input type="text" name="idid" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ms-MY</label>
                    <div class="col-md-10">
                        <input type="text" name="msmy" class="form-control" required>
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
