<?php  
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Grabs a list of states and provinces commonly used in web forms.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
defined('C5_EXECUTE') or die(_("Access Denied."));
class ListsStatesProvincesHelper {
	
	private $stateProvinces = array(
	'US' => array(
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'DC' => 'Washington, DC',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming'
	),
	
	'CA' => array(
		'AB' => 'Alberta',
		'BC' => 'British Columbia',
		'MB' => 'Manitoba',
		'NB' => 'New Brunswick',
		'NF' => 'Newfoundland',
		'NT' => 'Northwest Territories',
		'NS' => 'Nova Scotia',
		'NU' => 'Nunavut',
		'ON' => 'Ontario',
		'PE' => 'Prince Edward Island',
		'QC' => 'Quebec',
		'SK' => 'Saskatchewan',
		'YT' => 'Yukon'
	),
	
	'AU' => array(
		'AAT' => 'Australian Antarctic Territory',
		'ACT' => 'Australian Capital Territory',
		'NT' => 'Northern Territory',
		'NSW' => 'New South Wales',
		'QLD' => 'Queensland',
		'SA' => 'South Australia',
		'TAS' => 'Tasmania',
		'VIC' => 'Victoria',
		'WA' => 'Western Australia',
	),

	'UK' => array(
		'AVON' => 'Avon',
		'BEDS' => 'Bedfordshire',
		'BERKS' => 'Berkshire',
		'BUCKS' => 'Buckinghamshire',
		'CAMBS' => 'Cambridgeshire',
		'CHESH' => 'Cheshire',
		'CLEVE' => 'Cleveland',
		'CORN' => 'Cornwall',
		'CUMB' => 'Cumbria',
		'DERBY' => 'Derbyshire',
		'DEVON' => 'Devon',
		'DORSET' => 'Dorset',
		'DURHAM' => 'Durham',
		'ESSEX' => 'Essex',
		'GLOUS' => 'Gloucestershire',
		'GLONDON' => 'Greater London',
		'GMANCH' => 'Greater Manchester',
		'HANTS' => 'Hampshire',
		'HERWOR' => 'Hereford & Worcestershire',
		'HERTS' => 'Hertfordshire',
		'HUMBER' => 'Humberside',
		'IOM' => 'Isle of Man',
		'IOW' => 'Isle of Wight',
		'KENT' => 'Kent',
		'LANCS' => 'Lancashire',
		'LEICS' => 'Leicestershire',
		'LINCS' => 'Lincolnshire',
		'MERSEY' => 'Merseyside',
		'NORF' => 'Norfolk',
		'NHANTS' => 'Northamptonshire',
		'NTHUMB' => 'Northumberland',
		'NOTTS' => 'Nottinghamshire',
		'OXON' => 'Oxfordshire',
		'SHROPS' => 'Shropshire',
		'SOM' => 'Somerset',
		'STAFFS' => 'Staffordshire',
		'SUFF' => 'Suffolk',
		'SURREY' => 'Surrey',
		'SUSS' => 'Sussex',
		'WARKS' => 'Warwickshire',
		'WMID' => 'West Midlands',
		'WILTS' => 'Wiltshire',
		'YORK' => 'Yorkshire'
	),
	
	'EI' => array(
		'CO ANTRIM' => 'County Antrim',
		'CO ARMAGH' => 'County Armagh',
		'CO DOWN' => 'County Down',
		'CO FERMANAGH' => 'County Fermanagh',
		'CO DERRY' => 'County Londonderry',
		'CO TYRONE' => 'County Tyrone',
		'CO CAVAN' => 'County Cavan',
		'CO DONEGAL' => 'County Donegal',
		'CO MONAGHAN' => 'County Monaghan',
		'CO DUBLIN' => 'County Dublin',
		'CO CARLOW' => 'County Carlow',
		'CO KILDARE' => 'County Kildare',
		'CO KILKENNY' => 'County Kilkenny',
		'CO LAOIS' => 'County Laois',
		'CO LONGFORD' => 'County Longford',
		'CO LOUTH' => 'County Louth',
		'CO MEATH' => 'County Meath',
		'CO OFFALY' => 'County Offaly',
		'CO WESTMEATH' => 'County Westmeath',
		'CO WEXFORD' => 'County Wexford',
		'CO WICKLOW' => 'County Wicklow',
		'CO GALWAY' => 'County Galway',
		'CO MAYO' => 'County Mayo',
		'CO LEITRIM' => 'County Leitrim',
		'CO ROSCOMMON' => 'County Roscommon',
		'CO SLIGO' => 'County Sligo',
		'CO CLARE' => 'County Clare',
		'CO CORK' => 'County Cork',
		'CO KERRY' => 'County Kerry',
		'CO LIMERICK' => 'County Limerick',
		'CO TIPPERARY' => 'County Tipperary',
		'CO WATERFORD' => 'County Waterford'
	),
	
	'NL' => array(
	
		'DR' => 'Drente',
		'FL' => 'Flevoland',
		'FR' => 'Friesland',
		'GL' => 'Gelderland',
		'GR' => 'Groningen',
		'LB' => 'Limburg',
		'NB' => 'Noord Brabant',
		'NH' => 'Noord Holland',
		'OV' => 'Overijssel',
		'UT' => 'Utrecht',
		'ZH' => 'Zuid Holland',
		'ZL' => 'Zeeland'
	),
	
	'BR' => array(
	
		'AC' => 'Acre',
		'AL' => 'Alagoas',
		'AM' => 'Amazonas',
		'AP' => 'Amapa',
		'BA' => 'Baia',
		'CE' => 'Ceara',
		'DF' => 'Distrito Federal',
		'ES' => 'Espirito Santo',
		'FN' => 'Fernando de Noronha',
		'GO' => 'Goias',
		'MA' => 'Maranhao',
		'MG' => 'Minas Gerais',
		'MS' => 'Mato Grosso do Sul',
		'MT' => 'Mato Grosso',
		'PA' => 'Para',
		'PB' => 'Paraiba',
		'PE' => 'Pernambuco',
		'PI' => 'Piaui',
		'PR' => 'Parana',
		'RJ' => 'Rio de Janeiro',
		'RN' => 'Rio Grande do Norte',
		'RO' => 'Rondonia',
		'RR' => 'Roraima',
		'RS' => 'Rio Grande do Sul',
		'SC' => 'Santa Catarina',
		'SE' => 'Sergipe',
		'SP' => 'Sao Paulo',
		'TO' => 'Tocatins'

	)
	);
	
	public function getStateProvinceName($v) {
		foreach($this->stateProvinces as $countries) {
			foreach($countries as $key => $value) {
				if ($key == $v) {
					return $value;
				}
			}
		}
	}
	
	public function getStateProvinceArray($k) {
		return $this->stateProvinces[$k];
	}
	
	public function getAll() {
		return $this->stateProvinces;
	}
	
	/** 
	 * Returns an array of US states
	 * @deprecated
	 * @return array
	 */
	public function getStates() {
		return $this->stateProvinces['US'];
	}
	
	/** 
	 * Returns an array of Canadian provinces
	 * @deprecated
	 * @return array
	 */
	public function getCanadianProvinces() {
		return $this->stateProvinces['CA'];
	}
	

}

?>