<?php

$ads = "
    CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}money_ads ( 
        id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        title VARCHAR(50) NOT NULL ,
        description VARCHAR(250) NOT NULL, 
        content TEXT NOT NULL ,
        url VARCHAR(150) NOT NULL ,
        price VARCHAR(10) NOT NULL ,
        content_type VARCHAR(10) NOT NULL , 
        strategy_type VARCHAR(10) NOT NULL , 
        strategy_value VARCHAR(10) NOT NULL , 
        display_when VARCHAR(20) NOT NULL , 
        display_on VARCHAR(10) NOT NULL , 
        display_on_pages VARCHAR(250) NOT NULL , 
        display_on_posts VARCHAR(250) NOT NULL , 
        complexity VARCHAR(10) NOT NULL , 
        advanced_timer VARCHAR(20) NOT NULL , 
        advanced_action VARCHAR(20) NOT NULL , 
        advanced_url VARCHAR(250) NOT NULL , 
        advanced_text VARCHAR(100) NOT NULL , 
        style_shadow VARCHAR(3) NOT NULL , 
        style_responsive VARCHAR(3) NOT NULL , 
        style_position VARCHAR(10) NOT NULL , 
        style_top VARCHAR(20) NOT NULL , 
        style_left VARCHAR(20) NOT NULL , 
        style_right VARCHAR(20) NOT NULL , 
        style_bottom VARCHAR(20) NOT NULL , 
        style_width VARCHAR(20) NOT NULL , 
        style_height VARCHAR(20) NOT NULL
    )";

$adsDemo = "
    CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}money_ads_demo ( 
        id BIGINT(20) NOT NULL AUTO_INCREMENT , 
        ad_id BIGINT(20) NOT NULL,
        PRIMARY KEY (id)
    )";

$adsSold = "
    CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}money_ads_sold ( 
        id BIGINT(20) NOT NULL AUTO_INCREMENT , 
        ad_id BIGINT(20) NOT NULL,
        ad_demo_id BIGINT(20) NOT NULL,
        buyer_id BIGINT(20) NOT NULL,
        statistic_id BIGINT(20) NOT NULL,
        date_purchase DATETIME NOT NULL, 
        date_start DATETIME NOT NULL,
        date_end DATETIME NOT NULL,
        paypal_sale_id VARCHAR(250) NOT NULL,
        paypal_payment_id VARCHAR(250) NOT NULL,
        paypal_payer_id VARCHAR(250) NOT NULL,
        status VARCHAR(20) NOT NULL,
        hash VARCHAR(250) NOT NULL,
        refund_amount VARCHAR(20) NOT NULL,
        currency VARCHAR(10) NOT NULL , 
        PRIMARY KEY (id)
    )";

$statistics = "
    CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}money_statistics ( 
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        views_per_day LONGTEXT NOT NULL,
        clicks_per_day LONGTEXT NOT NULL,
        countries LONGTEXT NOT NULL,
        total_days BIGINT(20),
        total_views BIGINT(20),
        total_clicks BIGINT(20),
        PRIMARY KEY (id)
    )";