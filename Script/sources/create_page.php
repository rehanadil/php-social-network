<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}
if ($user['subscription_plan']['create_pages'] == 0)
{
	if ($ajax)
		$ajaxUrl = subscriptionUrl();
	else
		header('Location: ' . subscriptionUrl());
}

if ($config['page_allow_create'] == 1)
{
	$themeData['page_title'] = $lang['page_create_label'];
	$i = 0;
	$listCategories = '';

	foreach (getPageCategories() as $parent_category)
	{
		$themeData['list_parent_category_label'] = $lang[$parent_category['name']];
		$childCategories = '';

		foreach (getPageCategories($parent_category['id']) as $main_category)
		{
			$themeData['list_category_id'] = $main_category['id'];
			$themeData['list_category_name'] = $lang[$main_category['name']];

			$childCategories .= \SocialKit\UI::view('timeline/page/create/list-category-each');
		}

		$themeData['list_child_categories'] = $childCategories;
		$listCategories .= \SocialKit\UI::view('timeline/page/create/list-parent-category-each');
	}

	$themeData['list_categories'] = $listCategories;
	/* */

	$themeData['page_content'] = \SocialKit\UI::view('timeline/page/create/content');
}