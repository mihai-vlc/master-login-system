<?php
/**
* Pagination class 
* @author ionutvmi@gmail.com
* master login system
*
*/

class pagination {
	var $pages = null;

	function __construct($total, $page, $perpage = 10){
		$total_pages = ceil($total/$perpage);
		$query = '';
		foreach($_GET as $k=>$v)
			if($k != 'page')
				$query .= "&$k=$v";

		$this->pages = "<div class='pagination'><ul>";

		if($page > 4)
			$this->pages .= "<li><a href='?$query'>First</a></li>";

		if($page > 1)
			$this->pages .= "<li><a href='?page=".($page-1)."$query'>Prev</a> </li>";

		for($i = max(1, $page - 3); $i <= min($page + 3, $total_pages); $i++)
			$this->pages .= ($i == $page ? "<li class='active'><a>".$i."</a></li>" : " <li><a href='?page=$i$query'>$i</a></li> ");

		if($page < $total_pages)
			$this->pages .= "<li><a href='?page=".($page+1)."$query'>Next</a></li>";

		if($page < $total_pages-3)
			$this->pages .= "<li><a href='?page=$total_pages$query'> Last </a></li>";

		$this->pages .= "</ul></div>";

		return true;
	}
}