<?php
/*! Create a Sitemap dinamically
	It needs a dedicated database table and an active connection to run! */
class HFsitemap {

	function generate(){
		 $sql = "SELECT  * FROM sitemap";
		 $result = mysql_query($sql) or die("Error meanwhile creating sitemap: CHECK! Error: ".mysql_error());
		// header('Content-Type: application/xml');
		 echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		 echo "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">";
		 
		while($row = mysql_fetch_assoc($result)) {
			echo '
				<url>
				<loc>http://www.ilvideocv.it/?t='.$row['threadid'].'</loc>
				<lastmod>2008-03-23T13:36:17+00:00</lastmod>
				<priority>0.50</priority>
				<changefreq>weekly</changefreq>
				</url>'
			;
		}
		
		echo '</urlset>';
		 
	}
	
	function google(){
		echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.google.com/schemas/sitemap/.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd"></urlset>';
	}

}
