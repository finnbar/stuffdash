<html>
	<head>
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" media="screen" href="https://fontlibrary.org/face/hanken" type="text/css"/>
		<?php
			if(isset($_GET["topic"])) {
				$topic = ucfirst($_GET["topic"]);
			} else {
				echo "<script type='text/javascript'>window.onload = function() {
					var v = prompt('Choose a topic!');
					window.location.assign(window.location+'?topic='+v);
				}</script>";
				die();
			}	
			require 'vendor/autoload.php';
			require_once 'phpflickr/phpFlickr.php';
			$tumblr = new Tumblr\API\Client("qhYmyXA6yFv0he9ypyCcDtZWmEdjEHkQtn7Wb9IYmu5BFvpKdc","nPBm7DEkXnEiLn5Mb4vVCZVkYM0CtLPmh1NOqUu7utxPp9Dklc");
			$flickr = new phpFlickr("feede905ba55d80e6df298038f92b78b");

			$summarylink = "https://simple.wikipedia.org/w/api.php?action=query&titles=" . $topic ."&format=json&prop=extracts&redirects";
			$summaryresponse = \Httpful\Request::get($summarylink)
				->expectsJson()
				->send();
			$pagenumber = array_keys((array) $summaryresponse->body->query->pages)[0];
			$extract = $summaryresponse->body->query->pages->$pagenumber->extract;
			preg_match("/^.+?\n/",$extract,$summary);

			$photos = $flickr->photos_search(array("tags"=>$topic,"sort"=>"relevance"));
			$photourl = "https://farm1.staticflickr.com/".$photos["photo"][0]["server"]."/".$photos["photo"][0]["id"]."_".$photos["photo"][0]["secret"].".jpg";
			
			\Codebird\Codebird::setConsumerKey("PXOBEnYwKZgeWvY0G6TLenFNk","VkGoqY4vxSGpPc790lvOpsDbh7w95ZPDDAK3sz6rSQ320RkdE3");
			$cb = \Codebird\Codebird::getInstance();
			$stats = $cb->search_tweets("q=#".$topic,true)->statuses;
		?>
		<title><?php echo $topic; ?></title>
	</head>
	<body>
		<div class="container">
			<div class = "row" id="topbar">
				<div class = "col-md-12">
					<img src="images/logostuff.png" height="30px" />
					<a href="/"><img src="images/Logomain.png" height="30px" /></a>
					<a href="#"><img src="images/share button.png" height="30px" id="sharer" /></a>
				</div>
			</div>
			<div class = "row" id="heading">
				<div id = "Header" class = "col-md-12"></div>
			</div>
			<div class = "row">
			  <div id = "Left" class = "col-md-4">
				<div id = "Photo"><img src='<?php echo $photourl; ?>' width="100%" /></div>
				<div id = "Description">
				  <div>
					<h3><?php echo "<h1>" . $topic . "</h1>"; ?></h3>
				  </div>
				  <div>
					<p><?php echo $summary[0]; ?></p>
				  </div>
				</div>
			  </div>
				<div id = "Middle" class = "col-md-4">
				  <div class="feed2">
				  <p>Tweets</p>
				  	<?php
				  		foreach ($stats as $stat) {
				  			echo "<h4>".$stat->user->name."</h4>";
				  			echo "<p>".$stat->text."</p>";
				  		}
				  	?>
				  </div>
				</div>
			  <div id = "Right" class = "col-md-4">
				<div>
					<p>Images from Tumblr</p>
					<?php
						$max_posts = 10;
						$posts = $tumblr->getTaggedPosts($topic);
						foreach ($posts as $post) {
							if(property_exists($post, "photos")) {
								echo "<img src='" . $post->photos[0]->alt_sizes[0]->url . "' width='100%' />";
								$max_posts--;
								if($max_posts == 0) {
									break;
								}
							}
						}
					?>
				</div>
			  </div>
			</div>
		</div>
	</body>
</html>