<?php
/**
 * This file contains a subclass of the Zend_Search_Lucene_Analysis_Analyzer_Standard class.
 * Its purpose is to help provide a corresponding PHP implementation of the Standard analyzer for
 * the Java implementation of Lucene. This Analyzer, in conjunction with the filters also provided
 * in this standard analyzer package, provide a method for indexing documents with word Stemming,
 * lower-casing, and number handling. The lower-case and number handling is provided by the pre-
 * existing filters from Zend.
 * 
 * License: see License.txt for a copy of the Zend License.
 *
 *Ref:
 * http://hudson.zones.apache.org/hudson/job/Lucene-trunk/javadoc//org/apache/lucene/analysis/standard/StandardAnalyzer.html
 *
 * @category   PHP_Analyzer_Standard
 */

 /** StandardAnalyzer_ */
 /* Depending on your circumstances, you may want to change the paths to meet your conventional / functional needs */

require_once 'StandardAnalyzer/Analyzer/Standard.php';
require_once 'StandardAnalyzer/TokenFilter/EnglishStemmer.php';

 /** Zend_Search_Lucene_Analysis_Analyzer_Standard */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';
/** Zend_Search_Lucene_Analysis_TokenFilter_LowerCase */
require_once 'Zend/Search/Lucene/Analysis/TokenFilter/LowerCase.php';
/** Zend_Search_Lucene_Analysis_TokenFilter_StopWords */
require_once 'Zend/Search/Lucene/Analysis/TokenFilter/StopWords.php';

class StandardAnalyzer_Analyzer_Standard_English extends StandardAnalyzer_Analyzer_Standard
{
	private $_stopWords = array ("a", "an", "and", "are", "as", "at", "be", "but", "by", "for", "if", "in", "into", "is", "it", "no", "not", "of", "on", "or", "s", "such", "t", "that", "the", "their", "then", "there", "these", "they", "this", "to", "was", "will", "with");
	
    public function __construct()
    {
        $this->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_LowerCaseUtf8());
        $this->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_StopWords($this->_stopWords));
        $this->addFilter(new StandardAnalyzer_Analysis_TokenFilter_EnglishStemmer());	
    }
}

