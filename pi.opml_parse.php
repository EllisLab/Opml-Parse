<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
						'pi_name'			=> 'OPML Parser',
						'pi_version'		=> '1.1',
						'pi_author'			=> 'Rick Ellis',
						'pi_author_url'		=> 'http://www.expressionengine.com/',
						'pi_description'	=> 'Permits you parse an OPML file and show its contents as a lit of links.',
						'pi_usage'			=> Opml_parse::usage()
					);

/**
 * Opml_parse Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			ExpressionEngine Dev Team
 * @copyright		Copyright (c) 2005 - 2009, EllisLab, Inc.
 * @link			http://expressionengine.com/downloads/details/opml_parser/
 */

class Opml_parse {

    var $return_data 	= '';
	var $tag_data		= '';
	var $opml_data		= '';
    
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */

    function Opml_parse($str = '')
    {
        $this->EE =& get_instance();

        $this->tag_data = ($str == '') ? $this->EE->TMPL->tagdata : $str; 
                        
		if ( ! ($file = $this->EE->TMPL->fetch_param('file_path')))
		{
			return '';
		}

		if ( ! is_file($file))
		{
			return '';
		}
		
		$this->filedata = implode("", file($file));
				
		if ($this->parse_xml() === FALSE)
		{
			return '';
		}
		
		$this->parse_tag();
    }

	// --------------------------------------------------------------------   

	/**
	* Parse xml
	*
	* Parse the xml
	*
	* @access   public
	* @return   type
	*/
	function parse_xml()
	{
		$parser = xml_parser_create();		
		if (xml_parse_into_struct($parser, $this->filedata, $elements, $index) == 0)
		{
			return FALSE;
		}
		
		xml_parser_free($parser);
		
		if (count($elements) == 0)
		{
			return FALSE;
		}		
		
		foreach ($elements as $val)
		{
			if ($val["tag"] == "OUTLINE")
			{
				$this->opml_data[] = $val["attributes"];
			} 
		}
	}	

	// --------------------------------------------------------------------

    
	/**
	* Parse tag
	*
	* @access   public
	* @return   string
	*/
	function parse_tag()
	{
		$limit = ( ! $this->EE->TMPL->fetch_param('limit')) ? FALSE : $this->EE->TMPL->fetch_param('limit');
		
		$vars = array('text', 'description', 'title', 'type','htmlurl', 'xmlurl');

		$output = '';
		$i = 0;
		foreach ($this->opml_data as $key => $val)
		{
			if ($limit !== FALSE AND $i == $limit)
				break;
		
			$temp = $this->tag_data;
			
			foreach ($vars as $xml)
			{
				if (isset($val[strtoupper($xml)]))
					$temp = str_replace(LD.$xml.RD, $val[strtoupper($xml)], $temp);
			}
			
			$output .= $temp;
			$i++;
		}

		if ($back = $this->EE->TMPL->fetch_param('backspace'))
		{
			if (is_numeric($back))
			{
				$output = substr(rtrim($output), 0, - $back);
			}
		}
		
		$this->return_data = $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Usage
	 *
	 * Plugin Usage
	 *
	 * @access	public
	 * @return	string
	 */
	function usage()
	{
		ob_start(); 
		?>
		This plugin lets you parse an OPML file and show its contents.  I wrote it to turn an exported OPML file from my RSS reader into a blogroll.

		Here's the basic prototype:

		{exp:opml_parse file_path="path/to/file.opml"}
		<a href="{htmlurl}">{title}</a><br />
		{/exp:opml_parse}

		There are two optional parameters:

		limit="10" - the number of rows you want returned:
		backspace="6" - the number of characters to be trimmed off the end.

		The following variables are permitted:

		{text}
		{description}
		{title}
		{type}
		{htmlurl}
		{xmlurl}


		Version 1.1
		******************
		- Updated plugin to be 2.0 compatible


		<?php
		$buffer = ob_get_contents();
	
		ob_end_clean(); 

		return $buffer;
	}
	
	// --------------------------------------------------------------------

}
// END CLASS

/* End of file pi.opml_parser.php */
/* Location: ./system/expressionengine/third_party/opml_parser/pi.opml_parser.php */