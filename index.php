<?php
include_once "config/header-general.php";
?>
<div class="sub-content">
	<div id="index-about">
		<h1>About Project Shoujo</h1>
		<p>Project Shoujo aims to be a web-based real time dating simulator.</p>
		<p>Right now there are only 2 playable characters. We plan on adding more, and eventually letting users create their own on an easy-to-use editor.</p>
		<p>Playing is and will always be free. The goal is to make character creation free too. We still don't know how big this is going to be, so we can't promise to make character creation free in the near future.</p>
		<p>We are looking for writers and artists who know how to draw and are willing to participate, so we can keep adding new content to our current characters and adding new ones. You can contact us and send questions/portfolios to <a href="mailto:f.leboran@gmail.com">f.leboran@gmail.com</a>.</p>
	</div>
	<h2>Latest characters</h2>
	<?php generate_shoujo_box(4, constants::shoujo_box_big) ?>
</div>
<?php
include_once "config/footer-general.php";
?>