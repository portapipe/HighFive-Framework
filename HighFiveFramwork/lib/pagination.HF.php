<?
	
class HFpagination{
	
	public $resultsPerPage = 6 ; //Number of results per page
	public $pag = 1; //The actual page
	public $sql = ""; //sql needed for create the query
	public $maxPages = 5;
	public $pageLink = '?p=$page';


	function setPage($num){
		$this->pag = $num;
		return $this;
	}

	/*! Set results per page, the number */
	function setResultsPerPage($num){
		$this->resultsPerPage = $num;
		return $this;
	}
	
	/*! Set the max pages viewable, default 5
		ex. if you are at page 2: [1]{2}[3][4][5][6] only if there will be enough data
	*/
	function setMaxPages($num){
		$this->maxPages = $num;
		return $this;
	}
	
	/*! Just pass an SQL select query WITHOUT! the LIMIT! to make all automated */
	function setSql($sql){
		$this->sql = $sql;
		return $this;
	}
	
	/*! IMPORTANT! Use $page in the link where you want the page link, it will be automagically replaced with the correct page number
		If you want to use the onclick="" just set the $link with: onclick="makeThings();" - Just the way you'll add it to the code :)
	*/
	function setPageLink($link){
		$link = trim($link);
		if(strpos($link,'onclick="') !== false && $link[0] == "o" && $link[2] == "c"){
			$link = '" '.$link;
			$link = substr($link, 0, (strlen($link)-1) );
		}
		$this->pageLink = $link;
		return $this;
	}
	
	function getSqlLimit(){
		$limit = ($this->pag - 1) * $this->resultsPerPage;
		return " LIMIT $limit,".($this->resultsPerPage * $this->maxPages);
	}
	
	
	function getLimit(){
		$limit = ($this->pag - 1) * $this->resultsPerPage;
		return $limit;
	}
	
	
	function fixLink($pageNum){
		return str_replace('$page', $pageNum, $this->pageLink);
	}
	
	function generate(){
		
		$pag = $this->pag;
		//Starting record for queries based on the actual page and results for page choosen
		$limit = ($pag - 1) * $this->resultsPerPage;
	
		
		//Query just for pagination's numbers
		$tot =  sqlToArray($this->sql." LIMIT $limit,".$this->resultsPerPage * $this->maxPages);
		$pagPerPagination = count($tot) / $this->resultsPerPage;
		$pagPerPagination = ceil($pagPerPagination);
		
        $pagination = '
            <div class="col-sm-12">
                <div align="center">
                    <ul class="pagination">
                    	<!-- Pagination Prev -->
                        <li class="'.($pag==1?"disabled":"").'">
                        	<a href="'.($pag==1?'" onclick="return false;"':$this->fixLink($pag-1)).'">«</a>
                        </li>
                       
                        ';

					if($pag != 1){
						for($i = 2; $i > 0;$i--){
							if(($pag-$i)>0){
								$pagination .= '
			                        <li><a href="'.$this->fixLink($pag-$i).'">'.($pag - $i).'</a></li>
			                    ';
			                }
	                    }
					}
					
					for($i = $pag; $i < ($pagPerPagination + $pag);$i++){
						if($i!=0){
							if($pag==$i){
								$pagination .= '               
			                        	<!-- Active Page -->
			                        	<li class="active"><a href="" onclick="return false;">'.$i.' <span class="sr-only">(current)</span></a></li>
			                        
								';
							}else{
								$pagination .= '               
			                       <li><a href="'.$this->fixLink($i).'">'.$i.'</a></li>
								';
							}
		                }
                    }
                    
                    
                    //<li><a href="#">2</a></li>
	                //<li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
	                
        $pagination .= '<!-- Next Page -->
        				<li class="'.($pag>=($pagPerPagination+$pag-1)?"disabled":"").'">
        					<a href="'.($pag>=($pagPerPagination+$pag-1)?'" onclick="return false;"':$this->fixLink($pag+1)).'">»</a>
        				</li>
                    </ul>
                </div>
            </div>
            ';
	
	
			return $pagination;
	
	}
	
	
}