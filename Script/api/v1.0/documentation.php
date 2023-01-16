<head>
	<style>
	@import url(https://fonts.googleapis.com/css?family=Roboto:100,200,300,400,500,700&subset=latin,latin-ext);
	body
	{
		color: #4e5665;
		font-size: 14px;
		font-family: Roboto, 'Segoe UI', Tahoma, Segoe UI, Arial, sans-serif
	}
	.api-intro
	{
		font-size: 16px;
		padding: 20px 10px;
		border-bottom: 1px solid #eee;
	}
	.parameter-container
	{
		padding: 25px;
		border-bottom: 1px solid #eee;
	}
	.param-name
	{
		color: #69c473;
		font-family: Menlo, Monaco, Andale Mono, Courier New, monospace;
	}
	.param-descr
	{
		padding: 20px 0 0 0;
	}
	.param-more
	{
		padding: 20px 0 0 0;
	}
	ul li
	{
		padding: 5px 0;
		line-height: 25px;
	}
	li span
	{
		color: #699dc4;
		font-family: Menlo, Monaco, Andale Mono, Courier New, monospace;
	}
	.inbl-wid105
	{
		display: inline-block;
		width: 105px;
		text-align: left;
	}
	.param-code
	{
		margin: 10px 0 0 0;
	}
	code
	{
		background-color: rgba(88, 144, 255, 0.1);
		color: #5890ff;
		padding: 8px;
	}
	.proglang-name
	{
		background: rgba(218, 122, 18, 0.3);
		color: #da7a12;
		padding: 10px;
	}
	.langcode
	{
		padding: 10px;
	    margin: 0 0 10px;
	    font-family: monospace;
		font-size: 16px;
	    color: #87ff58;
	    word-break: break-all;
	    word-wrap: break-word;
	    background-color: #4e5665;
	    border: 1px solid rgba(0, 0, 0, 0.1);
	    border-radius: 4px;
	    line-height: 25px;
	}
	</style>
</head>
<body>
	<?php require_once('connect.php'); ?>
	<div class="api-intro">
		Our API allows you to retrieve informations from our website via GET request using the following GET parameters:
	</div>

	<div class="parameter-container">
		<div class="param-name">type</div>
		<div class="param-descr">Defines the type of the query you want to run. This parameter is <strong>required</strong> and must be filled in.</div>
		<div class="param-more">
			<strong>Possible values:</strong><br>
			<ul>
				<li><span class="inbl-wid105">profile_data</span> : To retrieve profile information about a certain User or Page.</li>
				<li><span class="inbl-wid105">post_data</span> : To retrieve information about a post/story.</li>
				<li><span class="inbl-wid105">profile_posts</span> : To retrieve unique numeric ID(s) of posts/stories posted by a certain User or Page. These ID(s) can later be used with 'post_data' type parameter to retrieve story informations.</li>
				<li><span class="inbl-wid105">search</span> : To retrieve profile informations that matches the search query.</li>
			</ul>
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-name">username</div>
		<div class="param-descr">Defines the username of the profile you want to retrieve information of. This parameter works with <i>profile_data</i> type parameter only. This parameter is not required if you're using <i>profileid</i> parameter instead, otherwise it <strong>must be filled</strong> in.</div>
		<div class="param-more">
			<strong>Allowed values:</strong> Username of a specific User or Page.
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-name">postid</div>
		<div class="param-descr">Defines the ID of the post you want to retrieve information of. This parameter works with <i>post_data</i> type parameter only. This parameter <strong>must be filled</strong> in.</div>
		<div class="param-more">
			<strong>Allowed values:</strong> Numeric ID of a specific Post.
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-name">profileid</div>
		<div class="param-descr">Defines the ID of the profile you want to retrieve information of. This parameter works with <i>profile_data</i> type parameter only. This parameter is not required if you're using <i>username</i> parameter instead, otherwise it <strong>must be filled</strong> in.</div>
		<div class="param-more">
			<strong>Allowed values:</strong> Numeric ID of a specific User or Page.
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-name">query</div>
		<div class="param-descr">Defines the search keyword you want to run search on.</div>
		<div class="param-more">
			<strong>Allowed values:</strong> Alphabets A-Z and numbers 0-9.
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-name">limit</div>
		<div class="param-descr">Defines the limit of the items you want to retrieve.</div>
		<div class="param-more">
			<strong>Allowed values:</strong> Numeric 0-9.
		</div>
		<div class="param-more">
			<strong>Default value:</strong> 5
		</div>
	</div>

	<div class="api-intro">
		Examples on how to make an API request:
	</div>

	<div class="parameter-container">
		<div class="param-descr">To retrieve profile data of a User or Page:</div>
		<div class="param-code">
			<code><?php echo $site_url; ?>/api/v1.0/?type=profile_data&username=USERNAME</code>
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-descr">To retrieve data of a Post/Story:</div>
		<div class="param-code">
			<code><?php echo $site_url; ?>/api/v1.0/?type=post_data&postid=POST_ID</code>
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-descr">To retrieve list of posts/stories posted by a User or Page:</div>
		<div class="param-code">
			<code><?php echo $site_url; ?>/api/v1.0/?type=profile_posts&profileid=PROFILE_ID</code>
		</div>
	</div>

	<div class="parameter-container">
		<div class="param-descr">To retrieve list of Users or Pages based on your search query:</div>
		<div class="param-code">
			<code><?php echo $site_url; ?>/api/v1.0/?type=search&query=QUERY&limit=LIMIT</code>
		</div>
	</div>

	<div class="api-intro">
		Examples on how to decode API data:
	</div>

	<div class="parameter-container">
		<div class="proglang-name">PHP:</div>
		<div class="param-code">
			<div class="langcode">
				&lt;?php<br>
				$json = file_get_contents("<?php echo $site_url; ?>/api/v1.0/?type=profile_data&username=1");<br>
				$data = json_decode($json, true);<br>
				print_r($data);<br>
				?>
			</div>
		</div>
	</div>

	<div class="parameter-container">
		<div class="proglang-name">Javascript (with jQuery):</div>
		<div class="param-code">
			<div class="langcode">
				var url = '<?php echo $site_url; ?>/api/v1.0/?type=profile_data&username=1';<br>
				$.getJSON(url, function (json)<br>
				{<br>
				    # your code<br>
				});<br>
			</div>
		</div>
	</div>
</body>