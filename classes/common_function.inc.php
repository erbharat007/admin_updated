<?php
	
	function dateformat($date)
	{
			$arr=explode("/",$date);
			$y=strftime("%Y-%m-%d", @mktime(0,0,0,$arr[1],$arr[0],$arr[2]));	
			return $y;
	} 
	
	function generatePassword($length = 10)
	{
		$allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$allowedCharsNumbers = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$Vowel = 'aeiouAEIOU';
		$password_generated = "";
		if($length <8)
		{
			$length =8;
		}
		for ($index = 1; $index <= $length; $index++)
		{
			if ($index % 3 == 0)
			{
				$randomNumber = rand(1,strlen($Vowel));
				$password_generated .= substr($Vowel,$randomNumber-1,1);
			}
			else
			{
				$randomNumber = rand(1,strlen($allowedChars));
				$password_generated .= substr($allowedChars,$randomNumber-1,1);
			}
			$allowedChars = $allowedCharsNumbers;
		}
		return $password_generated;
	}

	function display_Admin_Paging_For_Admin($total_pages, $targetpage, $start, $end, $select_list_href)
    {
        $adjacents = 3;

        $limit = $end; 								//how many items to show per page
        $page = $_REQUEST['page'];

        if ($page == 0) $page = 1;					//if no page var is given, default to 1.
        $prev = $page - 1;							//prev page is page - 1
        $next = $page + 1;							//next page is page + 1
        $lastpage = @ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
        $lpm1 = $lastpage - 1;						//last page minus 1

        //$pagination = '<ul class="pagination">';

        if($lastpage > 1)
        {
            $pagination .= '<ul class="pagination">';
            //prev button
            if ($page > 1)
                $pagination.= '<li><a href="'.$targetpage.'&page='.$prev.$select_list_href.'">Prev</a></li>';
            else
                $pagination.= '<li class="disabled"><a>Prev</a></li>';

            //pages
            if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
            {
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= '<li class="active"><a>'.$counter.'</a></li>';
                    else
                        $pagination.= '<li><a href="'.$targetpage.'&page='.$counter.$select_list_href.'">'.$counter.'</a></li>';
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
            {
                //close to beginning; only hide later pages
                if($page < 1 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= '<li class="active"><a>'.$counter.'</a></li>';
                        else
                            $pagination.= '<li><a href="'.$targetpage.'&page='.$counter.$select_list_href.'">'.$counter.'</a></li>';
                    }
                    $pagination.= '<li>...</li>';
                    $pagination.= '<li><a href="'.$targetpage.'&page='.$lpm1.$select_list_href.'">'.$lpm1.'</a></li>';
                    $pagination.= '<li><a href="'.$targetpage.'&page='.$lastpage.$select_list_href.'">'.$lastpage.'</a></li>';
                }
                //in middle; hide some front and some back
                elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $pagination.= '<li><a href="'.$targetpage.'&page=1'.$select_list_href.'">1</a></li>';
                    $pagination.= '<li><a href="'.$targetpage.'&page=2'.$select_list_href.'">2</a></li>';
                    $pagination.= '<li>...</li>';
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= '<li class="active"><a>'.$counter.'</a></li>';
                        else
                            $pagination.= '<li><a href="'.$targetpage.'&page='.$counter.$select_list_href.'">'.$counter.'</a></li>';
                    }
                    $pagination.= '<li>...</li>';
                    $pagination.= '<li><a href="'.$targetpage.'&page='.$lpm1.$select_list_href.'">'.$lpm1.'</a></li>';
                    $pagination.= '<li><a href="'.$targetpage.'&page='.$lastpage.$select_list_href.'">'.$lastpage.'</a></li>';
                }
                //close to end; only hide early pages
                else
                {
                    $pagination.= '<li><a href="'.$targetpage.'&page=1'.$select_list_href.'">1</a></li>';
                    $pagination.= '<li><a href="'.$targetpage.'&page=2'.$select_list_href.'">2</a></li>';
                    $pagination.= '<li>...</li>';
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= '<li class="active"><a>'.$counter.'</a></li>';
                        else
                            $pagination.= '<li><a href="'.$targetpage.'&page='.$counter.$select_list_href.'">'.$counter.'</a></li>';
                    }
                }
            }

            //next button
            if ($page < $counter - 1)
                $pagination.= '<li><a href="'.$targetpage.'&page='.$next.$select_list_href.'">Next</a></li>';
            else
                $pagination.= '<li class="disabled"><a>Next</a></li>';
            $pagination.= '</ul>';
        }
        //$pagination .= '</ul>';
        return $pagination;
    }
	
	function getDbDateFormat($date)
	{
		$objDate = DateTime::createFromFormat('d/m/Y', $date);
		$formatedDate = $objDate->format('Y-m-d');
		return $formatedDate;
	}
	
	function getDateDifference($date1, $date2)
	{
		//$objDate1 = DateTime::createFromFormat('d/m/Y', $date1);
		//$objDate2 = DateTime::createFromFormat('d/m/Y', $date2);
		$objDate1 = new DateTime($date1);
		$objDate2 = new DateTime($date2);
		
		$interval = $objDate1->diff($objDate2);
		return $interval->days;
	}
	
	function checkCreateFolder($folderName)
	{
		if(!file_exists($folderName)) 
		{
			$flag = mkdir($folderName, 0777);
			if($flag)
			{
				return true;
			}
			else
			{
				return false;
			}
		} 
		else 
		{
			return true;
		}
	}
	function reArrayFiles(&$file_post) 
	{
		$fileArray = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
	
		/*for ($i=0; $i<$file_count; $i++) 
		{
			foreach ($file_keys as $key) 
			{
				$fileArray[$i][$key] = $file_post[$key][$i];
			}
		}*/		
		foreach ($file_post['name'] as $i => $j)
		{
			foreach ($file_keys as $key) 
			{
				$fileArray[$i][$key] = $file_post[$key][$i];
			}
		}
	
		return $fileArray;
	}