<?php
/*
** Lista los datos de una tabla
** Lucho (09/2011)
*/

class Listing extends Mysql {

    private $pageNumber = 1;
	private $onclickCommand = NULL;

    private function Page($n) {
        $this->pageNumber = number($n) == 0 ? 1 : $n;
    }
	
	public function pgclick($cmd){
		$this->onclickCommand = ' onclick="' . $cmd . '"';
	}
	
	private function clickcommand($page){
		return sprintf($this->onclickCommand, $page);
	}

    public function get($table, $limit = 20, $fields = NULL, $pageNumber = 1, $conditions = NULL) {
        
		if(is_numeric($limit)):
			$this->Page($pageNumber);
			$starting = ($this->pageNumber -1) * $limit; 
		endif;
		
		$fieldNames = NULL;
        
        if (NULL == $fields):
            
            $fieldNames = "*";
            $fieldCount = "*";
        
        else:
            
            if (is_array($fields)):
                
                foreach ($fields as $field):
                
                    $fieldNames .= $field . ",";
            
                endforeach;
                
                $fieldNames = substr($fieldNames, 0, -1);
                $fieldCount = $fields[0];
				
				$fieldCount = explode(" ", $fieldCount);
				$fieldCount = $fieldCount[0];
                
            endif;
            
            if (!is_numeric($starting) || !is_numeric($limit)):
                
                Message::set("Parámetro de limite incorrecto", MESSAGE_ERROR);
				return false;
                
            endif;
            
        endif;
        
        if ($limit > 0):
            $limiting = "LIMIT {$starting}, {$limit}";
		elseif($limit == 0 || $limit == NULL):
			$limiting = "";
        endif;

        $listing  = "SELECT {$fieldNames}, ( SELECT COUNT( {$fieldCount} ) FROM {$table} {$conditions} ) AS RowCount FROM {$table} {$conditions} {$limiting}";
        $listing  = $this->Execute($listing);
	
		if(count($listing) > 0):
        	$rowCount = $listing[0]['RowCount'];
		else:
			$rowCount = 0;
		endif;
		
        if ($limit > 0):
            $params = "";
            foreach($_GET as $key => $value):
				if($key != "page"):
					$params .= '&' . urlencode(strip_tags($key)) . '=' . urlencode($value);
				endif;
			endforeach;
            
            $pages = $this->paginate(12, $rowCount, $limit);
        
            $strNext     = htmlspecialchars('>');
            $strPrior    = htmlspecialchars('<');
            $priorNumber = $pages['page'] -1;
            $nextNumber  = $pages['page'] +1;
            
			$navigationNumbers = NULL;
            $navigationPrior = '<li><a href="?page='.$priorNumber.$params.'"' . $this->clickcommand($priorNumber) . '>' . $strPrior . '</a></li>';
            $navigationNext  = '<li><a href="?page='.$nextNumber.$params.'"' . $this->clickcommand($nextNumber) . '>' . $strNext . '</a></li>';
            
            if ($pages['page'] == 1):
                $nextNumber      = $pages['page'] + 1;
                $navigationPrior = '<li class="disabled"><a href="" onclick="return!1;">'.$strPrior.'</a></li>';
                $navigationNext  = '<li><a href="?page='.$nextNumber.$params.'"' . $this->clickcommand($nextNumber) . '>' . $strNext . '</a></li>';
            endif;

            if ($pages['page'] == $pages['pages']):
                $priorNumber     = $pages['page'] -1;
                $navigationPrior = '<li><a href="?page='.$priorNumber.$params.'"' . $this->clickcommand($priorNumber) . '>' . $strPrior . '</a></li>';
                $navigationNext  = '<li class="disabled"><a href="" onclick="return!1;">'.$strNext.'</a></li>';
            endif;
            
            if ($pages['lower'] > 2):
                $firstPage = '<li><a href="?page=1'.$params.'"' . $this->clickcommand(1) . '>1...</a></li>';
			else:
				$firstPage = NULL;
            endif;
            
            if (($pages['upper'] < $pages['pages'])):
                $lastPage = '<li><a href="?page='.$pages['pages'].$params.'"' . $this->clickcommand($pages['pages']) . '>...' . $pages['pages'] . '</a></li>';
			else:
				$lastPage = NULL;
            endif;
        
            for($page = $pages['lower']; $page <= $pages['upper']; $page++):
                
                if($page == $this->pageNumber):
                    $navigationNumbers .= '<li class="active"><a href="" onclick="return!1;"> ' . $page . ' </a></li>';
                else:
                    $navigationNumbers .= '<li> <a href="?page=' . $page .$params. '"' . $this->clickcommand($page) . '>' . $page . '</a> </li>';
                endif;
                
            endfor;
            
            if($pages['pages'] > 1):
                $navigation = '<ul class="pagination mt0">'.$navigationPrior .' '. $firstPage .' '. $navigationNumbers .' '. $lastPage .' '. $navigationNext.'</ul>';
			else:
				$navigation = NULL;
            endif;
      
        endif;
        
        return array(
            "list" => $listing,
            "pagination" => $pages,
            "navigation" => $navigation
        );
    }
    
    /*
     * Paginación
     */
    
    private function paginate($index, $rowCount, $limit) {

        $totalPages  = ceil($rowCount / $limit);
        $middlePoint = ceil($index / 2);

        if ($this->pageNumber == $totalPages):
            $nextNumber = NULL;
            $backNumber = $this->pageNumber - 1;
        endif;

        if ($this->pageNumber == 1):
            $nextNumber = $this->pageNumber + 1;
            $backNumber = NULL;
        endif;

        if ($this->pageNumber > 1 && $this->pageNumber < $totalPages):
            $nextNumber = $this->pageNumber + 1;
            $backNumber = $this->pageNumber - 1;
        endif;

        if ($totalPages > $index):
            $upper	  = $index;
            $lastPage = $this->pageNumber < $totalPages ? $totalPages : "";
        else:
            $upper = $totalPages;
        endif;

        if ($this->pageNumber >= $index):

            $lower = ($this->pageNumber - $index) + 1;
            $firstPage = 1;

            if ($totalPages >= ($this->pageNumber + $middlePoint)):
                $lower = $this->pageNumber - $middlePoint;
                $upper = $this->pageNumber + $middlePoint;
            else:
                $upper = $totalPages;
                $lower = $totalPages - $index;
            endif;
        else:
            $lower = 1;
            $firstPage = "";
        endif;
		
		$index = isset($index) ? $index : 0;
		$upper = isset($upper) ? $upper : 0;
		$lower = isset($lower) ? $lower : 0;
		$backNumber = isset($backNumber) ? $backNumber : 0;
        $nextNumber = isset($nextNumber) ? $nextNumber : 0;
		
		
        return array(
            "index" => $index,
            "upper" => $upper,
            "lower" => $lower,
            "prior" => $backNumber,
            "next"  => $nextNumber,
            "pages" => $totalPages,
            "page"  => $this->pageNumber,
            "rows"  => $rowCount
        );
        
    }

}

?>
