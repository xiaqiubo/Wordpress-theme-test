<?php

	//注册小工具栏register_sidebar()只能注册一个小工具
	register_sidebar(
		array(
		'name'=>'新闻列表',
		'description'=>'新闻列表页的侧边栏',
		'before_widget'=>'<div class="block clearfix">',
		'after_widget'=>'</div>',
		'before_title'=>'<h3 class="title">',
		'after_title'=>'</h3>',
		)
	);
	register_sidebar(
		array(
			'name'           =>'文章页侧边栏',
			'before_widget'  =>'<div class="panel panel-primary">',
			'after_widget'   =>'</div>',
			'before_title'   =>'<div class="panel-heading"><h3 class="panel-title">',
			'after_title'    =>'</h3></div>'
		)
	);
	

	
	//添加特色图像功能
	add_theme_support('post-thumbnails');

	//添加友情链接栏目代替link mannager插件
	add_filter( 'pre_option_link_manager_enabled', '__return_true' );
	
	// WordPress 添加面包屑导航 
	function fengqing_breadcrumbs() {
		$delimiter = '&nbsp;/&nbsp;'; // 分隔符
		$before = '<span class="current">'; // 在当前链接前插入
		$after = '</span>'; // 在当前链接后插入
		if ( !is_home() && !is_front_page() || is_paged() ) {
			echo '<div class="breadcrumb" id="crumbs">'.'当前位置：';
			global $post;
			$homeLink = home_url();
			echo ' <i class="fa fa-home pr-10"></i><a itemprop="breadcrumb" href="'. $homeLink .'">首页 </a> ' . $delimiter . ' ';
			if ( is_category() ) { // 分类 存档
				global $wp_query;
				$cat_obj = $wp_query->get_queried_object();
				$thisCat = $cat_obj->term_id;
				$thisCat = get_category($thisCat);
				$parentCat = get_category($thisCat->parent);
				if ($thisCat->parent != 0){
					$cat_code = get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');
					echo $cat_code = str_replace ('<a','<a itemprop="breadcrumb"', $cat_code );
				}
				echo $before . '' . single_cat_title('', false) . '' . $after;
			} elseif ( is_day() ) { // 天 存档
				echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
				echo '<a itemprop="breadcrumb"  href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
				echo $before . get_the_time('d') . $after;
			} elseif ( is_month() ) { // 月 存档
				echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
				echo $before . get_the_time('F') . $after;
			} elseif ( is_year() ) { // 年 存档
				echo $before . get_the_time('Y') . $after;
			} elseif ( is_single() && !is_attachment() ) { // 文章
				if ( get_post_type() != 'post' ) { // 自定义文章类型
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					echo '<a itemprop="breadcrumb" href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
					echo $before . get_the_title() . $after;
				} else { // 文章 post
					$cat = get_the_category(); $cat = $cat[0];
					$cat_code = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
					echo $cat_code = str_replace ('<a','<a itemprop="breadcrumb"', $cat_code );
					echo $before . get_the_title() . $after;
				}
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				echo $before . $post_type->labels->singular_name . $after;
			} elseif ( is_attachment() ) { // 附件
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID); $cat = $cat[0];
				echo '<a itemprop="breadcrumb" href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
				echo $before . get_the_title() . $after;
			} elseif ( is_page() && !$post->post_parent ) { // 页面
				echo $before . get_the_title() . $after;
			} elseif ( is_page() && $post->post_parent ) { // 父级页面
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<a itemprop="breadcrumb" href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
				echo $before . get_the_title() . $after;
			} elseif ( is_search() ) { // 搜索结果
				echo $before ;
				printf( __( '搜索结果: %s', 'fengqing' ),  get_search_query() );
				echo  $after;
			} elseif ( is_tag() ) { //标签 存档
				echo $before ;
				printf( __( '标签存档: %s', 'fengqing' ), single_tag_title( '', false ) );
				echo  $after;
			} elseif ( is_author() ) { // 作者存档
				global $author;
				$userdata = get_userdata($author);
				echo $before ;
				printf( __( '作者: %s', 'fengqing' ),  $userdata->display_name );
				echo  $after;
			} elseif ( is_404() ) { // 404 页面
				echo $before;
				_e( '您已丢失', 'fengqing' );
				echo  $after;
			}
			if ( get_query_var('paged') ) { // 分页
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() )
					echo sprintf( __( '( Page %s )', 'fengqing' ), get_query_var('paged') );
			}
			echo '</div>';
		}
	}
	//获取文章别名
	function the_slug() {
	    $post_data = get_post($post->ID, ARRAY_A);
	    $slug = $post_data['post_name'];
	    return $slug; 
	}

?>