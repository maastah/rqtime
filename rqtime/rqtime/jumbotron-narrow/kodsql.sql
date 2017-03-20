CREATE TABLE `urls` (
	`user_url_id` bigint NOT NULL AUTO_INCREMENT,
	`user_url` varchar(200) NOT NULL,
	`user_url_data` DATE NOT NULL,
	PRIMARY KEY (`user_url_id`)
);

CREATE TABLE `sitemaps` (
	`sm_url_id` bigint NOT NULL AUTO_INCREMENT,
	`main_url` varchar(200) NOT NULL,
	`sitemap_urls` TEXT NOT NULL,
	`sitemap_url_date` DATE NOT NULL,
	`sitemap_url_rt` FLOAT NOT NULL,
	PRIMARY KEY (`sm_url_id`)
);


