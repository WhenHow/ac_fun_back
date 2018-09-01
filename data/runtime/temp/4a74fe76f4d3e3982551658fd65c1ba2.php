<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:47:"themes/admin_simpleboot3/admin\route\index.html";i:1529914518;s:81:"C:\xampp\htdocs\smart_property\public\themes\admin_simpleboot3\public\header.html";i:1529914518;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->


    <link href="/themes/admin_simpleboot3/public/assets/themes/<?php echo cmf_get_admin_style(); ?>/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/admin_simpleboot3/public/assets/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        form .input-order {
            margin-bottom: 0px;
            padding: 0 2px;
            width: 42px;
            font-size: 12px;
        }

        form .input-order:focus {
            outline: none;
        }

        .table-actions {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 0px;
        }

        .table-list {
            margin-bottom: 0px;
        }

        .form-required {
            color: red;
        }
    </style>
    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "/",
            WEB_ROOT: "/",
            JS_ROOT: "static/js/",
            APP: '<?php echo \think\Request::instance()->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="/themes/admin_simpleboot3/public/assets/js/jquery-1.10.2.min.js"></script>
    <script src="/static/js/wind.js"></script>
    <script src="/themes/admin_simpleboot3/public/assets/js/bootstrap.min.js"></script>
    <script>
        Wind.css('artDialog');
        Wind.css('layer');
        $(function () {
            $("[data-toggle='tooltip']").tooltip({
                container:'body',
                html:true,
            });
            $("li.dropdown").hover(function () {
                $(this).addClass("open");
            }, function () {
                $(this).removeClass("open");
            });
        });
    </script>
    <?php if(APP_DEBUG): ?>
        <style>
            #think_page_trace_open {
                z-index: 9999;
            }
        </style>
    <?php endif; ?>
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo url('route/index'); ?>">URL美化</a></li>
        <li><a href="<?php echo url('route/add'); ?>">添加URL规则</a></li>
    </ul>
    <form class="js-ajax-form" action="<?php echo url('route/listOrder'); ?>" method="post">
        <div class="table-actions">
            <button type="submit" class="btn btn-primary btn-sm js-ajax-submit">排序</button>
        </div>
        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="50">排序</th>
                <th width="50">ID</th>
                <th>原始网址</th>
                <th>显示网址</th>
                <th>类型</th>
                <th>状态</th>
                <th width="130">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php 
                $statuses=array('0'=>"已禁用","1"=>"已启用");
                $types=array('1'=>"自定义","2"=>"别名定义");
             if(is_array($routes) || $routes instanceof \think\Collection || $routes instanceof \think\Paginator): if( count($routes)==0 ) : echo "" ;else: foreach($routes as $key=>$vo): ?>
                <tr>
                    <td>
                        <?php if($vo['type'] == '2'): ?>
                            <input class="input-order" type="text" value="<?php echo $vo['list_order']; ?>" readonly>
                            <?php else: ?>
                            <input name="list_orders[<?php echo $vo['id']; ?>]" class="input-order" type="text"
                                   value="<?php echo $vo['list_order']; ?>">
                        <?php endif; ?>

                    </td>
                    <td><?php echo $vo['id']; ?></td>
                    <td><?php echo $vo['full_url']; ?></td>
                    <td><?php echo $vo['url']; ?></td>
                    <td>
                        <?php if($vo['type'] == '2'): ?>
                            <span class="label label-danger" data-toggle="tooltip" title="别名定义规则,无法编辑,排序"><?php echo $types[$vo['type']]; ?></span>
                            <?php else: ?>
                            <span class="label label-success" data-toggle="tooltip" title="自定义规则,可以编辑,排序"><?php echo $types[$vo['type']]; ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $statuses[$vo['status']]; ?></td>
                    <td>
                        <?php if($vo['type'] == '2'): ?>
                            编辑  禁用  删除
                            <?php else: ?>
                            <a href="<?php echo url('route/edit',array('id'=>$vo['id'])); ?>">编辑</a>
                            <?php if($vo['status'] == '1'): ?>
                                <a href="<?php echo url('route/ban',array('id'=>$vo['id'])); ?>" class="js-ajax-dialog-btn"
                                   data-msg="确定禁用吗？">禁用</a>
                                <?php else: ?>
                                <a href="<?php echo url('route/open',array('id'=>$vo['id'])); ?>" class="js-ajax-dialog-btn"
                                   data-msg="确定启用吗？">启用</a>
                            <?php endif; ?>
                            <a href="<?php echo url('route/delete',array('id'=>$vo['id'])); ?>" class="js-ajax-delete">删除</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <th width="50">排序</th>
                <th width="50">ID</th>
                <th>原始网址</th>
                <th>显示网址</th>
                <th>类型</th>
                <th>状态</th>
                <th width="130">操作</th>
            </tr>
            </tfoot>
        </table>
        <div class="table-actions">
            <button type="submit" class="btn btn-primary btn-sm js-ajax-submit">排序</button>
        </div>
    </form>
</div>
<script type="text/javascript" src="/static/js/admin.js"></script>
</body>
</html>