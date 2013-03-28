<?php
if (!isset($keyword)){
	$keyword='';
}
?>

<div style="width:260px;">
	<section>
		<header>
			<h1>Search</h1>
		</header>
		<article>
			<form id="searchForm" method="get" action="/mixed/search">
				<input name="keyword" type="text" value="<?php echo $keyword ?>" id="search" />
				<input type="submit" value="Search" />
			</form>
			<footer></footer>
		</article>
	</section>
</div>