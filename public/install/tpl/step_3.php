<?php require 'tpl/header.php';?>
	<div class="main">
		<form action="index.php?step=3" method="post">
		<table class="table1 zebra">
			<tr>
				<th width="10%">数据库服务器</th>
				<th>安装后,原数据库会被清空,请做好备份</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库服务器：</td>
				<td><input type="text" class="text" value="127.0.0.1" name="DB_HOST" /></td>
				<td>本地填写：127.0.0.1或IP</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库端口：</td>
				<td><input type="text" class="text" value="3306" name="DB_PORT" /></td>
				<td>数据库端口默认为3306</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库用户名：</td>
				<td><input type="text" class="text" value="root" name="DB_USER" /></td>
				<td>请填写具有root权限的用户</td>
			</tr>
			<tr>
				<td>数据库密码：</td>
				<td><input type="text" class="text" value="" name="DB_PWD" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库名称：</td>
				<td><input type="text" class="text" value="nbnbk" name="DB_NAME" /></td>
				<td>不存在则自动创建</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库表前缀：</td>
				<td><input type="text" class="text" value="fl_" name="DB_PREFIX" /></td>
				<td>不推荐使用默认表前缀</td>
			</tr>
			<tr>
				<th>网站配置</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td>网站名称：</td>
				<td><input type="text" class="text" value="我的网站" name="SITE_NAME" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;网站网址：</td>
				<td><input type="text" class="text" value="<?php echo $weburl;?>" name="SITE_URL" /></td>
				<td>请带上协议头http://或https://</td>
			</tr>
			<!-- <tr>
				<td><font class="red">*</font>&nbsp;网站主题：</td>
				<td><input type="radio" name="SITE_THEME" value="default" checked="checked">企业站 <input type="radio" name="SITE_THEME" value="blog">个人博客</td>
				<td>&nbsp;</td>
			</tr> -->
			<tr>
				<th>网站超级管理员</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;用户名：</td>
				<td><input type="text" class="text" value="admin888" name="username" readonly="readonly" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;密　码：</td>
				<td><input type="text" class="text" value="123456" name="password" readonly="readonly" /></td>
				<td>请设置最少6位密码</td>
			</tr>
			<!-- <tr>
				<td><font class="red"></font>&nbsp;E-mail：</td>
				<td><input type="text" class="text" value="" name="email" /></td>
				<td>&nbsp;</td>
			</tr> -->
			<!-- <tr>
				<td>测试数据：</td>
				<td><label><input type="checkbox" value="1" name="add_test" /><?php echo $lang['test_data_intro']; ?></label></td>
				<td>&nbsp;</td>
			</tr> -->
		</table>
		<div class="action"><a href="javascript:history.go(-1);" class="btn_blue">上一步</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn_blue" onclick="postData()">下一步</a></div>
		</form>
	</div>
<script type="text/javascript">
function postData() {
	var _postForm = $('form').serialize();
	$.post('index.php?step=3',_postForm,function(data){
		if(data.status == 'error') {
			alert(data.info);
			return false;
		} else {
			window.location.href = 'index.php?step=4';
		}
	},'json');
}
</script>
</body></html>