<?php
if ($continue)
{
	$results = getUserSuggestionsInfo('', 50);
	$suggestions = array();

	if (is_array($results))
	{
		foreach ($results as $suggId)
		{
			$suggestionObj = new \SocialKit\User();
			$suggestionObj->setId($suggId);
			$suggestion = $suggestionObj->getRows();

			$suggestions[] = array(
		    	"id" => $suggestion['id'],
				"username" => $suggestion['username'],
				"name" => $suggestion['name'],
				"image" => $suggestion['avatar_url'],
				"cover" => $suggestion['cover_url'],
				"birthday" => $suggestion['birthday'],
				"gender" => $suggestion['gender'],
				"location" => $suggestion['location'],
				"hometown" => $suggestion['hometown'],
				"about" => $suggestion['about'],
				"time" => $suggestion['last_logged'],
				"color" => $suggestion['color']
		    );
		}

		$data = array(
			"status" => 200,
			"results" => $suggestions
		);
	}
}