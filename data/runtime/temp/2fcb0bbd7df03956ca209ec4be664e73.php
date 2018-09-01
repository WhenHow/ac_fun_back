<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:52:"themes/admin_simpleboot3/user\admin_oauth\index.html";i:1529914518;s:81:"C:\xampp\htdocs\smart_property\public\themes\admin_simpleboot3\public\header.html";i:1529914518;}*/ ?>
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
<div class="wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a><?php echo lang('USER_OAUTHADMIN_INDEX'); ?></a></li>
    </ul>
    <form method="post" class="js-ajax-form margin-top-20">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th><?php echo lang('USER_FROM'); ?></th>
                <th><?php echo lang('AVATAR'); ?></th>
                <th><?php echo lang('BINGDING_ACCOUNT'); ?></th>
                <th><?php echo lang('FIRST_LOGIN_TIME'); ?></th>
                <th><?php echo lang('LAST_LOGIN_TIME'); ?></th>
                <th><?php echo lang('LAST_LOGIN_IP'); ?></th>
                <th><?php echo lang('LOGIN_TIMES'); ?></th>
                <th align="center"><?php echo lang('ACTIONS'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($lists) || $lists instanceof \think\Collection || $lists instanceof \think\Paginator): if( count($lists)==0 ) : echo "" ;else: foreach($lists as $key=>$vo): ?>
                <tr>
                    <td><?php echo $vo['id']; ?></td>
                    <td><?php echo $vo['third_party']; ?></td>
                    <td><img width="25" height="25" src="<?php echo $vo['avatar']; ?>"/></td>
                    <td><?php echo $vo['user_nickname']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$vo['create_time']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$vo['last_login_time']); ?></td>
                    <td><?php echo $vo['last_login_ip']; ?></td>
                    <td><?php echo $vo['login_times']; ?></td>
                    <td>
                        <a href="<?php echo url('adminOauth/delete',array('id'=>$vo['id'])); ?>" class="js-ajax-delete"><?php echo lang('DELETE'); ?></a>
                    </td>
                </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <ul class="pagination"><?php echo $page; ?></ul>
    </form>
</div>
<script src="/static/js/admin.js"></script>
</body>
</html>