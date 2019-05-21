<?php
namespace App\Helpers;


class StringHelper {

    /**
     * Formats a string from 'browser-usage' to 'browserUsage'
     * @param $text String
     * @return $text String
     */
    public static function formatDashesToCamelCase(String $text) {
        //find the position of the '-'
        $pos = strpos($text, '-');
        if($pos > 0) {
        //explode the string based on dashes
        $explode = explode('-',$text);
            $text = '';
            foreach($explode as $i => $word) {
                //leave the first word as it is but upperCase the others
                if($i == 0) {
                    $text .= $word;
                } else {
                    $text .= ucfirst($word);
                }
            }
        }
        return $text;
    }

    /**
     * Converts camelCase string to have spaces between each.
     * @param $camelCaseString
     * @return string
     *
     *   (?<=[a-z])      # Position is after a lowercase,
     *   (?=[A-Z])       # and before an uppercase letter.
     */
    public static function formatCamelCaseToSpaces($camelCaseString) {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        return join($a, " " );
    }

	/**
	 * Add slashes and remove new lines to the content
	 * @param $content
	 * @return string
	 */
	public static function addSlashesRemoveNewLines( $content )
	{
		// Add slashes and remove new lines to the content
		$content = addslashes( preg_replace( "!\s+!", " ", str_replace ( array( "\r\n", "\n", "\r"), " ", $content ) ) );
		return $content;
	}


	/**
	 * @param string $string
	 * @return string
	 */
	public static function snakeCaseToTitle( string $string )
	{
		return ucwords( str_replace('_', ' ', $string) );
	}


	/**
	 * @param $number
	 * @return mixed
	 */
	public static function formatNumber( $number )
	{
		if(is_numeric( $number ))
			$number = number_format($number, 4);

		// Remove any erroneous floats
		$number = str_replace('.0000', '', $number);

		return $number;
	}


	/**
	 * @param array $theArray
	 * @return string
	 */
	public static function arrayToList(array $theArray)
	{
		$list = '<ul>';

		foreach( $theArray as $key => $value){
			if( is_array($value) ){
				$list .= '<li>' .self::snakeCaseToTitle($key) .': ';
				$list .= self::arrayToList( $value );
				$list .= '</li>';
			}else{
				if (strpos($key, 'memory') !== false) {
					$list .= '<li><strong>' .self::snakeCaseToTitle($key) .'</strong>: ' .FileHelper::bytesToReadable($value) .'</li>';
				}elseif (strpos($key, 'buffer') !== false) {
					$list .= '<li><strong>' .self::snakeCaseToTitle($key) .'</strong>: ' .FileHelper::bytesToReadable($value) .'</li>';
				}elseif (strpos($key, 'time') !== false) {
					$list .= '<li><strong>' .self::snakeCaseToTitle($key) .'</strong>: ' .\App\Helpers\DateTime::long(date ("Y-m-d H:i:s", $value), true) .' UTC</li>';
				}else{
					$list .= '<li><strong>' .self::snakeCaseToTitle($key) .'</strong>: ' .self::formatNumber( $value ) .'</li>';
				}
			}
		}

		$list .= '</ul>';

		return $list;
	}


	/**
	 * @param array $theArray
	 * @param string $title
	 * @return string
	 */
	public static function arrayToHiddenList(array $theArray, $title = 'Click to view', $id=0)
	{
		$list = '<div class="panel-group" role="tablist"><div class="panel panel-default" style="background-color:transparent;"><div role="tab" id="collapseListGroupHeading' .$id .'"> 
			<strong href="#collapseListGroup' .$id .'" class="collapsed" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapseListGroup' .$id .'">
				' .$title .'
			</strong> 	
		</div> 
		
		<div class="panel-collapse collapse" role="tabpanel" id="collapseListGroup' .$id .'" aria-labelledby="collapseListGroupHeading' .$id .'" aria-expanded="false" > 
			<ul class="list-group">';

			foreach( $theArray as $key => $value){
				if( is_array($value) ){
					$list .= '<li class="list-group-item"  style="background-color:transparent;"><strong>' .self::snakeCaseToTitle($key) .'</strong>: ';
					$list .= self::arrayToList( $value );
					$list .= '</li>';
				}else{
					$list .= '<li class="list-group-item" style="background-color:transparent;"><strong>' .self::snakeCaseToTitle($key) .'</strong>: ' .self::formatNumber( $value ) .'</li>';
				}
			}

		$list .= '</ul></div></div></div>';

		return $list;
	}


	/**
	 * Multidimensional Array To HTML Table
	 * Will take an array and turn it into a table with all the beatification we can add
	 * The array must be key value pairs and the value can be an array of values
	 * @param array $theArray
	 * @param bool $bordered
	 * @return string
	 */
	public static function multidimensionalArrayToTable( array $theArray, $titles=[], $datatable=false, $bordered=false, $hover=false, $striped=false)
	{
		// Table classes
		$classes = 'table';
		$classes .= $bordered ? ' table-bordered' : '';
		$classes .= $hover ? ' table-hover' : '';
		$classes .= $striped ? ' table-striped' : '';

		if( !empty($titles)) // Must have titles to have a datatable
			$classes .= $datatable ? ' datatable' : '';

		// Start the table
		$table = '<table class="' .$classes .'">';

		// Create the header
		$table .= self::createHeader($titles);

		// Start the body
		$table .= '<tbody>';

		// Loop through the array and create the main table
		foreach($theArray as $key => $value){

			$table .= '<tr><td width="20%">' .self::snakeCaseToTitle($key) .'</td>';

			if( is_array($value) ){

				$table .= '<td width="80%">';

				// If there are too many to show as a view, hide them
				if( count($value) < 15 ){
					$table .= self::arrayToList( $value );
				}else{
					$title = trans('admin.click-to-show');
					$id = $key;
					$table .= self::arrayToHiddenList($value, $title, $id);
				}
				$table .= '</td>';

			}elseif( is_numeric($value) ){
				$table .= '<td><strong>' .self::formatNumber( $value ) .'</strong></td>';
			}elseif( $value === 1 || $value === true || $value === '1' || $value === 'true' ){
				$table .= '<td><strong>' .trans('admin.true') .'</strong></td>';
			}elseif( $value === 0 || $value === false ){
				$table .= '<td><strong>' .trans('admin.false') .'</strong></td>';
			}else{
				$table .= '<td><strong>' .$value .'</strong></td>';
			}

			$table .= '</tr>';
		}
		$table .= '</tbody>';
		$table .= '</table>';

		// Return a fully html table
		return $table;
	}


	/**
	 * Create a header for any table
	 * @param $titles
	 * @return string
	 */
	private static function createHeader( $titles )
	{
		$header = '';

		if( !empty($titles)) {
			$header .= '<thead><tr>';
			foreach ($titles as $title)
				$header .= '<th>' . $title . '</th>';
			$header .= '</tr></thead>';
		}

		return $header;
	}


	/**
	 * Check whether two strings are a "good enough" match.
	 * It compares strings using a case-insensitive match first.
	 * If that fails and the third parameter is not 0 (the default), it calculates whether the Levenshtein distance is within that value.
	 * @param $string1 - String to compare
	 * @param $string2 - Other string to compare
	 * @param $distance - Maximum levenshtein distance for acceptability
	 * @return Boolean indicating whether the match is acceptable
	 */
	public static function matchIsAcceptable( $string1, $string2, $distance = 0 )
	{
		//Assume there is no match, then see if they do. This approach is easier to extend for complex validation.
		$acceptable = false;
		// The first step is to support a case-insensitive match (we won't bother with a "raw" match)
		$icaseString1 =  strtolower( $string1 );
		$icaseString2 = strtolower( $string2 );
		if ( $icaseString1 === $icaseString2 ) {
			$acceptable = true;
		}
		// Only calculate the Levenshtein distance if the case-insensitive comparison wasn't acceptable and some difference is acceptable.
		// (A Levenshtein distance of 0 would indicate no difference).
		if ( !$acceptable && ( $distance > 0 ) ){
			// Calculate the Levenshtein distance between the two values, which is a measure of the number of changes
			// We may need to convert the strings from UTF8 to extended ASCII to better cater for accented characters
			// but for now we will ignore that complexity because the comparison is already quite slow.
			// Note that we use the case-insensitive versions as input to the function.
			;
			if ( levenshtein($icaseString1, $icaseString2) <= $distance ) {
				$acceptable = true;
			}
		}
		return $acceptable;

	}
}